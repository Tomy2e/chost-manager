<?php

class SouscriptionException extends Exception {

}

class Souscription {
    private $db, $idClient, $clientObj;

    const TVA = 20; // 20% de TVA

    public function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    public function setIdClient($id_client)
    {
        $this->idClient = $id_client;

        $this->clientObj = new Client;

        $this->clientObj->setIdClient($id_client);

        if(!$this->clientObj->fetchInfos())
        {
            throw new SouscriptionException("L'utilisateur n'existe pas");
        }
    }

    public function verifDispoSousdomaine($sousdomaine)
    {
        $sousdomaine = strtolower($sousdomaine);

        $reserves = array("admin", "www", "webmaster", "manager");

        if(!ctype_alnum($sousdomaine))
        {
            return false;
        }

        if(in_array($sousdomaine, $reserves)) 
        {
            return false;
        }

        $prep_verifDispo = $this->db->prepare("SELECT * FROM SOUSCRIPTION WHERE SOUSDOMAINE = ?");
        $prep_verifDispo->execute(array($sousdomaine));

        $verifDispo = $prep_verifDispo->fetch();

        if(!empty($verifDispo))
        {
            return false;
        }

        return true;
    }

    private function creerSouscription($id_offre, $sousdomaine)
    {
        $identifiantSouscription = uniqid("hs");
        $passwordSouscription = bin2hex(openssl_random_pseudo_bytes(16));

        if(!$this->verifDispoSousdomaine($sousdomaine))
        {
            throw new SouscriptionException("Le sous domaine n'est pas disponible");
        }

        $prep_ajout = $this->db->prepare("
        INSERT INTO SOUSCRIPTION (ID_CLIENT, ID_OFFRE, EXPIRE, IDENTIFIANT_SOUSCRIPTION, PASSWORD_SOUSCRIPTION, SOUSDOMAINE)
        VALUES (:id_client, :id_offre, :expire, :identifiant, :password, :ssdomaine)");

        if(!$prep_ajout->execute(array(
            'id_client' => $this->idClient,
            'id_offre' => $id_offre,
            'expire' => time() + 2628000,
            'identifiant' => $identifiantSouscription,
            'password' => $passwordSouscription,
            'ssdomaine' => $sousdomaine
        )))
        {
            throw new SouscriptionException("Impossible d'ajouter la souscription à la DB");
        }

        return array("identifiant" => $identifiantSouscription, "password" => $passwordSouscription);
    }

    public function ajouterSouscription($titre, $stockage, $prix, $sousdomaine)
    {
        if(empty($this->idClient))
        {
            throw new SouscriptionException("ID non initialisé");
        }

        if(!$this->verifDispoSousdomaine($sousdomaine))
        {
            throw new SouscriptionException("Le sous domaine n'est pas disponible");
        }

        // On vérifie que le compte est crédité 
        $prixTotal = round($prix * (1 + self::TVA / 100), 2);

        if($this->clientObj->getCredit(true) < $prixTotal)
        {
            throw new SouscriptionException("Le compte n'est pas sufisamment crédité pour effectuer cette souscription");
        }

        // Création de la facture
        $facture = new Facture;
        $facture->ajouterAchat($titre, $prix, $stockage, true);
        $factureSouscription = $facture->genererFacture($this->idClient, self::TVA);

        // Ajout de la souscription
        $souscriptionDb = $this->creerSouscription($factureSouscription['achats'][0]['id_achat'], $sousdomaine);

        // Création de l'hébergement (partie sysadmin auto)

        // Linux
        $linuxm = new LINUXmanager;
        $linuxm->addUser($souscriptionDb['identifiant']);

        // PHP
        $phpm = new PHPmanager;
        $phpm->ecrireConf($souscriptionDb['identifiant']);
        // NE PAS RELOAD MAINTENANT SINON CA CASSE LE SCRIPT
        
        // NGINX
        $nginxm = new NGINXmanager;
        $nginxm->ecrireConf($souscriptionDb['identifiant'], array(
            array("domaine"=>$sousdomaine . "." . USER_DOMAIN, "chemin" => "/")
        ));
        $nginxm->rechargerServeur();

        // MySQL
        $mysqlm = new MySQLmanager($this->db);
        $mysqlm->createUser($souscriptionDb['identifiant'], $souscriptionDb['password']);

        // FTP
        $ftpm = new FTPmanager;
        $ftpm->addUser($souscriptionDb['identifiant'], $souscriptionDb['password']);
        $ftpm->setQuota($souscriptionDb['identifiant'], intval($stockage));

        // Effectuer le paiement depuis le crédit de l'utilisateur
        $this->clientObj->deduireCredit($factureSouscription['total']);

        // Todo : envoyer un mail

        return $souscriptionDb;
    }
}
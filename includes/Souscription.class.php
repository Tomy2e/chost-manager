<?php

class SouscriptionException extends Exception {

}

/* Todo:
- supprimerSouscription()
- listerSouscriptions()
- infosSouscription()
- suspendreSouscription()
- renouvelerSouscription()
- changerMdp()
*/

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

        if(strlen($sousdomaine) > 30)
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
            'expire' => date('Y-m-d H:i:s', time() + 2628000),
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
        
        $facture->envoyerMail($this->clientObj, $factureSouscription);

        // Ajout de la souscription
        $souscriptionDb = $this->creerSouscription($factureSouscription['achats'][0]['id_achat'], $sousdomaine);

        // Création de l'hébergement (partie sysadmin auto)

        // Linux
        $linuxm = new LINUXmanager;
        $linuxm->addUser($souscriptionDb['identifiant']);

        // PHP
        $phpm = new PHPmanager;
        $phpm->ecrireConf($souscriptionDb['identifiant']);
        $phpm->rechargerServeur();
        
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

        // Effectuer le paiement depuis le crédit de l'utilisateur (sauf si l'abonnement est gratuit..)
        if($prix > 0)
        {
            $this->clientObj->deduireCredit($factureSouscription['total']);
        }
        
        // Todo : envoyer un mail
        MAILmanager::send($this->clientObj->getEmail(), "Votre hébergement cHost", "<html>
        <head>
         <title>Votre hébergement cHost</title>
        </head>
        <body>
        <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
        <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
        <hr>
        Bonjour ".$this->clientObj->getPrenom() . " " . $this->clientObj->getNom() . ",<br /><br />
        Nous sommes heureux de vous annoncer que votre hébergement est dès maintenant accessible à l'adresse suivante : <a href='http://$sousdomaine.".USER_DOMAIN."'>http://$sousdomaine.".USER_DOMAIN."</a>.<br /><br />
        Veuillez conserver précieusement les identifiants suivants qui vous permettront d'administrer votre site web :<br />
        Nom d'utilisateur : ".$souscriptionDb['identifiant']."<br />
        Mot de passe : ".$souscriptionDb['password']."<br /><br />
        Ces identifiants sont notamment valides aux endroits suivants : <br />
        Notre serveur FTP : ".SITE_FTP."<br />
        Notre base de données MySQL : ".SITE_SQLADMIN."<br /><br />
        Nos équipes restent bien entendu à votre dispositions via l'onglet support accessible sur votre espace client.<br />
        <br />
        Cordialement, l'équipe cHost.
        </center>
  
        </body>
       </html>", true);

        return $souscriptionDb;
    }

    public function listerSouscriptions()
    {
        if(empty($this->idClient))
        {
            throw new SouscriptionException("ID non initialisé");
        }

        $prep_list = $this->db->prepare("SELECT * FROM SOUSCRIPTION, OFFRES WHERE ID_CLIENT = ? AND SOUSCRIPTION.ID_OFFRE = OFFRES.ID_OFFRE");
        $prep_list->execute(array(
            $this->idClient
        ));

        return $prep_list->fetchAll(PDO::FETCH_ASSOC);
    }

    // On veut savoir si l'utilisateur n'a pas déjà un abonnement GRATUIT
    public function eligibleOffreEssai()
    {
        foreach($this->listerSouscriptions() as $souscription)
        {
            if($souscription['PRIX_OFFRE'] == 0 && $souscription['ESPACE_STOCKAGE'] > 0)
            {
                return false;
            }
        }
        
        return true;
    }

    public function infoSouscription($identifiantSouscription)
    {
        $prep_infos = $this->db->prepare("SELECT * FROM SOUSCRIPTION, CLIENTS, OFFRES WHERE IDENTIFIANT_SOUSCRIPTION = ? AND CLIENTS.ID_CLIENT = SOUSCRIPTION.ID_CLIENT AND OFFRES.ID_OFFRE = SOUSCRIPTION.ID_OFFRE");
        $prep_infos->execute(array($identifiantSouscription));
        $infos = $prep_infos->fetch(PDO::FETCH_ASSOC);

        $linuxm = new LINUXmanager;

        $infos['DIRSIZE'] = $linuxm->getDirSize($infos['IDENTIFIANT_SOUSCRIPTION']);
        $infos['POURCENTAGE_UTILISATION_DISQUE'] = intval((100 * $infos['DIRSIZE']) / $infos['ESPACE_STOCKAGE']);
        $infos['POURCENTAGE_UTILISATION_DISQUE'] = ($infos['POURCENTAGE_UTILISATION_DISQUE'] > 100) ? 100 : $infos['POURCENTAGE_UTILISATION_DISQUE'];

        return $infos;
    }
}
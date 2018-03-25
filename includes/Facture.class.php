<?php

class FactureException extends Exception {

}

class Facture
{
    private $achats = array();
    private $db;

    public function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    public function ajouterAchat($nom, $prix, $stockage, $isSouscription = false)
    {
        array_push($this->achats, array(
            "id_achat" => null,
            "nom" => $nom,
            "prix" => round(floatval($prix), 2),
            "stockage" => intval($stockage)
        ));
    }

    // Utile en cas de renouvellement
    public function ajouterOffre($id_offre)
    {
        $prep_getOffre = $this->db->prepare("SELECT * FROM OFFRES WHERE ID_OFFRE = ?");
        $prep_getOffre->execute(array(
            $id_offre
        ));

        $offre = $prep_getOffre->fetch();

        if(empty($offre))
        {
            throw new FactureException("L'offre demandée n'existe pas");
        }

        array_push($this->achats, array(
            "id_achat" => $offre['ID_OFFRE'],
            "nom" => $offre['NOM_OFFRE'],
            "prix" => $offre['PRIX_OFFRE'],
            "stockage" => $offre['ESPACE_STOCKAGE']
        ));
    }

    private function creerOffre($nom, $prix, $stockage)
    {
        $prep_creation = $this->db->prepare("INSERT INTO OFFRES(NOM_OFFRE, PRIX_OFFRE, ESPACE_STOCKAGE) VALUES (?,?,?)");
        if(!$prep_creation->execute(array(
            $nom,
            round(floatval($prix), 2),
            intval($stockage)
        )))
        {
            throw new FactureException("Impossible de créer l'offre dans la DB");
        }

        return $this->db->lastInsertId();
    }

    public function genererFacture($id_client, $tva = 0)
    {
        $total = 0;

        // On crée les offres qui n'existent pas
        // On récupère le total de la facture
        foreach($this->achats as $key=>$achat)
        {
            if(is_null($achat['id_achat']))
            {
                $this->achats[$key]['id_achat'] = $this->creerOffre($achat['nom'], $achat['prix'], $achat['stockage']);
            }

            $total += $achat['prix'];
        }

        // On calcule le total avec TVA incluse
        $coefficient = 1 + ($tva/100);
        $total = round($total * $coefficient, 2);

        // Création de la facture
        $id_facture = $this->creerFacture($id_client, $total, $tva);

        // On ajoute les achats effectués
        foreach($this->achats as $achat)
        {
            $prep_insertion = $this->db->prepare("INSERT INTO ACHATS (ID_OFFRE, ID_FACTURE) VALUES(?,?)");
            if(!$prep_insertion->execute(array(
                $achat['id_achat'],
                $id_facture
            )))
            {
                throw new FactureException("Un achat n'a pas pu être ajouté à la base");
            }
        }

        return array("achats" => $this->achats, "total" => $total, "id_facture" => $id_facture);
    }

    private function creerFacture($id_client, $totalAvecTva, $tva)
    {

        $prep_creation = $this->db->prepare("INSERT INTO FACTURES(ID_CLIENT, DATE_FACTURE, TOTAL_FACTURE, TVA) VALUES (?,?,?,?)");
        if(!$prep_creation->execute(array(
            $id_client,
            date('Y-m-d H:i:s'),
            round(floatval($totalAvecTva), 2),
            $tva
        )))
        {
            throw new FactureException("Erreur lors de la création de la facture!");
        }

        return $this->db->lastInsertId();
    }

    public function listerFactures($id_client)
    {
        $prep_listing = $this->db->prepare("SELECT * FROM FACTURES WHERE ID_CLIENT = ?");
        $prep_listing->execute(array($id_client));

        $factures = $prep_listing->fetchAll(PDO::FETCH_ASSOC);

        foreach($factures as $key=>$facture)
        {
            // lister les achats
            $prep_achats = $this->db->prepare("SELECT * FROM OFFRES, ACHATS WHERE ID_FACTURE = ? AND OFFRES.ID_OFFRE = ACHATS.ID_OFFRE");
            $prep_achats->execute(array($facture['ID_FACTURE']));

            $factures[$key]['achats'] = $prep_achats->fetchAll(PDO::FETCH_ASSOC);
        }

        return $factures;
    }

    public function infosFacture($id_facture)
    {
        $prep_facture = $this->db->prepare("SELECT * FROM FACTURES WHERE ID_FACTURE = ?");
        $prep_facture->execute(array($id_facture));

        $facture = $prep_facture->fetch(PDO::FETCH_ASSOC);

        if(empty($facture))
        {
            throw new FactureException("La facture n'existe pas");            
        }

        // On récupère les achats
        $prep_achats = $this->db->prepare("SELECT * FROM OFFRES, ACHATS WHERE ID_FACTURE = ? AND OFFRES.ID_OFFRE = ACHATS.ID_OFFRE");
        $prep_achats->execute(array($facture['ID_FACTURE']));

        $facture['achats'] = $prep_achats->fetchAll(PDO::FETCH_ASSOC);

        // On récupère les infos concernant l'utilisateur qui a créé la facture
        $prep_client = $this->db->prepare("SELECT * FROM CLIENTS WHERE ID_CLIENT = ?");
        $prep_client->execute(array($facture['ID_CLIENT']));

        $facture['client'] = $prep_client->fetch(PDO::FETCH_ASSOC);

        return $facture;
    }

    public function envoyerMail($userObj, $factureGeneree)
    {
        $body = "<html>
        <head>
         <title>Votre facture cHost</title>
        </head>
        <body>
        <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
        <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
        <hr>
        Bonjour ".$userObj->getPrenom() . " " . $userObj->getNom() . ",<br /><br />
        Vous avez passé commande chez nous et nous vous en remercions.<br />
        Votre facture N°".$factureGeneree['id_facture']." de ".$factureGeneree['total']."€ est disponible ici : <a href='".SITE_URL."facture-detaillee.php?id=".$factureGeneree['id_facture']."'>".SITE_URL."facture-detaillee.php?id=".$factureGeneree['id_facture']."</a>. <br />
        <br />
        Cordialement, l'équipe cHost.
        </center>
  
        </body>
       </html>";
       
       return MAILmanager::send($userObj->getEmail(), "Votre facture cHost", $body, true);
    }
}
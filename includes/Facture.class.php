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
        $prep_getOffre = $this->db->prepare("SELECT * FROM offres WHERE ID_OFFRE = ?");
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
            "stockage" => $offre['STOCKAGE']
        ));
    }

    private function creerOffre($nom, $prix, $stockage)
    {
        $prep_creation = $this->db->prepare("INSERT INTO offres(NOM_OFFRE, PRIX_OFFRE, ESPACE_STOCKAGE) VALUES (?,?,?)");
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

    public function genererFacture($id_client)
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

        // Création de la facture
        $id_facture = $this->creerFacture($id_client, $total);

        // On ajoute les achats effectués
        foreach($this->achats as $achat)
        {
            $prep_insertion = $this->db->prepare("INSERT INTO achats (ID_OFFRE, ID_FACTURE) VALUES(?,?)");
            if(!$prep_insertion->execute(array(
                $achat['id_achat'],
                $id_facture
            )))
            {
                throw new FactureException("Un achat n'a pas pu être ajouté à la base");
            }
        }

        return $this->achats;
    }

    private function creerFacture($id_client, $total)
    {
        $prep_creation = $this->db->prepare("INSERT INTO factures(ID_CLIENT, DATE_FACTURE, TOTAL_FACTURE) VALUES (?,?,?)");
        if(!$prep_creation->execute(array(
            $id_client,
            date('Y-m-d H:i:s'),
            round(floatval($total), 2)
        )))
        {
            throw new FactureException("Erreur lors de la création de la facture!");
        }

        return $this->db->lastInsertId();
    }

    public function listerFactures($id_client)
    {
        $prep_listing = $this->db->prepare("SELECT * FROM factures WHERE ID_CLIENT = ?");
        $prep_listing->execute(array($id_client));

        $factures = $prep_listing->fetchAll(PDO::FETCH_ASSOC);

        foreach($factures as $key=>$facture)
        {
            // lister les achats
            $prep_achats = $this->db->prepare("SELECT * FROM offres, achats WHERE ID_FACTURE = ? AND offres.ID_OFFRE = achats.ID_OFFRE");
            $prep_achats->execute(array($facture['ID_FACTURE']));

            $factures[$key]['achats'] = $prep_achats->fetchAll(PDO::FETCH_ASSOC);
        }

        return $factures;
    }

    public function infosFacture($id_facture)
    {
        $prep_facture = $this->db->prepare("SELECT * FROM factures WHERE ID_FACTURE = ?");
        $prep_facture->execute(array($id_facture));

        $facture = $prep_facture->fetch(PDO::FETCH_ASSOC);

        if(empty($facture))
        {
            throw new FactureException("La facture n'existe pas");            
        }

        // On récupère les achats
        $prep_achats = $this->db->prepare("SELECT * FROM offres, achats WHERE ID_FACTURE = ? AND offres.ID_OFFRE = achats.ID_OFFRE");
        $prep_achats->execute(array($facture['ID_FACTURE']));

        $facture['achats'] = $prep_achats->fetchAll(PDO::FETCH_ASSOC);

        // On récupère les infos concernant l'utilisateur qui a créé la facture
        $prep_client = $this->db->prepare("SELECT * FROM clients WHERE ID_CLIENT = ?");
        $prep_client->execute(array($facture['ID_CLIENT']));

        $facture['client'] = $prep_client->fetch(PDO::FETCH_ASSOC);

        return $facture;
    }
}
<?php

class ClientException extends Exception {

}

class Client
{
    private $id_client;
    private $db;
    private $prenom, $nom, $email, $password, $adresse, $codepostal, $ville, $telephone, $credit;

    public function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    public function setIdClient($id_client)
    {
        $this->id_client = $id_client;
    }

    public function fetchInfos()
    {
        if(empty($this->id_client))
        {
            throw new ClientException("La classe n'est pas initialisée avec un Id");
        }

        $prep_fetchInfos = $this->db->prepare("SELECT * FROM clients WHERE id_client = ?");
        $prep_fetchInfos->execute(array(
            $this->id_client
        ));

        $infos = $prep_fetchInfos->fetch();

        if(!empty($infos)) 
        {
            $this->prenom = $infos['PRENOM'];
            $this->nom = $infos['NOM'];
            $this->email = $infos['EMAIL'];
            $this->password = $infos['PASSWORD'];
            $this->adresse = $infos['ADRESSE'];
            $this->codepostal = $infos['CODEPOSTAL'];
            $this->ville = $infos['VILLE'];
            $this->telephone = $infos['TELEPHONE'];
            $this->credit = $infos['CREDIT'];

            return true;
        }
        else
        {
            return false;
        }
    }

    public function ajouterCredit($somme)
    {
        if(empty($this->id_client))
        {
            throw new ClientException("La classe n'est pas initialisée avec un Id");
        }

        $somme = intval($somme);

        if($somme <= 0)
        {
            throw new ClientException("La somme à ajouter doit être supérieure ou égale à 0");
        }

        $addCredit = $this->db->prepare("UPDATE clients SET credit = credit + :ajout WHERE id_client = :id_client");
        $addCredit->bindParam(':ajout', $somme, PDO::PARAM_INT);
        $addCredit->bindParam(':id_client', $this->id_client, PDO::PARAM_INT);
        
        return $addCredit->execute();
    }

    public function deduireCredit($somme)
    {
        if(empty($this->id_client))
        {
            throw new ClientException("La classe n'est pas initialisée avec un Id");
        }

        $somme = intval($somme);

        if($somme <= 0)
        {
            throw new ClientException("La somme à déduire doit être supérieure ou égale à 0");
        }



        // On vérifie que le crédit est suffisant
        if(($this->getCredit() - $somme) >= 0)
        {
            $deduireCredit = $this->db->prepare("UPDATE clients SET credit = credit - :supp WHERE id_client = :id_client");
            $deduireCredit->bindParam(':supp', $somme, PDO::PARAM_INT);
            $deduireCredit->bindParam(':id_client', $this->id_client, PDO::PARAM_INT);
    
            return $deduireCredit->execute();
        }
        else
        {
            return false;
        }
    }

    public function getCredit($update = false)
    {
        if(empty($this->id_client))
        {
            throw new ClientException("La classe n'est pas initialisée avec un Id");
        }

        if($update)
        {
            if(!$this->fetchInfos())
            {
                throw new ClientException("Impossible de mettre à jour les données");
            }
        }

        return $this->credit;
    }
}
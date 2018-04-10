<?php

class TicketException extends Exception {

}

class Ticket {

    private $id_client;
    private $db;


    public function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    public function getTickets($id_client)
    {
        $prep_fetch = $this->db->prepare("SELECT * FROM TICKETS where ID_CLIENT = ?");
        $prep_fetch->execute(array(
            $id_client

        ));

        $fetched = $prep_fetch->fetchAll(PDO::FETCH_ASSOC);

        return $fetched;
        //print_r($fetched);
    }
    public function getAllTickets()
    {
        $prep_fetch = $this->db->prepare("SELECT * FROM TICKETS where LOCK_TICKET = 0");
        $prep_fetch->execute();

        $fetched = $prep_fetch->fetchAll(PDO::FETCH_ASSOC);

        return $fetched;
        //print_r($fetched);
    }
    public function getTicket($id_ticket)
    {
        $prep_fetch = $this->db->prepare("SELECT * FROM TICKETS where ID_TICKET = ?");
        $prep_fetch->execute(array(
            $id_ticket

        ));

        $fetched = $prep_fetch->fetchAll(PDO::FETCH_ASSOC);

        return $fetched;
        //print_r($fetched);
    }

    public function addTicket($id_client,$server_affecte,$type,$message,$prenom)
    {
        $insertion = $this->db->prepare("INSERT INTO TICKETS (TYPE_PROBLEME,ID_CLIENT,LOCK_TICKET) 
        VALUES (:type, :client, :lock)");
        $insertion->execute(array(
            'type' => htmlspecialchars($type),
            'client' => $id_client,
            'lock' => 0
        ));
    

        $id_ticket = $this->db->lastInsertId();
        $this->addMessage($message,$prenom,$id_ticket);

        return $id_ticket;
    }

    public function closeTicket($id_ticket)
    {
        $change = $this->db->prepare("UPDATE `TICKETS` SET `LOCK_TICKET` = '1' WHERE `TICKETS`.`ID_TICKET` = :id_ticket");
        $change->execute(array(
        'id_ticket' => $id_ticket
    ));
    




    }

    public function addMessage($message,$prenom,$id_ticket)
    {
        print_r($id_ticket);
        $insertion = $this->db->prepare("INSERT INTO MESSAGES (MESSAGE_TICKET,DATE_MESSAGE,PRENOM_AUTEUR,ID_TICKET) 
        VALUES (:message, :date, :prenom, :ticket)");
        $insertion->execute(array(
            'message' => htmlspecialchars($message),
            'date' => date('Y-m-d H:i:s'),
            'prenom' => htmlspecialchars($prenom),
            'ticket' => $id_ticket
        ));
    }

    public function getMessage($id_ticket)
    {
        $prep_fetch = $this->db->prepare("SELECT * FROM MESSAGES where ID_TICKET = ?");
        $prep_fetch->execute(array(
            $id_ticket

        ));

        $fetched = $prep_fetch->fetchAll(PDO::FETCH_ASSOC);

        return $fetched;
        //print_r($fetched);
    }

    public function supprimerTickets($id_client)
    {
        // Pour tous les tickets
        foreach($this->getTickets($id_client) as $ticket)
        {
            // on supprime tous les messages associés au ticket
            $prep_supprMsg = $this->db->prepare("DELETE FROM MESSAGES WHERE ID_TICKET = ?");
            if($prep_supprMsg->execute(array($ticket['ID_TICKET'])) === false)
            {
                throw new TicketException("Une erreur SQL s'est produite lors de la tentative de suppression des messages associés à un ticket");
            }
        }

        // On supprime maintenant tous les tickets
        $prep_supprTickets = $this->db->prepare("DELETE FROM TICKETS WHERE ID_CLIENT = ?");

        if($prep_supprTickets->execute(array($id_client)) === false)
        {
            throw new TicketException("Une erreur SQL s'est produite lors de la suppression des tickets d'un client");
        }
    }

}


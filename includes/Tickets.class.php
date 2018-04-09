<?php

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

    public function addTicket($clientObj,$server_affecte,$type,$message)
    {
        $insertion = $this->db->prepare("INSERT INTO TICKETS (TYPE_PROBLEME,ID_CLIENT,LOCK_TICKET) 
        VALUES (:type, :client, :lock)");
        $insertion->execute(array(
            'type' => htmlspecialchars($type),
            'client' => $clientObj->getId(),
            'lock' => 0
        ));
    

        $id_ticket = $this->db->lastInsertId();
        $this->addMessage($message,$clientObj->getPrenom(),$id_ticket);
        $this->envoyerMail($clientObj,$id_ticket);

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


public function envoyerMail($userObj,$id_ticket)
    {
        $body = "<html>
        <head>
         <title>Votre ticket a bien été crée</title>
        </head>
        <body>
        <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
        <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
        <hr>
        Bonjour ".$userObj->getPrenom(). " " . $userObj->getNom() . ",<br /><br />
        Vous avez crée un ticket et notre équipe vous répondra sous peu.<br />
        Votre ticket a bien étais crée et est disponible ici : <a href='".SITE_URL."ticket.php?ticket=".$id_ticket."'>".SITE_URL."ticket.php?ticket=".$id_ticket."</a>. <br />
        <br />
        Cordialement, l'équipe cHost.
        </center>
  
        </body>
       </html>";
       
       return MAILmanager::send($userObj->getEmail(), "Votre facture cHost", $body, true);
    }
}


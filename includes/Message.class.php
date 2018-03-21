<?php

class Message {

    private $id_client;
    private $db;


    public function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    public function getMessage($id_ticket)
    {
        $prep_fetch = $this->db->prepare("SELECT * FROM tickets where id_ticket = ?");
        $prep_fetch->execute(array(
            $id_ticket

        ));

        $fetched = $prep_fetch->fetchAll(PDO::FETCH_ASSOC);

        return $fetched;
        //print_r($fetched);
    }

    public function addTicket($id_client,$server_affecte,$type,$message,$prenom)
    {
        $insertion = $this->db->prepare("INSERT INTO tickets (TYPE_PROBLEME,ID_CLIENT,LOCK_TICKET) 
VALUES (:type, :client, :lock)");
$insertion->execute(array(
    'type' => $type,
    'client' => $id_client,
    'lock' => 0
));
    

$id_ticket = $this->db->lastInsertId();
$this->addMessage($message,$prenom,$id_ticket);


    }

    $id_ticket = $this->db->lastInsertId();
$this->addMessage($message,$prenom,$id_ticket);


    public function addMessage($message,$prenom,$id_ticket)
    {
        $insertion = $this->db->prepare("INSERT INTO messages (MESSAGE_TICKET,DATE_MESSAGE,PRENOM_AUTEUR,ID_TICKET) 
        VALUES (:message, :date, :prenom, :ticket)");
        $insertion->execute(array(
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
            'prenom' => $prenom,
            'ticket' => $id_ticket
        ));
    }

}


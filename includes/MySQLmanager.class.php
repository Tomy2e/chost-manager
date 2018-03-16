<?php

class MySQLmanagerException extends Exception {

}

/* Attention :

Les clients doivent être en localhost (hardcodé)

*/

class MySQLmanager {
    // La connexion à la base
    private $pdoHandle;

    /* Constructeur 
    Une connexion MySQL doit déjà être active avec la classe PDO
    */
    function __construct($pdoHandle)
    {
        // Vérifier le type de la variable
        if(!is_a($pdoHandle, 'PDO'))
        {
            throw new MySQLmanagerException('La variable $pdoHandle doit être de type PDO');
        }

        // Vérifier que la connexion est active
        try {
            $this->pdoHandle->query("SELECT 1");
        } catch (PDOException $e) {
            throw new MySQLmanagerException("La connexion à la base de données a échouée");
        }
    }

    function createUser($username, $password)
    {

        if(!ctype_alnum($username)) 
        {
            throw new MySQLmanagerException("L'identifiant doit être alpha numérique !");
        }

        // Création du compte MySQL sans privilèges
        // commande : CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
        $addUser = $this->pdoHandle->prepare("CREATE USER ?@'localhost' IDENTIFIED BY ?;");
        if($addUser->execute(array(
            $username,
            $password
        )) === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de la création de l'utilisateur.");
        }

        // Création de la base de données par défaut
        $this->addDatabase($username, $username);

        return true;
    }

    function addDatabase($username, $dbName)
    {
        // Création de la base de données pour cet utilisateur
        $addDatabase = $this->pdoHandle->prepare("CREATE DATABASE ?");
        if($addDatabase->execute(array(
            $dbName
        )) === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de la création de la base de données pour l'utilisateur.");
        }

        // Ajout des droits à cet utilisateur
        // Commande : GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON `chost`.* TO 'testuser'@'%';
        $addPrivileges = $this->pdoHandle->prepare("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON ?.* TO ?@'%';");
        if($addPrivileges->execute(array(
            $username,
            $dbName
        )) === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de l'ajout des droits pour l'utilisateur.");
        }
    }

    function updatePassword($username, $password)
    {
        $updatePassword = $this->pdoHandle->prepare("ALTER USER ?@'localhost' IDENTIFIED BY ?;");
        if($updatePassword->execute(array(
            $username,
            $password
        )) === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de la modification du mot de passe");
        }

        return true;
    }

    function getUserDbSize($username)
    {
        /*
        TODO :
        SELECT table_schema                                        "DB Name", 
        Round(Sum(data_length + index_length) / 1024 / 1024, 1) "DB Size in MB" 
        FROM   information_schema.tables 
        GROUP  BY table_schema; 
        */
    }

    function deleteDatabase($dbName)
    {
        $delDb = $this->pdoHandle->prepare("DROP DATABASE ?;");
        if($delDb->execute(array(
            $dbName
        )) === false)
        {
            throw new Exception("Une erreur s'est produite lors de la suppression de la base de données");
        }

        return true;
    }

    /* Supprimer un utilisateur */
    function deleteUser($username)
    {
        $delUser = $this->pdoHandle->prepare("DROP USER ?@'localhost';");
        if($delUser->execute(array(
            $username
        )) === false)
        {
            throw new Exception("Une erreur s'est produite lors de la suppression de l'utilisateur");
        }

        return true;
    }
}
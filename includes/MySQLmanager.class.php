<?php

class MySQLmanagerException extends Exception {

}

/* Attention :

Les clients doivent être en localhost (hardcodé)
COMPTE ROOT OBLIGATOIRE!!

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

        $this->pdoHandle = $pdoHandle;

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
        
        $this->pdoHandle->query("flush privileges;");

        // Création de la base de données par défaut
        $this->addDatabase($username, $username);

        return true;
    }

    function addDatabase($username, $dbName)
    {

        if(!ctype_alnum($dbName)) 
        {
            throw new MySQLmanagerException("Le nom de la base doit être alpha numérique !");
        }

        // Création de la base de données pour cet utilisateur
        // https://stackoverflow.com/a/19986035
        
        $addDatabase = $this->pdoHandle->prepare("CREATE DATABASE $dbName");
        if($addDatabase->execute() === false)
        {
            print_r($this->pdoHandle->errorInfo());
            throw new MySQLmanagerException("Une erreur s'est produite lors de la création de la base de données pour l'utilisateur.");
        }

        // Ajout des droits à cet utilisateur
        // Commande : GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON `chost`.* TO 'testuser'@'%';
        $addPrivileges = $this->pdoHandle->prepare("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, CREATE VIEW, EVENT, TRIGGER, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EXECUTE ON `$username`.* TO '$dbName'@'localhost';");
        if($addPrivileges->execute() === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de l'ajout des droits pour l'utilisateur.");
        }

        $this->pdoHandle->query("flush privileges;");
    }

    function updatePassword($username, $password)
    {
        if(!ctype_alnum($username)) 
        {
            throw new MySQLmanagerException("L'identifiant doit être alpha numérique !");
        }

        $updatePassword = $this->pdoHandle->prepare("ALTER USER $username@'localhost' IDENTIFIED BY ?;");
        if($updatePassword->execute(array(
            $password
        )) === false)
        {
            throw new MySQLmanagerException("Une erreur s'est produite lors de la modification du mot de passe");
        }

        $this->pdoHandle->query("flush privileges;");

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
        if(!ctype_alnum($dbName)) 
        {
            throw new MySQLmanagerException("Le nom de la base doit être alpha numérique !");
        }
        $delDb = $this->pdoHandle->prepare("DROP DATABASE $dbName;");
        if($delDb->execute() === false)
        {
            throw new Exception("Une erreur s'est produite lors de la suppression de la base de données");
        }

        return true;
    }

    /* Supprimer un utilisateur */
    function deleteUser($username)
    {
        if(!ctype_alnum($username)) 
        {
            throw new MySQLmanagerException("L'identifiant doit être alpha numérique !");
        }

        $delUser = $this->pdoHandle->prepare("DROP USER $username@'localhost';");
        if($delUser->execute() === false)
        {
            throw new Exception("Une erreur s'est produite lors de la suppression de l'utilisateur");
        }

        $this->pdoHandle->query("flush privileges;");

        return true;
    }
}
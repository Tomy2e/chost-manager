<?php

class DBmanager
{
    public static $instance;

    static function getInstance()
    {
        if(empty(self::$instance))
        {
            try {
                self::$instance = new PDO('mysql:host=localhost;dbname='.BDD_NAME, BDD_USERNAME, BDD_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
                //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        return self::$instance;
    }
}
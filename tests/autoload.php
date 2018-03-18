<?php


require_once('../includes/config.php');

include('../includes/LINUXmanager.class.php');
include('../includes/MySQLmanager.class.php');
include('../includes/PHPmanager.class.php');
include('../includes/NGINXmanager.class.php');
include('../includes/FTPmanager.class.php');

try {
    $dbh = new PDO('mysql:host=localhost;dbname='.BDD_NAME, BDD_USERNAME, BDD_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    //$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
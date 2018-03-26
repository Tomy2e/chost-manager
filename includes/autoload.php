<?php
session_start();

require_once('includes/config.php');

require_once('includes/DBmanager.class.php');
require_once('includes/Client.class.php');
require_once('includes/CodesActivation.class.php');
require_once('includes/Facture.class.php');
require_once('includes/Souscription.class.php');
require_once('includes/MAILmanager.class.php');
require_once('includes/Tickets.class.php');

require_once('includes/LINUXmanager.class.php');
require_once('includes/FTPmanager.class.php');
require_once('includes/MySQLmanager.class.php');
require_once('includes/NGINXmanager.class.php');
require_once('includes/PHPmanager.class.php');

if(PAYPAL_ENABLE == 'YES')
{
    require 'vendor/autoload.php';
    require_once('includes/EasyPayPal.class.php');
}


function isConnected()
{
    if(!empty($_SESSION['id_client']) && $_SESSION['id_client'] != 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function deconnexion()
{
    $_SESSION['id_client'] = 0;
}

function connexion($id_client)
{
    $_SESSION['id_client'] = intval($id_client);
}

function is_cli()
{
    if ( defined('STDIN') )
    {
        return true;
    }

    if ( php_sapi_name() === 'cli' )
    {
        return true;
    }

    if ( array_key_exists('SHELL', $_ENV) ) {
        return true;
    }

    if ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) 
    {
        return true;
    } 

    if ( !array_key_exists('REQUEST_METHOD', $_SERVER) )
    {
        return true;
    }

    return false;
}

if(isConnected())
{
    $clientObj = new Client;
    $clientObj->setIdClient($_SESSION['id_client']);
    if(!$clientObj->fetchInfos())
    {
        deconnexion();
        header("Location: connexion.php");
        exit();
    }

    $souscriptionObj = new Souscription;
    $souscriptionObj->setIdClient($_SESSION['id_client']);
}
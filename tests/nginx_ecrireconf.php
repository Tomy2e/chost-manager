<?php

require_once('autoload.php');

$nginxm = new NGINXmanager;

$nginxm->ecrireConf('user1', array(
    array("domaine" =>"monsupersite.local", "chemin" =>"/"),
    array("domaine"=> "sousdomaine.monsupersite.local", "chemin"=>"/sousdomaine")
));
<?php

require_once('autoload.php');

$mysqlm = new MySQLmanager($dbh);

$mysqlm->deleteDatabase('user1');
$mysqlm->deleteUser('user1');
<?php

require_once('autoload.php');

$mysqlm = new MySQLmanager($dbh);

$mysqlm->createUser('user1', 'password');
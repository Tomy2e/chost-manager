<?php

require_once('autoload.php');

$sftp = new SSHmanager;


$sftp->ecrireConf('user1');

echo "test create";
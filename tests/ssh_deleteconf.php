<?php

require_once('autoload.php');

$sftp = new SSHmanager;


$sftp->supprimerConf('user1');

echo "test delete";
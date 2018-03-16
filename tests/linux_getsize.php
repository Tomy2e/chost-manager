<?php

require_once('autoload.php');

$linuxm = new LINUXmanager;

echo $linuxm->getDirSize('user1');
<?php

require_once('autoload.php');

$ftpm = new FTPmanager;

$ftpm->setQuota('user1', 50);
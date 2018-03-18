# chost-manager

## Packages requis
> nginx php7.0-fpm mysql-server mysql-client php7.0-mysql pure-ftpd

> git

## Configuration PHP7-FPM
$ sudo nano /etc/php/7.0/fpm/pool.d/www.conf  
> user = root  
> group = root  


**Par défaut et pour des raisons de sécurité, php7.0-fpm refusera de démarrer un pool en tant que root. La manipulation à effectuer pour contourner cela sous Ubuntu est la suivante: https://serverfault.com/a/789039**

## Configuration Nginx
TODO

## Configuration Pure-FTPd
Commandes à exécuter après l'installation:
> sudo ln -s /etc/pure-ftpd/conf/PureDB /etc/pure-ftpd/auth/50pure  
> sudo service pure-ftpd restart

Infos complémentaires: https://doc.ubuntu-fr.org/pure-ftp

## Ordre d'exécution des tests
### Création
- ajout de l'user linux_adduser.php
- écrire la conf php php_ecrireconf.php
- reload la conf php php_reload.php
- écrire la conf nginx nginx_ecrireconf.php
- reload la conf nginx nginx_reload.php
- ajout de l'user et de la db mysql_adduser.php
- ajout du compte ftp ftp_adduser.php

### Suppression
- faire l'inverse de la création

## Todo
- Bloquer les fonctions PHP sensibles (ini_set, shell_exec, ...)
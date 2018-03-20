<?php

/*
    * Identifiants SQL
    Compte ROOT obligatoire !! (Permission de création d'utilisateurs nécessaire)
*/
define('BDD_HOST', 'localhost');
define('BDD_NAME', 'chost');
define('BDD_USERNAME', 'root');
define('BDD_PASSWORD', '');

/*
    * Environnement
    Permet de limiter l'affichage des erreurs PHP
    - Valeurs possibles: DEV, PROD
*/
define('ENVIRONMENT', 'DEV'); 

/* 
    * Chemin vers la config des sites nginx
    Possibilité d'utiliser un chemin fictif
    Slash final obligatoire!
*/
define('NGINX_PATH', '/etc/nginx/sites-enabled/');

/*
    * Commande pour recharger la configuration nginx
    Laisser vide pour ne pas relancer la configuration
    PHP doit avoir les droits nécessaires !
*/
define('NGINX_RELOAD', 'service nginx reload');

/* 
    * Chemin vers la config des pools PHP-FPM
    Possibilité d'utiliser un chemin fictif
    Slash final obligatoire!
*/
define('PHP_PATH', '/etc/php5/fpm/pool.d/');

/*
    * Chemin vers les sockets PHP
    Possibilité d'utiliser un chemin fictif
    Slash final obligatoire!
*/
define('PHP_SOCKET_PATH', '/run/php/');

/*
    * Commande pour recharger la configuration PHP-FPM
    Laisser vide pour ne pas relancer la configuration
    PHP doit avoir les droits nécessaires !

*/
define('PHP_RELOAD', 'service php5-fpm reload');

/*
    * Commande pour ajouter un utilisateur Unix
    Laisser vide pour ne pas ajouter un utilisateur
    PHP doit avoir les droits nécessaires (root) !
    - arguments : %username%, %homedir%
*/
define('UNIX_USERADD', "useradd %username% -s /bin/false -p '*' -d %homedir%");

/*
    * Commande pour supprimer un utilisateur Unix
    Laisser vide pour ne pas supprimer les utilisateurs
    - arguments : %username%
*/
define('UNIX_USERDEL', 'userdel --remove %username%');

/*
    * Répertoire contenant les données utilisateur
    Slash final obligatoire!
*/
define('USER_PATH', '/home/');

/*
    * Domaine utilisé pour héberger les sites hébergés
    L'entrée *.domain.tld doit pointer vers ce serveur
*/
define('USER_DOMAIN', 'chost.io');

/* 
    * Commande pour ajouter un utilisateur FTP
    Laisser vide pour ne pas ajouter un utilisateur FTP
    - arguments : %username%, %homedir%, %password%
*/
define('FTP_USERADD', '(echo %password%; echo %password%) | pure-pw useradd %username% -u %username% -g %username% -d %homedir%');

/*
    * Commande pour modifier le mot de passe d'un utilisateur FTP
    Laisser vide pour ne pas autoriser la modification d'un mot de passe
    - arguments : %username%, %password%
*/
define('FTP_PASSWD', '(echo %password%; echo %password%) | pure-pw passwd %username%');

/*
    * Commande pour supprimer un utilisateur FTP
    Laisser vide pour ne pas autoriser la suppression d'un utilisateur FTP
    - arguments : %username%
*/
define('FTP_USERDEL', 'pure-pw userdel %username%');

/* 
    * Commande pour mettre à jour la DB FTP
    Laisser vide pour ne pas mettre à jour la DB FTP automatiquement
*/
define('FTP_MKDB', 'pure-pw mkdb');

/*
    * Commande pour définir la taille maximale que peut utiliser un utilisateur FTP (quotas)
    Laisser vide pour ne pas appliquer de quotas.
    Doc : https://download.pureftpd.org/pub/pure-ftpd/doc/README
    Utilisation requise de pure-quotacheck ?? https://linux.die.net/man/8/pure-quotacheck
    Source : https://download.pureftpd.org/pub/pure-ftpd/doc/README.Virtual-Users
    - arguments : %username%, %sizeInMB%
*/
define('FTP_QUOTAS', 'pure-pw usermod %username% -N %sizeInMB%');

/*
    * Activer ou pas le paiement par PayPal
    Valeurs: YES, NO
*/
define('PAYPAL_ENABLE', 'NO');

/*
    * Identifiants Application PayPal
*/
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_CLIENT_SECRET', '');
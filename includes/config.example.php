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
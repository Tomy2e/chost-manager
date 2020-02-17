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
define('NGINX_PATH', 'sys/');

/*
    * Commande pour recharger la configuration nginx
    Laisser vide pour ne pas relancer la configuration
    PHP doit avoir les droits nécessaires !
*/
define('NGINX_RELOAD', '');

/* 
    * Chemin vers la config des pools PHP-FPM
    Possibilité d'utiliser un chemin fictif
    Slash final obligatoire!
*/
define('PHP_PATH', 'sys/');

/*
    * Chemin vers les sockets PHP
    Possibilité d'utiliser un chemin fictif
    Slash final obligatoire!
*/
define('PHP_SOCKET_PATH', 'sys/');

/*
    * Commande pour recharger la configuration PHP-FPM
    Laisser vide pour ne pas relancer la configuration
    PHP doit avoir les droits nécessaires !

*/
define('PHP_RELOAD', '');

/*
    * Commande pour ajouter un utilisateur Unix
    Laisser vide pour ne pas ajouter un utilisateur
    PHP doit avoir les droits nécessaires (root) !
    - arguments : %username%, %homedir%
*/
define('UNIX_USERADD', "");

/*
    * Commande pour supprimer un utilisateur Unix
    Laisser vide pour ne pas supprimer les utilisateurs
    - arguments : %username%
*/
define('UNIX_USERDEL', '');

/*
    * Répertoire contenant les données utilisateur
    Slash final obligatoire!
*/
define('USER_PATH', 'sys/');

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
define('FTP_USERADD', '');

/*
    * Commande pour modifier le mot de passe d'un utilisateur FTP
    Laisser vide pour ne pas autoriser la modification d'un mot de passe
    - arguments : %username%, %password%
*/
define('FTP_PASSWD', '');

/*
    * Commande pour supprimer un utilisateur FTP
    Laisser vide pour ne pas autoriser la suppression d'un utilisateur FTP
    - arguments : %username%
*/
define('FTP_USERDEL', '');

/* 
    * Commande pour mettre à jour la DB FTP
    Laisser vide pour ne pas mettre à jour la DB FTP automatiquement
*/
define('FTP_MKDB', '');

/*
    * Commande pour définir la taille maximale que peut utiliser un utilisateur FTP (quotas)
    Laisser vide pour ne pas appliquer de quotas.
    Doc : https://download.pureftpd.org/pub/pure-ftpd/doc/README
    Utilisation requise de pure-quotacheck ?? https://linux.die.net/man/8/pure-quotacheck
    Source : https://download.pureftpd.org/pub/pure-ftpd/doc/README.Virtual-Users
    - arguments : %username%, %sizeInMB%
*/
define('FTP_QUOTAS', '');

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

/*
    * Adresse email expéditeur
*/
define('EMAIL_FROM', 'noreply@chost.com');

/* 
    * URL pointant vers le manager
*/
define('SITE_URL', 'http://localhost/chost/');

/*
    * URL pointant vers phpMyAdmin
*/
define('SITE_SQLADMIN', 'http://localhost/phpmyadmin');

/*
    * Adresse publique du serveur FTP (ip ou domaine)
*/
define('SITE_FTP', 'chost.io');

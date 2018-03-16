<?php

class PHPmanagerException extends Exception {

}

class PHPmanager {

    /* Génère le fichier de configuration du pool PHP FPM
    $identifiant DOIT correspondre à un utilisateur EXISTANT sur le système Linux actuel!!!
    */
    function genererConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new PHPManagerException("L'identifiant doit être alpha numérique !");
        }

        return "[$identifiant]

user = $identifiant
group = $identifiant

listen = ".PHP_SOCKET_PATH."$identifiant-fpm.sock

listen.owner = www-data
listen.group = www-data

pm = dynamic

pm.max_children = 5

pm.start_servers = 2

pm.min_spare_servers = 1

pm.max_spare_servers = 3

chroot = ".PHP_PATH."$identifiant/www/
chdir = /

php_admin_value[open_basedir]   = /
php_admin_value[sys_temp_dir]   = /tmp/
php_admin_value[upload_tmp_dir] = /tmp/
        ";
    }

    /* Ecrit la conf */
    function ecrireConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new PHPManagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(PHP_PATH) || !is_writable(PHP_PATH))
        {
            throw new PHPManagerException("Le chemin vers les pools PHP n'existe pas ou n'est pas accessible en écriture");
        }

        file_put_contents(
            PHP_PATH . $identifiant. ".conf",
            $this->genererConf($identifiant)
        );
    }

    function supprimerConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new PHPManagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(PHP_PATH) || !is_writable(PHP_PATH))
        {
            throw new PHPManagerException("Le chemin vers les pools PHP n'existe pas ou n'est pas accessible en écriture");
        }

        if(!file_exists(PHP_PATH . $identifiant . ".conf"))
        {
            throw new PHPmanagerException("Le fichier de conf associé à cet identifiant n'existe pas");
        }

        unlink(PHP_PATH . $identifiant . ".conf");
    }

    /* Recharger le serveur */
    function rechargerServeur()
    {
        // On ne recharge le serveur que si la commande de rechargement est définie
        if(!empty(PHP_RELOAD))
        {
            shell_exec(PHP_RELOAD);

            return true;
        }
        else
        {
            return false;
        }
    }
}
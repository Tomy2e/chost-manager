<?php

class NGINXmanagerException extends Exception {

}

class NGINXmanager {

    function genererConf($identifiant, $vhosts = array())
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new NGINXmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_array($vhosts))
        {
            throw new NGINXmanagerException('Le paramètre $vhost doivent être un tableau');
        }

        $config = "";

        foreach($vhosts as $host)
        {
            if(empty($host['domaine']) || empty($host['chemin']))
            {
                throw new NGINXmanagerException("L'hôte doit comporter un domaine et un chemin relatif");
            }

            // On vérifie que le chemin est valide
            // Source : https://stackoverflow.com/a/36871938/6483433
            if (preg_match('%^/(?!.*\/$)(?!.*[\/]{2,})(?!.*\?.*\?)(?!.*\.\/).*%im', $host['chemin']) == 0 || strstr($host['chemin'], '..'))
            {
                throw new NGINXmanagerException("Le chemin relatif local est invalide, vérifier qu'il ne contient pas des caractères interdits comme '..'");
            }

            // On vérifie que le domaine est valide
            // Source : https://stackoverflow.com/a/4694816/6483433
            if(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host['domaine']) == 0 //valid chars check
            || preg_match("/^.{1,253}$/", $host['domaine']) == 0 //overall length check
            || preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host['domaine']) == 0)
            {
                throw new NGINXmanagerException("Le nom d'hôte fourni est invalide");
            }

            // On supprime le "/" final s'il y en a un
            $host['chemin'] = ($host['chemin'] == '/') ? '/' : rtrim($host['chemin'], '/');
            
            // Concaténation à la config
            $config .= '
server {
    listen 80;

    root '.USER_PATH.$identifiant.'/www'.$host['chemin'] .';

    # Add index.php to the list if you are using PHP
    index index.html index.htm index.php;

    server_name '.$host['domaine'].';

    location / {
        # First attempt to serve request as file, then
        # as directory, then fall back to displaying a 404.
        try_files $uri $uri/ =404;
    }


    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:'.PHP_SOCKET_PATH.$identifiant.'-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    access_log '.USER_PATH.$identifiant.'/logs/'.$host['domaine'] .'_access.log;
    error_log '.USER_PATH.$identifiant.'/logs/'.$host['domaine'] .'_error.log;
}
            ';
        }

        return $config;
    }

    function ecrireConf($identifiant, $vhosts = array())
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new NGINXmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(NGINX_PATH) || !is_writable(NGINX_PATH))
        {
            throw new NGINXmanagerException("Le chemin vers la config nginx n'existe pas ou n'est pas accessible en écriture");
        }

        file_put_contents(
            NGINX_PATH . $identifiant. ".conf",
            $this->genererConf($identifiant, $vhosts)
        );
    }

    function supprimerConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new PHPmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(NGINX_PATH) || !is_writable(NGINX_PATH))
        {
            throw new NGINXmanagerException("Le chemin vers la config nginx n'existe pas ou n'est pas accessible en écriture");
        }

        if(!file_exists(NGINX_PATH . $identifiant . ".conf"))
        {
            throw new NGINXmanagerException("Le fichier de conf associé à cet identifiant n'existe pas");
        }

        unlink(NGINX_PATH . $identifiant . ".conf");
    }

    /* Recharger le serveur */
    function rechargerServeur()
    {
        // On ne recharge le serveur que si la commande de rechargement est définie
        if(!empty(NGINX_RELOAD))
        {
            shell_exec(NGINX_RELOAD);

            return true;
        }
        else
        {
            return false;
        }
    }

}
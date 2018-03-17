<?php

class LINUXmanagerException extends Exception {

}

class LINUXmanager {

    function addUser($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new LINUXmanagerException("L'identifiant doit être alpha numérique !");
        }

        // Création de l'utilisateur Unix
        if(!empty(UNIX_USERADD))
        {
            $commande = str_replace(array(
                '%username%',
                '%homedir%'
            ), array(
                escapeshellarg($identifiant),
                escapeshellarg(USER_PATH . $identifiant)
            ), UNIX_USERADD);

            // TODO: vérifier que la commande a marché (code de retour ?)
            shell_exec($commande);
            
        }

        // Création du répertoire home et des sous répertoires
        if(!is_dir(USER_PATH) || !is_writable(USER_PATH))
        {
            throw new LINUXmanagerException("Le répertoire home n'existe pas ou n'est pas accessible en écriture");
        }

        if(!mkdir(USER_PATH . $identifiant))
        {
            throw new LINUXmanagerException("Le répertoire utilisateur n'a pas pu être créé");
        }
        
        if(!mkdir(USER_PATH . $identifiant . '/www'))
        {
            throw new LINUXmanagerException("Le répertoire utilisateur/www n'a pas pu être créé");
        }

        if(!mkdir(USER_PATH . $identifiant . '/logs'))
        {
            throw new LINUXmanagerException("Le répertoire utilisateur/logs n'a pas pu être créé");
        }

        // On définit correctement les permissions (Uniquement sur Linux!!)
        if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        {
            if(!chown(USER_PATH . $identifiant, $identifiant) || !chown(USER_PATH . $identifiant . '/www', $identifiant) || !chown(USER_PATH . $identifiant . '/logs', $identifiant))
            {
                throw new LINUXmanagerException("Impossible de définir les permissions correctement");
            }
        }

    }

    function deleteUser($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new LINUXmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!empty(UNIX_USERDEL))
        {
            $commande = str_replace('%username%', escapeshellarg($identifiant), UNIX_USERDEL);

            // TODO: tester si ça a fonctionné
            shell_exec($commande);
        }
    }

    /* Retourne en octet la taille occupée par un identifiant */
    function getDirSize($identifiant)
    {
        $dir = USER_PATH . $identifiant;

        if(!file_exists($dir))
        {
            throw new LINUXmanagerException("Impossible de calculer la taille d'un dossier qui n'existe pas");
        }

        // source : https://helloacm.com/get-files-folder-size-in-php/ 

        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }
}
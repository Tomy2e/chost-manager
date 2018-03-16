<?php

class LINUXmanagerException extends Exception {

}

class LINUXmanagerException {

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
                escapeshellarg($username),
                escapeshellarg(USER_PATH . $username)
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

        // Todo : donner les droits à l'utilisateur
        


    }
}
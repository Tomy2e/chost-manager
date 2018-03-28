<?php

class SSHmanagerException extends Exception {

}

class SSHmanager {

    /* Génère le fichier de configuration du pool PHP FPM
    $identifiant DOIT correspondre à un utilisateur EXISTANT sur le système Linux actuel!!!
    */
    function genererConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new SSHManagerException("L'identifiant doit être alpha numérique !");
        }

        return "Match User $identifiant
        ForceCommand internal-sftp
        PasswordAuthentication yes
        ChrootDirectory ".USER_PATH."$identifiant
        PermitTunnel no
        AllowAgentForwarding no
        AllowTcpForwarding no
        X11Forwarding no";
    }

    /* Ecrit la conf */
    function ecrireConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new SSHmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(SSH_PATH) || !is_writable(SSH_PATH))
        {
            throw new SSHmanagerException("Le chemin vers les pools SSH n'existe pas ou n'est pas accessible en écriture");
        }

        file_put_contents(
            SSH_PATH . $identifiant,
            $this->genererConf($identifiant)
        );
    }

    function supprimerConf($identifiant)
    {
        if(!ctype_alnum($identifiant)) 
        {
            throw new SSHmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!is_dir(SSH_PATH) || !is_writable(SSH_PATH))
        {
            throw new SSHmanagerException("Le chemin vers les pools PHP n'existe pas ou n'est pas accessible en écriture");
        }

        if(!file_exists(SSH_PATH . $identifiant))
        {
            throw new SSHmanagerException("Le fichier de conf associé à cet identifiant n'existe pas");
        }

        unlink(SSH_PATH . $identifiant);
    }

    /* Recharger le serveur */
    function rechargerServeur()
    {
        // On ne recharge le serveur que si la commande de rechargement est définie
        if(!empty(SSH_RELOAD))
        {
            shell_exec(SSH_RELOAD);

            return true;
        }
        else
        {
            return false;
        }
    }
}
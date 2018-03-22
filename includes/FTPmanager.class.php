<?php

class FTPmanagerException extends Exception {

}

class FTPmanager {

    function addUser($username, $password)
    {
        if(!ctype_alnum($username))
        {
            throw new FTPmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!empty(FTP_USERADD))
        {
            $commande = str_replace(array(
                '%username%',
                '%password%',
                '%homedir%'
            ), array(
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg(USER_PATH . $username)
            ), FTP_USERADD);
    
            shell_exec($commande);

            $this->mkDb();
        }

    }

    function setQuota($username, $quotaInMB)
    {
        if(!ctype_alnum($username))
        {
            throw new FTPmanagerException("L'identifiant doit être alpha numérique !");
        }

        $quotaInMB = intval($quotaInMB);

        if($quotaInMB <= 0)
        {
            throw new FTPmanagerException("Le quota doit être supérieur à 0");
        }

        if(!empty(FTP_QUOTAS))
        {

            $commande = str_replace(array(
                '%username%',
                '%sizeInMB%'
            ), array(
                escapeshellarg($username),
                escapeshellarg($quotaInMB)
            ), FTP_QUOTAS);

            shell_exec($commande);
            
            $this->mkDb();
        }
    }

    function setPassword($username, $password)
    {
        if(!ctype_alnum($username))
        {
            throw new FTPmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!empty(FTP_PASSWD)) 
        {
            $commande = str_replace(array(
                '%username%',
                '%password%'
            ), array(
                escapeshellarg($username),
                escapeshellarg($password)
            ), FTP_PASSWD);

            shell_exec($commande);

            $this->mbDb();
        }
    }

    function delUser($username)
    {
        if(!ctype_alnum($username))
        {
            throw new FTPmanagerException("L'identifiant doit être alpha numérique !");
        }

        if(!empty(FTP_USERDEL)) 
        {
            $commande = str_replace(array(
                '%username%'
            ), array(
                escapeshellarg($username)
            ), FTP_USERDEL);

            shell_exec($commande);

            $this->mbDb();
        }
    }

    private function mkDb()
    {
        if(!empty(FTP_MKDB)) 
        {
            shell_exec(FTP_MKDB);
        }
    }


}
<?php

class CodesActivation
{
    private $db;

    function __construct()
    {
        $this->db = DBmanager::getInstance();
    }

    function utiliserCode($code)
    {
        $prep_getCode = $this->db->prepare("SELECT * FROM codes_activation WHERE code = ?");
        $prep_getCode->execute(array(
            $code
        ));

        $code = $prep_getCode->fetch();

        if(!empty($code)) 
        {
            $this->supprimerCode($code);
            return true;
        }
        else
        {
            return false;
        }
    }

    function supprimerCode($code)
    {
        $prep_supCode = $this->db->prepare("DELETE FROM codes_activation WHERE code = ?");
        return $prep_supCode->execute(array(
            $code
        ));
    }
}
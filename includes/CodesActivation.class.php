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

        $code_fetched = $prep_getCode->fetch();

        if(!empty($code_fetched)) 
        {
            $this->supprimerCode($code);
            return $code_fetched['VALEUR_CODE'];
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
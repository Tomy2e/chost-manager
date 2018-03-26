<?php

class MAILmanagerException extends Exception {

}

class MAILmanager {
    public static function send($destinataire, $sujet, $body, $isHTML = false)
    {
        
        if(!filter_var($destinataire, FILTER_VALIDATE_EMAIL))
        {
            throw new MAILmanagerException("Le format de l'adresse mail est incorrect");
        }

        $headers = 'From: '. EMAIL_FROM . "\r\n" .
        'Reply-To: ' . EMAIL_FROM . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        if($isHTML) 
        {
            $headers .= "\r\n" . 'MIME-Version: 1.0';
            $headers .= "\r\nContent-Type: text/html; charset=\"utf-8\"";
        }

        return mail($destinataire, $sujet, $body, $headers);
    }
}
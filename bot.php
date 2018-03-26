<?php
require_once('includes/autoload.php');

if(!is_cli())
{
    header("Location: index.php");
    exit();
}

$linuxm = new LINUXmanager;

foreach(Client::listerClients() as $client)
{
    $clientObj = new Client;
    $clientObj->setIdclient($client['ID_CLIENT']);
    $clientObj->fetchInfos();

    $souscriptionObj = new Souscription;
    $souscriptionObj->setIdClient($client['ID_CLIENT']);

    echo "-> Client "  . $client['ID_CLIENT'] . PHP_EOL; 

    foreach($souscriptionObj->listerSouscriptions() as $souscription)
    {
        echo "  -> Souscription " . $souscription['SOUSDOMAINE'] . PHP_EOL;
        $timestampExpire = new DateTime($souscription['EXPIRE']);
        $timestampExpire = $timestampExpire->getTimestamp();

        $secondesRestant = $timestampExpire - time(); // positif si l'abonnement est actif, négatif si l'abonnement est expiré

        $prixTTC = $souscription['PRIX_OFFRE'] * (1 + Souscription::TVA / 100);
        $sousdomaine = $souscription['SOUSDOMAINE'];

        // L'abonnement doit être renouvelé (moins de 24 heures restant)
        if($secondesRestant > 0 && $secondesRestant <= 86400)
        {
            echo "    -> Renouvellement auto (moins de 24 heures) : ";

            if($clientObj->getCredit(true) >= $prixTTC)
            {
                $souscriptionObj->renouvelerSouscription($souscription['IDENTIFIANT_SOUSCRIPTION']);
                echo "OK";
            }
            else
            {
                echo "Fonds insuffisants";
                // Envoyer un mail d'erreur
                MAILmanager::send($clientObj->getEmail(), "Problème lors du renouvellement de votre hébergement cHost", "
                <html>
                <head>
                 <title>Problème lors du renouvellement de votre hébergement cHost</title>
                </head>
                <body>
                <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
                <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
                <hr>
                Bonjour ".$clientObj->getPrenom() . " " . $clientObj->getNom() . ",<br /><br />
                Nous n'avons pas pu renouveler votre souscription concernant le site : <a href='http://$sousdomaine.".USER_DOMAIN."'>http://$sousdomaine.".USER_DOMAIN."</a>.<br /><br />
                En effet, les crédits présents sur votre compte sont insuffisants pour procéder au renouvellement automatique<br />
                Veuillez recharger votre compte et procéder au renouvellement manuellement.<br />
                Sachez qu'en cas de non réaction de votre part, votre hébergement sera suspendu et tous les jours de suspension seront dûs<br />
                Au bout d'une période de 10 jours, tous vos fichiers seront supprimés sans possibilité de récupération.<br /><br />
                Nos équipes restent à votre dispositions via l'onglet support accessible sur votre espace client en cas de problème.<br />
                <br />
                Cordialement, l'équipe cHost.
                </center>
          
                </body>
               </html>
                ", true);
            }

            echo PHP_EOL;
        }
        // L'abonnement est expiré
        else if($secondesRestant < 0)
        {
            echo "    -> Abonnement expiré, on renouvelle : ";
            // On tente de renouveler 
            if($clientObj->getCredit(true) >= $prixTTC)
            {
                $souscriptionObj->renouvelerSouscription($souscription['IDENTIFIANT_SOUSCRIPTION']);
                echo "OK" . PHP_EOL;
            }
            else
            {
                // ça a échoué
                echo "Fonds insuffisants" . PHP_EOL;

                if(abs($secondesRestant) <= 86400)
                {
                    // expiré depuis moins d'1 jours, on suspend et on envoie un mail
                    echo "    -> Abonnement expiré depuis moins d'1 jour, on suspend et on envoie un mail" . PHP_EOL;

                    $souscriptionObj->suspendreSouscription($souscription['IDENTIFIANT_SOUSCRIPTION']);

                    // Envoyer un mail d'erreur
                    MAILmanager::send($clientObj->getEmail(), "Problème lors du renouvellement de votre hébergement cHost", "
                    <html>
                    <head>
                    <title>Problème lors du renouvellement de votre hébergement cHost</title>
                    </head>
                    <body>
                    <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
                    <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
                    <hr>
                    Bonjour ".$clientObj->getPrenom() . " " . $clientObj->getNom() . ",<br /><br />
                    Nous sommes dans le regret de vous annoncer que le site suivant a été suspendu : <a href='http://$sousdomaine.".USER_DOMAIN."'>http://$sousdomaine.".USER_DOMAIN."</a>.<br /><br />
                    En effet, les crédits présents sur votre compte sont insuffisants pour procéder au renouvellement automatique<br />
                    Veuillez recharger votre compte et procéder au renouvellement manuellement.<br />
                    Au bout d'une période de 10 jours, tous vos fichiers seront supprimés sans possibilité de récupération.<br /><br />
                    Nos équipes restent à votre dispositions via l'onglet support accessible sur votre espace client en cas de problème.<br />
                    <br />
                    Cordialement, l'équipe cHost.
                    </center>
            
                    </body>
                    </html>
                    ", true);
                }
                // Au bout de 10 jours, on supprime la souscription
                else if(abs($secondesRestant) > (86400 * 10))
                {
                    echo "    -> Abonnement expiré depuis plus de 10 jours, on le supprime" . PHP_EOL;
                    $souscriptionObj->resilierSouscription($souscription['IDENTIFIANT_SOUSCRIPTION']);

                    // Envoyer un mail d'erreur
                    MAILmanager::send($clientObj->getEmail(), "Suppression de votre hébergement cHost", "
                    <html>
                    <head>
                    <title>Suppression de votre hébergement cHost</title>
                    </head>
                    <body>
                    <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
                    <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
                    <hr>
                    Bonjour ".$clientObj->getPrenom() . " " . $clientObj->getNom() . ",<br /><br />
                    Nous sommes dans le regret de vous annoncer que le site suivant a été supprimé : <a href='http://$sousdomaine.".USER_DOMAIN."'>http://$sousdomaine.".USER_DOMAIN."</a>.<br /><br />
                    Constatant l'absence de renouvellement de votre part, nous avons procédé à la suppression de votre souscription de manière irréversible.<br />
                    <br />
                    Cordialement, l'équipe cHost.
                    </center>
            
                    </body>
                    </html>
                    ", true);

                    continue; // On n'exécute pas la suite
                }
            }
        }

        if($linuxm->getDirSize($souscription['IDENTIFIANT_SOUSCRIPTION']) > $souscription['ESPACE_STOCKAGE'])
        {
            echo "    -> L'abonnement dépasse son quota autorisé" . PHP_EOL;

            // Envoyer un mail d'erreur
            MAILmanager::send($clientObj->getEmail(), "Quota dépassé sur un de vos hébergements", "
            <html>
            <head>
            <title>Quota dépassé sur un de vos hébergements</title>
            </head>
            <body>
            <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
            <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
            <hr>
            Bonjour ".$clientObj->getPrenom() . " " . $clientObj->getNom() . ",<br /><br />
            Nous avons constaté un dépassement dépassement de quota sur le site suivant : <a href='http://$sousdomaine.".USER_DOMAIN."'>http://$sousdomaine.".USER_DOMAIN."</a>.<br /><br />
            Nous vous encourageons à prendre les mesures nécessaires pour faire cesser cette utilisation excessive d'espace disque.<br />
            <br />
            Cordialement, l'équipe cHost.
            </center>
    
            </body>
            </html>
            ", true);
        }
    }
}
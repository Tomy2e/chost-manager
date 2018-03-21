<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

/* 
    Template HTML utilisé: https://htmlpdfapi.com/blog/free_html5_invoice_templates
*/

$facturesObj = new Facture;

try{
    $facture = $facturesObj->infosFacture($_GET['id']);

    if($facture['ID_CLIENT'] != $_SESSION['id_client'])
    {
        header("Location: factures.php");
        exit();
    }
}
catch (FactureException $e)
{
    header("Location: factures.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Facture #<?= $facture['ID_FACTURE'] ?> - cHost</title>
    <link rel="stylesheet" href="css/facture.css" media="all" />
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="images/logo.png">
      </div>
      <h1>Facture #<?= $facture['ID_FACTURE'] ?></h1>
      <div id="company" class="clearfix">
        <div>cHost France</div>
        <div>2 Rue de la Châtaigneraie,<br /> 35510 Cesson-Sévigné</div>
        <div>02 99 33 04 36</div>
        <div><a href="mailto:contact@chost.com">contact@chost.com</a></div>
      </div>
      <div id="project">
        <div><span>CIVILIT&Eacute;</span> Particulier</div>
        <div><span>CLIENT</span> <?= $facture['client']['PRENOM']; ?> <?= $facture['client']['NOM']; ?></div>
        <div><span>ADDRESSE</span> <?= $facture['client']['ADRESSE']; ?>, <?= $facture['client']['CODEPOSTAL']; ?> <?= $facture['client']['VILLE']; ?></div>
        <div><span>EMAIL</span> <a href="mailto:<?= $facture['client']['EMAIL']; ?>"><?= $facture['client']['EMAIL']; ?></a></div>
        <div><span>DATE</span> <?= $facture['DATE_FACTURE']; ?></div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service">ID</th>
            <th class="desc">DESCRIPTION</th>
            <th>PRIX</th>
            <th>QTE</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($facture['achats'] as $achat) : ?>
          <tr>
            <td class="service"><?= $achat['ID_OFFRE']; ?></td>
            <td class="desc"><?= $achat['NOM_OFFRE']; ?></td>
            <td class="unit"><?= $achat['PRIX_OFFRE']; ?>€</td>
            <td class="qty">1</td>
            <td class="total"><?= $achat['PRIX_OFFRE']; ?>€</td>
          </tr>
        <?php endforeach; ?>
          <tr>
            <td colspan="4">SOUS-TOTAL</td>
            <td class="total"><?= $facture['TOTAL_FACTURE']; ?>€</td>
          </tr>
          <tr>
            <td colspan="4">TVA 0%</td>
            <td class="total">0€</td>
          </tr>
          <tr>
            <td colspan="4" class="grand total">TOTAL</td>
            <td class="grand total"><?= $facture['TOTAL_FACTURE']; ?>€</td>
          </tr>
        </tbody>
      </table>
      <div id="notices">
        <div>AVERTISSEMENT:</div>
        <div class="notice">Assurez vous d'avoir un compte suffisamment crédité pour permettre le renouvellement automatique de vos services.</div>
      </div>
    </main>
    <footer>
    Ces factures sont générées automatiquement par notre système, veuillez ouvrir un ticket si vous constatez une erreur.
    </footer>
  </body>
</html>
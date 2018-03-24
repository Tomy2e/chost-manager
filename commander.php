<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

/* Steps : 

  1 - Selection de l'offre
  2 - Vérification eligibilité si prix = 0 puis Configuration de l'offre (domaine) + confirmation paiement
  3 - Affichage message de confirmation ou d'erreur
*/

$offres = array(
  array(
    "code" => "disco2018",
    "titre" => "Formule Discover - 100MO - 1 mois",
    "stockage" => 100,
    "prix" => 0
  ),
  array(
    "code" => "pro2018",
    "titre" => "Formule Pro - 4GO - 1 mois",
    "stockage" => 4096,
    "prix" => 5
  ),
  array(
    "code" => "prem2018",
    "titre" => "Formule Premium - 10GO - 1 mois",
    "stockage" => 10240,
    "prix" => 10
  )
);

$step = 1;

if(!empty($_GET['code']))
{
  foreach($offres as $offre)
  {
    if($_GET['code'] == $offre['code'])
    {
      $offreSelectionnee = $offre;
      $coeff = 1 + Souscription::TVA/100;
      $prixTTC = round($offre['prix'] * $coeff, 2);

      if($offre['prix'] == 0 && !$souscriptionObj->eligibleOffreEssai())
      {
        $view_error = "Vous avez déjà un abonnement gratuit actif, veuillez résilier celui-ci pour pouvoir souscrire à cet abonnement";
        break;
      }

      if($offre['prix'] > $clientObj->getCredit(true))
      {
        $view_error = "Votre compte est insufisamment crédité pour pouvoir souscrire à cet abonnement";
        break;
      }

      $step = 2;
      break;
    }
  }
}

if($step == 2 && !empty($_POST))
{
  if(empty($_POST['sous-domaine']) || !$souscriptionObj->verifDispoSousdomaine($_POST['sous-domaine']))
  {
    $view_error = "Le sous-domaine choisi n'est pas disponible";
  }
  
  if(empty($_POST['cgv']) || $_POST['cgv'] != 'checked')
  {
    if(!empty($view_error))
    {
      $view_error .= "<br />";
    }
    $view_error.= "Veuillez accepter les CGU et les CGV en cochant la case appropriée";
  }

  if(empty($view_error))
  {
    try {
     $souscriptionObj->ajouterSouscription($offreSelectionnee['titre'], $offreSelectionnee['stockage'], $offreSelectionnee['prix'], $_POST['sous-domaine']);
     $step = 3;
    } catch(Exception $e)
    {
      $view_error = $e->getMessage();
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Commander - Espace Client - cHost</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <!-- Navigation-->
  <?php require_once("includes/nav.template.php"); ?>
  <div class="content-wrapper">

  <?php if($step == 1) : ?>
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.php">Accueil</a>
        </li>
        <li class="breadcrumb-item active">Commander un hébergement</li>
      </ol>

      <?php if(!empty($view_error)) : ?>
      <div class="alert alert-danger" role="alert">
        <strong>Oups !</strong> <?= $view_error; ?>
      </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-12">
          <h1>Commander</h1>
          <form action="" method="get">
          <div class="form-group">
            <label for="code">Choisissez une offre</label>
            <select name="code" class="form-control">
              <?php foreach($offres as $offre) : ?>
              <option value="<?= $offre['code']; ?>"><?= $offre['titre']; ?> - <?= $offre['stockage']; ?>MB d'hébergement web - 1 base MySQL - <?= $offre['prix']; ?>€/mois (HT)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Suivant</button>
        </form>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php elseif($step == 2) : ?>
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.php">Accueil</a>
        </li>
        <li class="breadcrumb-item">
          <a href="commander.php">Commander un hébergement</a>
        </li>
        <li class="breadcrumb-item active"><?= $offreSelectionnee['titre']; ?></li>
      </ol>

      <?php if(!empty($view_error)) : ?>
      <div class="alert alert-danger" role="alert">
        <strong>Oups !</strong> <?= $view_error; ?>
      </div>
      <?php endif; ?>

      <div class="row">

        <div class="col-12">
        <h1>Ce qui est inclu dans votre offre</h1>

        <ul class="list-group">
        <li class="list-group-item"><?= $offre['stockage']; ?>MB d'espace disque pour votre hébergement web</li>
        <li class="list-group-item">1 sous-domaine en .<?= USER_DOMAIN; ?></li>
        <li class="list-group-item">1 accès FTP</li>
        <li class="list-group-item">1 base de données MySQL avec accès phpMyAdmin</li>
        <li class="list-group-item">Traffic illimité</li>
        <li class="list-group-item">PHP 7.0</li>
        <li class="list-group-item"><strike>Certificats SSL Let's Encrypt</strike> Bientôt!</li>
      </ul>
        </div>


        <div class="col-12 mt-3">
          <h1>Paramètres</h1>
          <form action="" method="post">
          <label for="basic-url">Sous-domaine (caractères alpha-numériques uniquement, moins de 30 caractères autorisés):</label>
          <div class="input-group mb-3">
            <input type="text" name="sous-domaine" value="<?= (!empty($_POST['sous-domaine'])) ? htmlspecialchars($_POST['sous-domaine']) : ''; ?>" class="form-control" id="basic-url" aria-describedby="basic-addon3">
            <div class="input-group-append">
              <span class="input-group-text">.<?= USER_DOMAIN; ?></span>
            </div>
          </div>
          <div class="form-group">
            <label for="cms">Gestionnaire de contenus (CMS) préinstallé:</label>
            <select name="cms" class="form-control">
            <option value="aucun">Aucun</option>
          </select>
          </div>

        </div>

        <div class="col-12 mt-3 mb-3">
        <h1>Récapitulatif</h1>

        <ul class="list-group">
        <li class="list-group-item">Prix HT : <?= $offreSelectionnee['prix']; ?>€/mois</li>
        <li class="list-group-item">TVA : <?= Souscription::TVA; ?>%</li>
        <li class="list-group-item">Prix TTC : <?= $prixTTC; ?>€/mois</li>
        <li class="list-group-item">Votre abonnement sera automatiquement renouvelé chaque mois avec les crédits disponibles sur votre compte, votre hébergement pourra être suspendu en cas de fonds insuffisants.</li>
      </ul>

      <div class="form-check mt-2">
            <label class="form-check-label">
              <input class="form-check-input" name="cgv" value="checked" type="checkbox" required> J'accepte les CGU et les CGV de cHost (obligatoire) 
            </label>
          </div>
          <button type="submit" class="btn btn-primary">Payer</button>
      </form>
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php elseif($step == 3) : ?>
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.php">Accueil</a>
        </li>
        <li class="breadcrumb-item">
          <a href="commander.php">Commander un hébergement</a>
        </li>
        <li class="breadcrumb-item active"><?= $offreSelectionnee['titre']; ?></li>
      </ol>

      <div class="row">

        <div class="col-12">
        <h1>Merci pour votre souscription !</h1>

        Votre hébergement est en train d'être configuré.<br /><br />

        Vous allez recevoir un email contenant vos identifiants FTP et MySQL (merci de vérifier vos spams).<br /><br />

        Veuillez ouvrir un ticket en cas de problème.
        </div>
      </div>
    </div>
    <!-- /.container-fluid-->
    <?php endif; ?>
    <!-- /.content-wrapper-->
    <?php require_once('includes/footer.template.php'); ?>    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="login.html">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
  </div>
</body>

</html>

<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}


$infoSouscription = $souscriptionObj->infoSouscription($_GET['id']);

//print_r($infoSouscription);

if($infoSouscription['ID_CLIENT'] != $_SESSION['id_client'])
{
    header("Location: index.php");
    exit();    
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
  <title>Votre hébergement - Espace Client - cHost</title>
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
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="index.php">Accueil</a>
        </li>
        <li class="breadcrumb-item">
        <a class="" data-toggle="collapse" href="#collapseComponents" data-parent="#exampleAccordion" aria-expanded="false">Mes hébergements</a>
        </li>
        <li class="breadcrumb-item active"><?= $infoSouscription['SOUSDOMAINE']; ?>.<?= USER_DOMAIN; ?></li>
      </ol>

      <?php if(!empty($view_error)) : ?>
      <div class="alert alert-danger" role="alert">
        <strong>Oups !</strong> <?= $view_error; ?>
      </div>
      <?php endif; ?>

      <?php if(!empty($view_success)) : ?>
      <div class="alert alert-success" role="alert">
        <strong>Hourra !</strong> <?= $view_success; ?>
      </div>
      <?php endif; ?>
      <div class="row">
      <div class="col-lg-12">
        <div class="card mb-3">
                <div class="card-header">
                <i class="fa fa-info"></i> Informations générales</div>
                <div class="card-body">
                <div class="row">
                    <div class="col-sm-8 my-auto">
                    Votre site web est disponible à l'adresse suivante : <a rel="noopener noreferrer" target="_blank" href="http://<?= $infoSouscription['SOUSDOMAINE']; ?>.<?= USER_DOMAIN; ?>">http://<?= $infoSouscription['SOUSDOMAINE']; ?>.<?= USER_DOMAIN; ?></a>
                    </div>

                </div>
                <div class="row mt-3">
                <div class="col-sm-8 my-auto">
                Vous utilisez actuellement <div class="h4 mb-0 text-primary" style="display:inline"><?= $infoSouscription['DIRSIZE']; ?>MB</div>/<div class="h4 mb-0 text-primary" style="display:inline"><?= $infoSouscription['ESPACE_STOCKAGE']; ?>MB</div> de votre hébergement WEB<br /> 
                </div>

                </div>
                <div class="progress mt-2">
                <div class="progress-bar 
                <?php if($infoSouscription['POURCENTAGE_UTILISATION_DISQUE'] < 50) : ?>
                bg-success
                <?php elseif($infoSouscription['POURCENTAGE_UTILISATION_DISQUE'] < 80): ?>
                bg-warning
                <?php else: ?>
                bg-danger
                <?php endif; ?>
                " role="progressbar" style="width: <?= $infoSouscription['POURCENTAGE_UTILISATION_DISQUE']; ?>%" aria-valuenow="<?= $infoSouscription['POURCENTAGE_UTILISATION_DISQUE']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                </div>
                <div class="card-footer small text-muted">Tout dépassement volontaire ou involontaire de votre quota pourra mener à une suspension de service</div>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <!-- Example Bar Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-folder-open"></i> FTP</div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-8 my-auto">
                  Hôte/serveur : <?= SITE_FTP; ?> (port 21)<br />
                  Nom d'utilisateur : <?= $infoSouscription['IDENTIFIANT_SOUSCRIPTION']; ?><br />
                  Mot de passe : Le mot de passe que vous avez reçu par email<br />
                  Logiciel recommandé : <a href="https://sourceforge.net/projects/winscp/" target="_blank">WinSCP</a>
                </div>

              </div>
            </div>
          </div>
          <!-- /Card Columns-->
        </div>
        <div class="col-lg-6">
          <!-- Example Pie Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-database"></i> MySQL</div>
              <div class="card-body">
              <div class="row">
                <div class="col-sm-8 my-auto">
                  Hôte/serveur : localhost<br />
                  Nom d'utilisateur : <?= $infoSouscription['IDENTIFIANT_SOUSCRIPTION']; ?><br />
                  Mot de passe : Le mot de passe que vous avez reçu par email<br />
                  Gestion en ligne: <a href="<?= SITE_SQLADMIN; ?>" target="_blank">phpMyAdmin</a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
      <div class="col-lg-12">
        <div class="card mb-3">
                <div class="card-header">
                <i class="fa fa-globe"></i> Domaines et sous-domaines</div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                    Vous pourrez bientôt ajouter des noms de domaines personnalisés !
                    </div>
                </div>
                <div class="card-footer small text-muted">Vous ne pouvez pas changer le domaine principal de votre hébergement</div>
            </div>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <!-- Example Bar Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-calendar"></i> Abonnement</div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-8 my-auto">
                  Votre abonnement expire le <div class="h4 mb-0 text-primary"><?= $infoSouscription['EXPIRE']; ?></div>
                </div>

              </div>
            </div>
            <div class="card-footer small text-muted">Votre abonnement sera renouvelé 1 jour avant la date d'expiration</div>
          </div>
          <!-- /Card Columns-->
        </div>
        <div class="col-lg-6">
          <!-- Example Pie Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-cogs"></i> Autres actions</div>
            <div class="card-body">

            <a class="btn btn-primary btn-block" href="#" role="button">Renouveler pour 1 mois</a>
            <a class="btn btn-warning btn-block" href="#" role="button">Changer le mot de passe FTP et MySQL</a>
            <a class="btn btn-danger btn-block" href="#" role="button">Résilier l'abonnement</a>


            </div>
            <div class="card-footer small text-muted">Toute action est définitive</div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.container-fluid-->
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

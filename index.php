<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

$factureObj = new Facture;
$factures = $factureObj->listerFactures($_SESSION['id_client']);

$ticketsObj = new Ticket;
$tickets = $ticketsObj->getTickets($_SESSION['id_client']);
$countTickets = 0;

foreach($tickets as $ticket)
{
  if($ticket['LOCK_TICKET'] == 0){
    $countTickets++;
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
  <title>Accueil - Espace Client - cHost</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
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
          <a href="#">Espace Client</a>
        </li>
        <li class="breadcrumb-item active">Accueil</li>
      </ol>
      <!-- Icon Cards-->
      <div class="row">
        <div class="col-xl-4 col-sm-6 mb-3">
          <div class="card text-white bg-warning o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-list"></i>
              </div>
              <div class="mr-5"><?= count($souscriptionObj->listerSouscriptions()); ?> hébergement actif</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" data-toggle="collapse" href="#collapseComponents" href="#">
              <span class="float-left">Voir plus</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3">
          <div class="card text-white bg-success o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-shopping-cart"></i>
              </div>
              <div class="mr-5"><?= count($factures); ?> factures disponibles</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="factures.php">
              <span class="float-left">Voir plus</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-3">
          <div class="card text-white bg-danger o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-support"></i>
              </div>
              <div class="mr-5"><?= $countTickets ?> tickets ouverts</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="mes-tickets.php">
              <span class="float-left">Voir plus</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
      <div class="col-lg-12">
        <div class="card mb-3">
                <div class="card-header">
                <i class="fa fa-rss"></i> L'actu cHost</div>
                <div class="card-body">
                <div class="row">
                    <div class="col-sm-8 my-auto">
                      <h2>Mise à jour du 22/03/2018</h2>
                      <p>
                      Bonjour à tous !<br />
                      Nous travaillons actuellement sur la possibilité d'avoir un accès SFTP, toutes les offres payantes auront accès à cette fonctionnalité sans augmentation de prix.
                      Pour vous remercier d'être aussi nombreux, nous avons augmenté de 1 Go l'espace disque de <strong>tous</strong> vos hébergements en cours !
                      <br />
                      L'équipe cHost.
                      </p>
                      <h2>Mise à jour du 20/03/2018</h2>
                      <p>
                      Bonjour à tous !<br />
                      Notre site est maintenant en ligne, nous comptons sur vous pour nous signaler tout bug rencontré lors de l'utilisation de notre site.<br />
                      Vous pouvez nous contacter via l'onglet "Support" accessible dans le menu à gauche.<br />
                      L'équipe cHost.
                      </p>
                    </div>

                </div>
                </div>
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
    <?php require_once('includes/modale-deconnexion.template.php'); ?>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
    <!-- Custom scripts for this page-->
    <script src="js/sb-admin-datatables.min.js"></script>
    <script src="js/sb-admin-charts.min.js"></script>
  </div>
</body>

</html>

<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

$test = new Ticket;


if(!empty($_POST)){

  $nouveau_ticket = $test->addTicket($_SESSION['id_client'], 1, $_POST['type'], $_POST['message'], $clientObj->getPrenom() . ' ' . $clientObj->getNom()[0] . '.');

  header("Location: ticket.php?ticket=$nouveau_ticket&opened");
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
  <title>Nouveau ticket - Espace Client - cHost</title>
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
          <a href="mes-tickets.php">Mes tickets</a>
        </li>
        <li class="breadcrumb-item active">Nouveau ticket</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Nouveau ticket</h1>
            <form method="POST">
            <select class="custom-select mb-3">
              <option selected>Hébergement affecté</option>
              <?php foreach ($souscriptionObj->listerSouscriptions() as $souscription) : ?>
              <option value="<?= $souscription['IDENTIFIANT_SOUSCRIPTION']; ?>"><?= $souscription['SOUSDOMAINE']; ?>.<?= USER_DOMAIN; ?> (<?= $souscription['IDENTIFIANT_SOUSCRIPTION']; ?>)</option>
              <?php endforeach; ?>
              <option>Aucun</option>
            </select>

            <select name="type" class="custom-select">
              <option selected>Type de problème</option>
              <option value="Paiement">Paiement</option>
              <option value="Domaine">Nom de domaine</option>
              <option value="Acces">Accès FTP</option>
              <option value="Autre">Autre</option>
            </select>
            <div class="form-group">
              <label for="exampleFormControlTextarea1"></label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="message" placeholder="Description du problème" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ouvrir le ticket</button>
          </form>
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
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
  </div>
</body>

</html>

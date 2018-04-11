<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: login.php");
  exit();
}

$test = new Ticket;
$type = array("Paiement","Domaine","Acces","Autre");

$serv = array("Aucun");

foreach($souscriptionObj->listerSouscriptions() as $sub){
  array_push($serv, $sub['IDENTIFIANT_SOUSCRIPTION']);
}


if(!empty($_POST) && in_array($_POST['type'],$type) && in_array($_POST['server'],$serv) && !empty($_POST['message'])){

  $nouveau_ticket = $test->addTicket($clientObj, 1, $_POST['type'], $_POST['message']);

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
            <?php   
          if(!empty($_POST) && !in_array($_POST['server'],$serv)){
          echo "<div class=\"alert alert-danger\" role=\"alert\">
            Veiller selectionner un server.
          </div>";
          }
           ?>
            <select name ="server" class="custom-select mb-3">
              <option selected>Hébergement affecté</option>
              <?php foreach ($souscriptionObj->listerSouscriptions() as $souscription) : ?>
              <option value="<?= $souscription['IDENTIFIANT_SOUSCRIPTION']; ?>"><?= $souscription['SOUSDOMAINE']; ?>.<?= USER_DOMAIN; ?> (<?= $souscription['IDENTIFIANT_SOUSCRIPTION']; ?>)</option>
              <?php endforeach; ?>
              <option>Aucun</option>
            </select>
            
            <?php   
          if(!empty($_POST) && !in_array($_POST['type'],$type)){
          echo "<div class=\"alert alert-danger\" role=\"alert\">
            Champ non validé.
          </div>";
          }
           ?>
            <select name="type" class="custom-select mb-3">
              <option selected>Type de problème</option>
              <?php 
              
              foreach($type as $value): ?>
              <option value="<?= $value;?>"><?= $value;?></option>
              <?php endforeach; ?>
            </select>
            <?php   
          if(!empty($_POST) &&  empty($_POST['message'])){
          echo "<div class=\"alert alert-danger\" role=\"alert\">
            Veuiller décrire l'objet de votre ticket.
          </div>";
          }
           ?>
            <div class="form-group">
              <label for="exampleFormControlTextarea1"></label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="message" placeholder="Description du problème" rows="3"><?php   
          if(!empty($_POST) &&  !empty($_POST['message'])){
          echo htmlentities($_POST['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
          }
           ?></textarea>
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

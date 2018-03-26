<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

$test = new Ticket;

$id = $_GET['ticket'];



$info = $test->getTicket($_GET['ticket']);
//if($info['ID_CLIENT'] == 1){
$tab =$test->getMessage($_GET['ticket']);

//$test->getTicket(1);


if($info[0]['ID_CLIENT'] != $_SESSION['id_client']) {
  header("Location: mes-tickets.php");
  exit();}

if(!empty($_POST) && $info[0]['LOCK_TICKET'] == 0){
  $test->addMessage($_POST['message'], $clientObj->getPrenom() . ' ' . $clientObj->getNom()[0] . '.' ,$tab[0]['ID_TICKET']);
}

$tab = $test->getMessage($_GET['ticket']);
//}
$lock = $info[0]['LOCK_TICKET'];
if(isset($_GET['action']) && $_GET['action'] == 'LOCK' && $info[0]['LOCK_TICKET'] == 0){
$test->closeTicket($_GET['ticket']);
$info = $test->getTicket($_GET['ticket']);
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
  <title>Mes tickets - Espace Client - cHost</title>
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
        <li class="breadcrumb-item active">Ticket #<?= $_GET['ticket']; ?> (<?= $info[0]['TYPE_PROBLEME']; ?>)</li>
      </ol>
      <?php if(isset($_GET['action']) && $_GET['action'] == 'LOCK' && $lock == 0): ?>
      <div class="alert alert-success">
        <strong>Hourra!</strong> Le ticket a bien été fermé.
      </div>
      <?php endif; ?>

      <?php if(isset($_GET['opened'])) : ?>
      <div class="alert alert-success" role="alert">
        Votre ticket a bien été ouvert. Un technicien vous répondra dans les plus brefs délais.<br />
        N'hésitez pas à rajouter plus de détails le temps que nous vous répondions.
      </div>
      <?php endif; ?>


      <?php
      foreach($tab as $msg) : ?>
              <div class="card mb-3 " style="witdh:80%!important;<?php if($msg['PRENOM_AUTEUR']==$tab[0]['PRENOM_AUTEUR'])echo "";?>">
                  <div class="card-header">
                      <?=$msg['PRENOM_AUTEUR'];?></div>
                    <div class="card-body ">
                      <div class="row">
                        <div class="col-sm-8 my-auto ">
                          <?=$msg['MESSAGE_TICKET'];?>
                        </div>

                      </div>
                    </div>
                    <div class="card-footer small text-muted text-right"><?=$msg['DATE_MESSAGE'];?></div>


              </div>
      <?php endforeach; ?>

      <?php if($info[0]['LOCK_TICKET'] == 0):?>
      <form method="POST" class="mb-3">
            <div class="form-group">
              <label for="exampleFormControlTextarea1"></label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="message" placeholder="..." rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button> <a class="btn btn-danger" href="ticket.php?ticket=<?= $_GET['ticket']; ?>&action=LOCK" role="button">Fermer le ticket</a>


      </form>
      <?php else: ?>
      <div class="alert alert-warning">
        <strong>Ticket fermé !</strong> Vous ne pouvez plus ajouter de messages. Veuillez ouvrir un autre ticket.
      </div>
      <?php endif;?>
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

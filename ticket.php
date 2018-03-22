<?php
require_once('includes/config.php');
require_once('includes/DBmanager.class.php');

require_once('includes/Tickets.class.php');

$test = new Ticket;

$id = $_GET['ticket'];



$info = $test->getTicket($_GET['ticket']);
//if($info['ID_CLIENT'] == 1){
$tab =$test->getMessage($_GET['ticket']);

//$test->getTicket(1);


if($info[0]['ID_CLIENT'] != 1) {
  header("Location: mes-tickets.php");
  exit();}

if(!empty($_POST) && $info[0]['LOCK_TICKET'] == 0){
  $test->addMessage($_POST['message'],"antoine",$tab[0]['ID_TICKET']);
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
          <a href="index.html">Accueil</a>
        </li>
        <li class="breadcrumb-item active">Mes tickets</li>
      </ol>
      <?php if(isset($_GET['action']) && $_GET['action'] == 'LOCK' && $lock == 0): ?>
      <div class="alert alert-success">
        <strong>Hourra!</strong> Le ticket a bien été fermé.
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
      <form method="POST">
            <div class="form-group">
              <label for="exampleFormControlTextarea1"></label>
              <textarea class="form-control" id="exampleFormControlTextarea1" name="message" placeholder="..." rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
      </form>
      <?php else: ?>
      <div class="alert alert-warning">
        <strong>Warning!</strong> Indicates a warning that might need attention.
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

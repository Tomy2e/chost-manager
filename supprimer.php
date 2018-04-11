<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

//$test->getTicket(1);

if(!isset($_POST['password'])){
  $_POST['password']=NULL;
  $chargement=0;
} else{
  $chargement=1;
}

if(!isset($_POST['check'])){
  $_POST['check']=NULL;
}


  if($chargement==1 && password_verify($_POST['password'], $clientObj->getPassword()) && $_POST['check']==1){

    $clientObj->supprimerClient(true);
    deconnexion();

    header("Location: ../");
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
  <title>Suppression de compte - Espace Client - cHost.fr</title>
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="../images/icone.png" />

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
          <li class="breadcrumb-item active">Supprimer mon compte</li>
      </ol>

      <form action="" method="post">

        </br>
        <input name="password" type="password" class="form-control input-lg" id="password" maxlength="" placeholder="Entrez votre mot de passe " required="" />
        </br>
        </br>
        </br>
        <div class="radio">
          <label><input type="checkbox" name="check" value="1">Je consens à supprimer mon compte</label>
        </div>
        </br>
        </br>
        </br>
        <button type="submit" name="go" class="btn btn-lg btn-danger btn-block">Supprimer</button>

      </form>
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
    <!-- Page level plugin JavaScript-->
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/sb-admin-datatables.min.js"></script>

  </div>
</body>

</html>

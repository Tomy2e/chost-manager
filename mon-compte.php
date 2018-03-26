<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
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
  <title>Mon compte - Espace Client - cHost</title>
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
        <li class="breadcrumb-item active">Mon compte</li>
      </ol>
      <div class="row">
        <div class="col-12">
          <h1>Mon compte</h1>
          <form>
            <div class="form-group row">
              <label for="example-text-input" class="col-2 col-form-label">Nom</label>
              <div class="col-10">
                <input class="form-control" disabled type="text" value="<?php echo $clientObj->getNom(); ?>" id="example-text-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-search-input" class="col-2 col-form-label">Prenom</label>
              <div class="col-10">
                <input class="form-control" disabled type="search" value="<?php echo $clientObj->getPrenom(); ?>" id="example-search-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-email-input" class="col-2 col-form-label">Email</label>
              <div class="col-10">
                <input class="form-control" disabled type="email" value="<?php echo $clientObj->getEmail(); ?>" id="example-email-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-search-input" class="col-2 col-form-label">Adresse</label>
              <div class="col-10">
                <input class="form-control" type="search" value="<?php echo $clientObj->getAdresse(); ?>" id="example-search-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-search-input" class="col-2 col-form-label">Code postale</label>
              <div class="col-10">
                <input class="form-control" type="search" value="<?php echo $clientObj->getCode(); ?>" id="example-search-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-search-input" class="col-2 col-form-label">Ville</label>
              <div class="col-10">
                <input class="form-control" type="search" value="<?php echo $clientObj->getVille(); ?>" id="example-search-input">
              </div>
            </div>
            <div class="form-group row">
              <label for="example-tel-input" class="col-2 col-form-label">Telephone</label>
              <div class="col-10">
                <input class="form-control" type="tel" value="<?php echo $clientObj->getTel(); ?>" id="example-tel-input">
              </div>
            </div>
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
            <a class="btn btn-primary" href="../chost-vitrine/index.html">Logout</a>
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

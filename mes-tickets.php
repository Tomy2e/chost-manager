<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

$test = new Ticket;


//$test->getTicket(1);



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
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

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
        <li class="breadcrumb-item active">Mes tickets</li>
      </ol>
      <div class="card mb-3">
      <div class="card-header">
        <i class="fa fa-life-ring"></i> Vos tickets</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>numero ticket</th>
                <th>Type de problème</th>
                <th>Lien</th>
                <th>Etat du ticket</th>
              </tr>
            </thead>
            <tbody>
            <?php  $tab = $test->getTickets(1);

          foreach($tab as $value){
            echo "<tr>";
              echo "<td>" . $value['ID_TICKET']."</td>";
              echo "<td>" . $value['TYPE_PROBLEME']."</td>";


           echo "<td><button onclick=\"window.location.href='./ticket.php?ticket=".$value['ID_TICKET']."'\"type= \"button\" class=\"btn btn-primary btn-lg btn-block\">Lien</button></td>";
            if($value['LOCK_TICKET'] == 0)
            echo "<td><button onclick=\"window.location.href='./ticket.php?ticket=".$value['ID_TICKET']."&action=LOCK'\"type= \"button\" class=\"btn btn-primary btn-lg btn-danger btn-block\">Fermer le ticket</button></td></tr>";
            else
            echo "<td><button type= \"button\" class=\"btn btn-primary btn-lg btn-danger btn-block disabled\">Ticket fermé</button></td></tr>";

            //print_r($value);
            //href ulr/id=truc ticket
          }  ?>

            </tbody>
          </table>
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
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/sb-admin-datatables.min.js"></script>

  </div>
</body>

</html>

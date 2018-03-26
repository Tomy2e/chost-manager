<?php
require_once('includes/autoload.php');

if(!isConnected()) {
  header("Location: connexion.php");
  exit();
}

if(!empty($_GET['paymentId']) && !empty($_GET['PayerID']) && PAYPAL_ENABLE == 'YES')
{
  $pp = new EasyPayPal;

  $credit = $pp->finaliserPaiement($_GET['paymentId'], $_GET['PayerID']);

  if($credit['success'] == false)
  {
    $view_error = "Une erreur PayPal s'est produite avec le code d'erreur suivant : " . $credit['code'];
  }
  else if($credit['success'] == true)
  {
      $clientObj->ajouterCredit($credit['montant']);

      $factureCode = new Facture;
      $factureCode->ajouterAchat("Recharge avec PayPal", $credit['montant'], 0);
      $factureGeneree = $factureCode->genererFacture($_SESSION['id_client']);

      $factureCode->envoyerMail($clientObj, $factureGeneree);
      
      $view_success = "Votre compte a bien été crédité de " . $credit['montant'] . "€";
  }
}

if(!empty($_POST))
{
  // Code d'activation ?
  if(!empty($_POST['code-activation']))
  {
    $ca = new CodesActivation;

    $montant = $ca->utiliserCode($_POST['code-activation']);
    if($montant !== false)
    {
      $clientObj->ajouterCredit($montant);

      $factureCode = new Facture;
      $factureCode->ajouterAchat("Recharge avec le code : " . htmlspecialchars($_POST['code-activation']), $montant, 0);
      $factureGeneree = $factureCode->genererFacture($_SESSION['id_client']);

      $factureCode->envoyerMail($clientObj, $factureGeneree);

      $view_success = "Votre compte a bien été crédité de " . $montant . "€";
    }
    else
    {
      // code invalide
      $view_error = "Le code fourni a déjà été utilisé ou n'existe pas.";
    }
  }
  else if(!empty($_POST['montant-paypal']) && PAYPAL_ENABLE == 'YES')
  {
    $montant = floatval($_POST['montant-paypal']);
    $montant = round($montant, 2);

    if($montant < 1 || $montant > 100)
    {
      $view_error = "Le montant doit être supérieur à 1€ et inférieur à 100€";
    }
    else
    {
      $pp = new EasyPayPal;
      header("Location:" . $pp->nouveauPaiement("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $montant, "Recharge du compte cHost - " . $montant . " euros"));
      exit();
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
  <title>Mon crédit - Espace Client - cHost</title>
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
        <li class="breadcrumb-item active">Mon crédit</li>
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
        <div class="col-lg-8">
          <!-- Example Bar Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-bar-chart"></i> Votre crédit</div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-8 my-auto">
                  Vous compte possède actuellement <div class="h4 mb-0 text-primary"><?= $clientObj->getCredit(true); ?>€</div>
                </div>

              </div>
            </div>
            <div class="card-footer small text-muted">Cette somme est non remboursable</div>
          </div>
          <!-- /Card Columns-->
        </div>
        <div class="col-lg-4">
          <!-- Example Pie Chart Card-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fa fa-pie-chart"></i> Recharger votre compte</div>
            <div class="card-body">
            <?php if(PAYPAL_ENABLE == 'YES') : ?>
            <div class="form-group row">
                  <h5>Créditer via PayPal</h5>
                </div>
              <form class="form-inline" action="credit.php" method="post">

                <div class="form-group row">
                  <label class="sr-only" for="inlineFormInput">Montant</label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="montant-paypal" value="5" aria-label="Amount (to the nearest dollar)">
                    <div class="input-group-append">
                      <span class="input-group-text">€</span>
                    </div>
                  </div>
                  &nbsp;&nbsp;
                  <button type="submit" class="btn btn-primary">Valider</button>
                </div>

              </form>
              <?php endif; ?>
              <div class="form-group row mt-3">
                  <h5>Utiliser un code d'activation</h5>
                </div>

              <form class="form-inline" action="credit.php" method="post">

                <div class="form-group row">
                  <label class="sr-only" for="inlineFormInput">Montant</label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="code-activation" value="" aria-label="Code d'activation" placeholder="ABCD-ABDC-ABCD">
                  </div>
                  &nbsp;&nbsp;
                  <button type="submit" class="btn btn-primary">Valider</button>
                </div>

              </form>
            </div>
            <div class="card-footer small text-muted">Une facture sera créée automatiquement</div>
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

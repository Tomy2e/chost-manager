<?php
require_once('includes/autoload.php');

if(isConnected())
{
        header("Location: index.php");
        exit();
}

if(isset($_POST['email']))
{
  $dbh = DBmanager::getInstance();
  $compteur = 0;
  $prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
  $prep_fetch->execute();

  $fetched = $prep_fetch->fetchAll();
  $nb = $prep_fetch->rowCount();
  //print_r( $fetched);
  for($i=0;$i<$nb;$i++){

  	if($_POST['email']== $fetched[$i][3] && $_POST['password']== $fetched[$i][4] ){
      if($fetched[$i][10]==1){
        connexion($fetched[$i]['ID_CLIENT']);
    		header('Location: index.php');
        exit();
      }
      else{
        echo("<script>alert('Le compte est bien créé, mais n\'est pas actif, consultez votre boite mail afin d\'y remédier');</script>");
        $compteur++;
      }
  	}
  }


  if($compteur==0){
    echo("<script>alert('Erreur de connexion, mot de passe ou identifiant incorrect');</script>");
  }
}


	$dbh = NULL;
?>



<!DOCTYPE HTML>

<html>
 <head>
  <link href="css/login.css" rel="stylesheet" id="bootstrap-css">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
  <script src="js/login.js"></script>
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
 </head>

 <body>

<div class="container">

  <div class="row" id="pwd-container">
    <div class="col-md-4"></div>

    <div class="col-md-4">
      <section class="login-form">
        <form method="post" action="" role="login">
       <!--   <img src="http://i.imgur.com/RcmcLv4.png" class="img-responsive" alt="" />
        -->
          <img src="images/logo.png" class="img-responsive" alt="" />

	  <input type="email" name="email" placeholder="Entrez votre mail" required class="form-control input-lg" value="<?= @$_POST['email'];?>" />

          <input name="password" type="password" class="form-control input-lg" id="password" placeholder="Entrez le mot de passe" required="" />


          <div class="pwstrength_viewport_progress"></div>


          <button type="submit" name="go" class="btn btn-lg btn-primary btn-block">Se connecter</button>
          <div>
            <a href="create.php">Cr&eacute;er un compte</a> ou <a href="#">R&eacute;initialiser le mot de passe</a>
          </div>

        </form>

        <div class="form-links">
          <a href="../chost-vitrine">Retour à l'accueil</a>
        </div>
      </section>
      </div>

      <div class="col-md-4"></div>


  </div>

<!--  <p>
    <a href="http://validator.w3.org/check?uri=http%3A%2F%2Fbootsnipp.com%2Fiframe%2FW00op" target="_blank"><small>HTML</small><sup>5</sup></a>
    <br>
    <br>
    -->
  </p>


</div>






 </body>
</html>

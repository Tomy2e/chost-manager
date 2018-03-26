
<?php
require_once('includes/autoload.php');

if(isConnected())
{
        header("Location: index.php");
        exit();
}

//----------------------------------TO DO----------------
//
// fixer les regex des mots de passes ainsi que de l'adresse
//
//
//-------------------------------------------------------
if(!isset($_POST['prenom'])){
  $_POST['prenom']=NULL;
}
if(!isset($_POST['nom'])){
  $_POST['nom']=NULL;
}

if(!isset($_POST['email'])){
  $_POST['email']=NULL;
}

$dbh = DBmanager::getInstance();

$prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
$prep_fetch->execute();

$fetched_control = $prep_fetch->fetchAll();
//print_r ($fetched_control);
$nb = $prep_fetch->rowCount();



if ( !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,30})$#", $_POST['prenom']) && !preg_match("#^$#", $_POST['prenom']) ){
                $_POST['prenom']=NULL;
                echo ("<script>alert('Merci d\'entrer un prénom composé uniquement de lettres');</script>");

        }

else if ( !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,30})$#", $_POST['nom']) && !preg_match("#^$#", $_POST['nom']) ){
                $_POST['nom']=NULL;
                echo ("<script>alert('Merci d\'entrer un nom composé uniquement de lettres');</script>");

        }






else for($i=0;$i<$nb;$i++){

        if($_POST['email']== $fetched_control[$i][3]){
		$_POST['email']=NULL;
		echo("<script>alert('Cette adresse est déjà utilisée, merci d'en choisir une autre');</script>");
	}
}

	 if ($_POST['email'] != NULL && !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{5,30})$#", $_POST['password']) && !preg_match("#^$#", $_POST['password']) && $_POST['password']!=$_POST['verif-password']) {

                echo ("<script>alert('Le mot de passe n'est pas identique lors de la réécriture ou comporte trop peu de caractères');</script>");

        }


	else if ($_POST['email'] != NULL && !preg_match("#/^[a-zéèàùûêâôë][a-zéèàùûêâôë\- \']*$/i#", $_POST['adresse']) && !preg_match("#^$#", $_POST['adresse']) ){
                $_POST['adresse']=NULL;
                echo ("<script>alert('Merci d\'entrer une adresse valide');</script>");

        }



	else if ($_POST['email'] != NULL && !preg_match("#^[0-9]{5}$#", $_POST['code-postal']) && !preg_match("#^$#", $_POST['code-postal']) ){
		$_POST['code-postal']=NULL;
    		echo ("<script>alert('Merci d\'entrer un code postal valide');</script>");

	}

        else if ($_POST['email'] != NULL && !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{5,30})$#", $_POST['ville']) && !preg_match("#^$#", $_POST['ville']) ) {

                echo ("<script>alert('Merci d\'entrer un nom de ville composé uniquement de lettres);</script>");

        }


	else if ($_POST['email']!=NULL && !preg_match("#^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$#", $_POST['tel']) && !preg_match("#^$#", $_POST['tel']) ){
		$_POST['tel']=NULL;
                echo ("<script>alert('Merci d\'entrer un numéro de téléphone valide');</script>");

       }


//^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$


$dbh=NULL;
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

		<input type="text" name="prenom" required class="form-control input-lg" placeholder="Indiquez votre prenom" value="<?= @$_POST['prenom'];?>"/>

                <input type="text" name="nom" required class="form-control input-lg" placeholder="Indiquez votre nom" value="<?= @$_POST['nom'];?>"/>


          	<input type="email" name="email" placeholder="Entrez votre mail" required class="form-control input-lg" value="<?= @$_POST['email'];?>" />

          	<input name="password" type="password" class="form-control input-lg" id="password" placeholder="Entrez un mot de passe" required="" />
	        <input name="verif-password" type="password" class="form-control input-lg" id="password" placeholder="Confirmez le mot de passe" required="" />


                <input type="text" name="adresse" required class="form-control input-lg" placeholder="Indiquez votre adresse" value="<?= @$_POST['adresse'];?>"/>

                <input type="text" maxlength="5"  name="code-postal" required class="form-control input-lg" placeholder="Indiquez votre code postal" value="<?= @$_POST['code-postal'];?>"/>

                <input type="text" name="ville" required class="form-control input-lg" placeholder="Indiquez votre ville de résidence" value="<?= @$_POST['ville'];?>"/>

                <input type="text" maxlength="18"  name="tel" required class="form-control input-lg" placeholder="Numéro de téléphone:" value="<?= @$_POST['tel'];?>"/>

          	<div class="pwstength_viewport_progress"></div>


         	 <button type="submit" name="go" class="btn btn-lg btn-primary btn-block">S'inscrire</button>
         	<!-- <div>
           		 <a href="create.php">Cr&eacute;er un compte</a> ou <a href="#">R&eacute;initialiser le mot de passe</a>
         	 </div>
-->
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

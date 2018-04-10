
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
  $chargement=0;
} else{
  $chargement=1;
}

if(!isset($_POST['adresse'])){
  $_POST['adresse']=NULL;
}
if(!isset($_POST['password'])){
  $_POST['password']=NULL;
}
if(!isset($_POST['code-postal'])){
  $_POST['code-postal']=NULL;
}
if(!isset($_POST['tel'])){
  $_POST['tel']=NULL;
}
if(!isset($_POST['ville'])){
  $_POST['ville']=NULL;
}

$dbh = DBmanager::getInstance();

$prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
$prep_fetch->execute();
$alert= NULL;
$mdp = NULL;
$succes= 0;
$fetched_control = $prep_fetch->fetchAll();
//print_r ($fetched_control);
$nb = $prep_fetch->rowCount();
$inscription = 7;
$analyse= htmlspecialchars($_POST['adresse']);



if ( !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,30})$#", $_POST['prenom']) && !preg_match("#^$#", $_POST['prenom']) ){
                $_POST['prenom']=NULL;
                $alert = "<div class='alert alert-danger' role='alert'>Merci d'entrer un prénom composé uniquement de lettres</div>";
                $inscription--;
        }

else if ( !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{1,30})$#", $_POST['nom']) && !preg_match("#^$#", $_POST['nom']) ){
                $_POST['nom']=NULL;
                $alert ="<div class='alert alert-danger' role='alert'>Merci d'entrer un nom composé uniquement de lettres</div>";
                $inscription--;

        }






else for($i=0;$i<$nb;$i++){

        if($_POST['email']== $fetched_control[$i][3] && $_POST['email']!=NULL){
		$_POST['email']=NULL;
		$alert = "<div class='alert alert-danger' role='alert'>Cette adresse mail est déjà utilisée, merci d'en choisir une autre</div>";
    $inscription--;

	}
        else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && $_POST['email']!=NULL && !preg_match('#.[a-z]{2,4}$#', $_POST['email'])) {
          $_POST['email']=NULL;
      		$alert = "<div class='alert alert-danger' role='alert'>Merci de rentrer une adresse mail au format conventionnel</div>";
          $inscription--;
        }

}

	 if ($_POST['email'] != NULL && !preg_match("#^$#", $_POST['password']) && $_POST['password']!=$_POST['verif-password'] && strlen($_POST['password'])>5 && strlen($_POST['password'])<100) {

                $alert ="<div class='alert alert-danger' role='alert'>Le mot de passe n'est pas identique lors de la réécriture ou comporte trop peu de caractères, il doit avoir au moins 6 caractères</div>";
                $inscription--;

        }


	else if ($_POST['email'] != NULL && $_POST['adresse'] != $analyse && !preg_match("#^$#", $_POST['adresse']) ){
                $_POST['adresse']=NULL;
                $alert ="<div class='alert alert-danger' role='alert'>Merci d'entrer une adresse valide</div>";
                $inscription--;

        }



	else if ($_POST['email'] != NULL && !preg_match("#^[0-9]{5}$#", $_POST['code-postal']) && !preg_match("#^$#", $_POST['code-postal']) ){
		$_POST['code-postal']=NULL;
    		$alert ="<div class='alert alert-danger' role='alert'>Merci d'entrer un code postal valide</div>";
        $inscription--;

	}

        else if ($_POST['email'] != NULL && !preg_match("#^([a-zA-Z'àâéèêôùûçÀÂÉÈÔÙÛÇ[:blank:]-]{5,30})$#", $_POST['ville']) && !preg_match("#^$#", $_POST['ville']) ) {

                $alert ="<div class='alert alert-danger' role='alert'>Merci d'entrer un nom de ville composé uniquement de lettres</div>";
                $inscription--;

        }


	else if ($_POST['email']!=NULL && !preg_match("#^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$#", $_POST['tel']) && !preg_match("#^$#", $_POST['tel']) ){
		$_POST['tel']=NULL;
                $alert = "<div class='alert alert-danger' role='alert'>Merci d'entrer un numéro de téléphone valide</div>";
                $inscription--;

       }


     if($inscription==7 && $chargement==1){

         $mdp=password_hash($_POST['password'],PASSWORD_DEFAULT);

         $insertion = $dbh->prepare("INSERT INTO CLIENTS (prenom, nom, email ,password, adresse, codepostal, ville,
            telephone, credit, compte_actif, token_aleatoire, type_compte)
VALUES (:prenom, :nom, :email, :password, :adresse, :codepostal, :ville,
   :telephone, :credit, :compte_actif, :token_aleatoire, :type_compte)");

   $destinataire = $_POST['email'];
   $sujet = "Bienvenue chez cHost!";
          $token= time();
   $insertion->execute(array(
       'prenom' => $_POST['prenom'],
       'nom' => $_POST['nom'],
       'email' => $_POST['email'],
       'password' => $mdp,
       'adresse' => $_POST['adresse'],
       'codepostal' => $_POST['code-postal'],
       'ville' => $_POST['ville'],
       'telephone' => $_POST['tel'],
       'credit' => 0,
       'compte_actif' => 0,
       'token_aleatoire' => $token,
       'type_compte' => 0

   ));

   $alert = "<div class='alert alert-success' role='alert'>Le compte a bien été créé, merci de vérifier votre boite mail pour l'activation du compte</div>";
   $succes=1;
   MAILmanager::send($_POST['email'], "Bienvenue chez cHost!", "<h1>Bonjour</h1><p>Veuillez trouver ci-joint le lien d'activation : <a href='".SITE_URL."activer.php?token=$token'>ici</a></p></br></br> Si vous n'avez pas demand&eacute; d'inscription au sein de notre structure, ignorez ce message", true);


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
  <title>Inscription - cHost.fr</title>
  <link rel="icon" type="image/png" href="../images/icone.png" />
  <meta charset="utf-8">
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

            <?php echo ($alert); ?>

            <?php if ($succes==0):?>
		<input type="text" name="prenom" required class="form-control input-lg" placeholder="Indiquez votre prenom" value="<?= @$_POST['prenom'];?>"/>

                <input type="text" name="nom" required class="form-control input-lg" placeholder="Indiquez votre nom" value="<?= @$_POST['nom'];?>"/>


          	<input type="email" name="email" placeholder="Entrez votre mail" required class="form-control input-lg" value="<?= @$_POST['email'];?>" />

          	<input name="password" type="password" class="form-control input-lg" id="password" maxlength="" placeholder="Entrez un mot de passe " required="" />
	        <input name="verif-password" type="password" class="form-control input-lg" id="password" placeholder="Confirmez le mot de passe" required="" />


                <input type="text" name="adresse" required class="form-control input-lg" placeholder="Indiquez votre adresse" value="<?= @$_POST['adresse'];?>"/>

                <input type="text" maxlength="5"  name="code-postal" required class="form-control input-lg" placeholder="Indiquez votre code postal" value="<?= @$_POST['code-postal'];?>"/>

                <input type="text" name="ville" required class="form-control input-lg" placeholder="Indiquez votre ville de résidence" value="<?= @$_POST['ville'];?>"/>

                <input type="text" maxlength="18"  name="tel" required class="form-control input-lg" placeholder="Numéro de téléphone:" value="<?= @$_POST['tel'];?>"/>

          	<div class="pwstength_viewport_progress"></div>


         	 <button type="submit" name="go" class="btn btn-lg btn-primary btn-block">S'inscrire</button>

         <?php endif?>
         	<!-- <div>
           		 <a href="create.php">Cr&eacute;er un compte</a> ou <a href="#">R&eacute;initialiser le mot de passe</a>
         	 </div>
-->
        </form>


  <div class="form-links">
          <a href="../">Retour à l'accueil</a>
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

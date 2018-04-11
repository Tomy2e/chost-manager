<?php

  require_once('includes/autoload.php');


  $dbh = DBmanager::getInstance();

  if(!isset($_POST['email'])){
    $_POST['email']=NULL;
    $chargement=0;
  } else{
    $chargement=1;
  }

  $prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
  $prep_fetch->execute();
  $fetched = $prep_fetch->fetchAll();
  $nb = $prep_fetch->rowCount();


//  $token= $_GET['token'];
  $alert=NULL;
  $place=NULL;
  $destinataire=NULL;
  $empty=true;
  $button= "<button type='submit' name='go' class='btn btn-lg btn-primary btn-block' >Confirmer</button>";

  $form =  "<input type='email' name='email' placeholder='Entrez votre mail' required class='form-control input-lg'  />";


  for($i=0;$i<$nb;$i++){

      if($_POST['email']== $fetched[$i][3] && $_POST['email']!=NULL){
  $destinataire=$_POST['email'];
  $place=$i;
  $empty=false;
      }

    }


    if($place != NULL && $fetched[$place][10] == 0 ){
      $alert = "<div class='alert alert-danger' role='alert'>Vous n'avez pas encore validé votre email, vous ne pouvez donc pas encore modifier votre mot de passe</div>";
    //  $form = NULL;
    //  $button = NULL;
      $chargement--;
    }







    if($place==NULL && $chargement==1 && $_POST['email']!=NULL && $empty){
      $alert = "<div class='alert alert-danger' role='alert'>Cette adresse n'existe pas, merci de selectionner un compte valide</div>";
    }

    else if(!$empty && $chargement==1 && $fetched[$place][11]!=NULL && $fetched[$place][10]==1){
      $alert = "<div class='alert alert-danger' role='alert'>Vous avez déja effectué récemment une demande de modification de mot de passe, pensez à vérifier votre boîte mail</div>";
    }

    else if( !$empty && $chargement==1 && $fetched[$place][11]==NULL && $fetched[$place][10]==1){

      $destinataire = $_POST['email'];
      $sujet = "Modification de votre mot de passe";
      $token= time();



      $mail=$fetched[$place][3];
      $update = $dbh->prepare("UPDATE CLIENTS SET  token_aleatoire=? WHERE email=?");
      $update->execute(array($token,$mail));

      $body = "<html>
      <head>
       <title>Votre facture cHost</title>
      </head>
      <body>
      <center style='width:70%;margin:0 auto; border:1px solid black;padding-top:15px;padding-bottom:15px;margin-top:20px;'>
      <a href='".SITE_URL."'><img src='https://i.imgur.com/FhZkKAh.png'/></a><br />
      <hr>
      Bonjour ,<br /><br />
      Vous avez souhait&eacute; modifier votre mot de passe.<br />

      Veuillez trouver ci-joint le lien permettant de modifier votre mot de passe : <a href='".SITE_URL."change.php?token=$token'>ici</a></p></br></br> Si vous n'avez pas demand&eacute; d'inscription au sein de notre structure, ignorez ce message
      <br />
      Cordialement, l'&eacute;quipe cHost.
      </center>

      </body>
     </html>";

      $alert = "<div class='alert alert-success' role='alert'>Un mail avec un lien de réinitialisation vous a été envoyé</div>";
      MAILmanager::send($_POST['email'], $sujet, $body, true);
      $button=NULL;
      $form =NULL;
    }


?>


<!DOCTYPE HTML>

<html>
 <head>
  <link href="css/login.css" rel="stylesheet" id="bootstrap-css">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
  <script src="js/login.js"></script>
  <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

  <title>Réinitialisation du mot de passe - cHost.fr</title>
  <link rel="icon" type="image/png" href="../images/icone.png" />
  <meta charset="utf-8">
 </head>

 <body>

<div class="container">

  <div class="row" id="pwd-container">
    <div class="col-md-4"></div>

    <div class="col-md-4">
      <section class="login-form">
       <!--   <img src="http://i.imgur.com/RcmcLv4.png" class="img-responsive" alt="" />
        -->
        <form method="post" action="" role="login">

          <img src="images/logo.png" class="img-responsive" alt="" />

            <?php echo ($alert); ?>
            <?php echo ($form); ?>
            <?php echo ($button); ?>


          </form>


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

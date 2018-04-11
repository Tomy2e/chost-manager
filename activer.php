<?php

  require_once('includes/autoload.php');


  $dbh = DBmanager::getInstance();

  $prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
  $prep_fetch->execute();
  $fetched = $prep_fetch->fetchAll();
  $nb = $prep_fetch->rowCount();

  $token= $_GET['token'];
  $alert=NULL;
  $place=NULL;
  $button=NULL;
  $empty=true;
  $id=NULL;

  $temp_id=NULL;


  for($i=0;$i<$nb;$i++){
    $temp_id=$fetched[$i][0];

    if($token== $fetched[$i][11]){
      $place=$i;
      $id=$fetched[$i][0];
      $empty=false;
    }


  }

  if($empty || time()-604800 >$token){
    $alert = "<div class='alert alert-danger' role='alert'>Le lien de validation n'est pas ou plus valide. Si votre compte a été créé il y a plus d'une semaine sans être validé, il a été supprimé.</div>";

    if(time()-604800 >$token && !$empty){
      $update = $dbh->prepare("DELETE FROM CLIENTS  WHERE id_client=?");
      $update->execute(array($id));
    }
  }

  else if ($fetched[$place][10]==1){

    $alert = "<div class='alert alert-danger' role='alert'>Votre mail a déja été validé!</div>";

  }

  else if(!$empty && $fetched[$place][10]==0){
    $mail=$fetched[$place][3];
    $update = $dbh->prepare("UPDATE CLIENTS SET compte_actif=1, token_aleatoire=NULL WHERE email=?");
    $update->execute(array($mail));

    $alert = "<div class='alert alert-success' role='alert'>L'adresse mail ".$mail." a bien été vérifiée, bienvenue parmi les utilisateurs officiels de cHost! La connexion au service client est désormais disponible</div>";
    $button=  "<a href='login.php' class='btn btn-lg btn-primary btn-block' role='button'>Connectez-vous!</a>";

  }

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

   <title>Activation du compte - cHost.fr</title>
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

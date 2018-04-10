<?php

  require_once('includes/autoload.php');


  $dbh = DBmanager::getInstance();

  $prep_fetch = $dbh->prepare("SELECT * FROM CLIENTS");
  $prep_fetch->execute();
  $fetched = $prep_fetch->fetchAll();
  $nb = $prep_fetch->rowCount();

  if(!isset($_POST['password'])){
    $_POST['password']=NULL;
    $_POST['verif-password']=NULL;
    $modif=0;
  }else{
    $modif=1;
  }

  $alert=NULL;




  $token= $_GET['token'];
  $empty=true;
  $place=NULL;
  $id=NULL;

  $button= "<button type='submit' name='go' class='btn btn-lg btn-primary btn-block' >Je modifie mon mot de passe</button>";

  $form = "<input name='password' type='password' class='form-control input-lg' id='password' maxlength='' placeholder='Entrez un mot de passe'  required='' />
<input name='verif-password' type='password' class='form-control input-lg' id='password' placeholder='Confirmez le mot de passe' required='' />";
  for($i=0;$i<$nb;$i++){
    if($token== $fetched[$i][11] ){
      $place=$i;
      $empty=false;
      $id=$fetched[$i][0];
    }

  }

  if($empty || time()-172800 >$token){
    $alert = "<div class='alert alert-danger' role='alert'>Le lien de validation n'est pas ou plus valide</div>";
    $form = NULL;
    $button = NULL;
    $modif--;
    if(time()-172800 >$token && !$empty){
      $update = $dbh->prepare("UPDATE CLIENTS SET  token_aleatoire=NULL WHERE email=?");
      $update->execute(array($id));
    }

  }


else if (  ($_POST['password']!=$_POST['verif-password']  || strlen($_POST['password'])<=5 || strlen($_POST['password'])>=100) && $_POST['password']!=NULL) {

               $alert ="<div class='alert alert-danger' role='alert'>Le mot de passe n'est pas identique lors de la réécriture ou comporte trop peu de caractères, il doit avoir au moins 6 caractères</div>";
               $modif--;

       }
  else if(!$empty && $fetched[$place][10] == 1 && $modif==1 && $_POST['password']==$_POST['verif-password'] && (strlen($_POST['password'])>5 && strlen($_POST['password'])<100)){
    $mail=$fetched[$place][3];
//    $update = $dbh->prepare("UPDATE CLIENTS SET compte_actif=1, token_aleatoire=NULL WHERE email=?");
//    $update->execute(array($mail));
    $form=NULL;
    $mdp=password_hash($_POST['password'],PASSWORD_DEFAULT);

    $update = $dbh->prepare("UPDATE CLIENTS SET  password=? , token_aleatoire=NULL WHERE email=?");
    $update->execute(array($mdp,$mail));

    $alert = "<div class='alert alert-success' role='alert'>Le mot de passe de votre compte a bien été modifiée. Tentez de vous en rappeler ;) </div>";
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
   <title>Réinitialisation du mot de passe - cHost.fr</title>
   <link rel="icon" type="image/png" href="../images/icone.png" />
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

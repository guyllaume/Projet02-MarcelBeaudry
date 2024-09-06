<!DOCTYPE html>
<html>
<head>
   <title><?php echo $strTitreApplication; ?></title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <link rel="stylesheet" type="text/css" href="style/style.css" /></head>
   <link rel="stylesheet" type="text/css" href="<?php echo $strNomFichierCSS; ?>" /></head>
<body>
<div class="container"> 
   <nav id="navEnTete">
      <?php echo $strTitreApplication; ?>
      <ul id="navList">
         <?php
         if(!$bIsConnected) {
         ?>
         <li><a href="index.php">Home</a></li>
         <li><a href="login.php">Login</a></li>
         <li><a href="signup.php">Signup</a></li>
         <?php
         }else{
         ?>
         <li><a href="annonces.php">Annonces</a></li>
         <li><a href="gestion.php">Gestionnaire</a></li>
         <li><a href="profil.php">Profil</a></li>
         <li><a href="index.php">Déconnexion</a></li>
         <?php
         }
         ?>
      </ul>
   </nav>

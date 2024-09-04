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
         <li><a href="index.php">Home</a></li>
         <li><a href="login.php">Login</a></li>
         <li><a href="signup.php">Signup</a></li>
      </ul>
   </nav>

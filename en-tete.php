<!DOCTYPE html>
<?php
session_start();
$bIsConnected = isset($_SESSION['user_id']);
?>
<head>
   <title><?php echo $strTitreApplication; ?></title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <link rel="stylesheet" type="text/css" href="style/style.css" />
   <link rel="stylesheet" type="text/css" href="<?php echo $strNomFichierCSS; ?>" />
</head>
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
         } else {
            // Vérifier si l'utilisateur est un administrateur (statut == 1)
            $isAdmin = isset($_SESSION['user_status']) && $_SESSION['user_status'] == 1;
            
            if ($isAdmin) {
            ?>
            <li><a href="annonces.php">Toutes les Annonces</a></li>
            <li><a href="gestionUtilisateur.php">Gestion des Utilisateurs</a></li>
            <li><a href="dataBaseNettoyage.php">Nettoyage de la Base de Données</a></li>
            <?php
            } else {
            ?>
            <li><a href="annonces.php">Annonces</a></li>
            <li><a href="gestion.php">Gestionnaire</a></li>
            <li><a href="profil.php">Profil</a></li>
            <?php
            }
            ?>
            <li><a href="logout.php">Déconnexion</a></li>
         <?php
         }
         ?>
      </ul>
   </nav>
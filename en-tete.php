<!DOCTYPE html>
<?php
session_start();
$bIsConnected = isset($_SESSION['user_id']);

if ($bIsConnected) {
    require_once 'classe-mysql.php';
    require_once '424x-cgodin-qc-ca.php';
    require_once 'db_connect.php';

    $conn = connectDB();
    
    $query = "SELECT Statut, Nom, Prenom FROM utilisateurs WHERE NoUtilisateur = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_status'] = $user['Statut'];
        if (empty($user['Nom']) || empty($user['Prenom'])) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            if ($currentPage != 'profil.php' && $user['Statut'] != 1) {
               error_log("Redirection vers profil.php pour l'utilisateur ID: " . $_SESSION['user_id']);
               header('Location: profil.php');
               exit();
            }
        }
    }
}
?>
<head>
   <title>Petites annonces GG</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <link rel="stylesheet" type="text/css" href="style/style.css" />
   <link rel="stylesheet" type="text/css" href="<?php echo $strNomFichierCSS; ?>" />
</head>
<body>
<div class="container">
   <nav id="navEnTete">
      <div class="logo-container">
         <img class="logo" src="images/logo.jpeg" />
         <span class="titre">Petites annonces GG</span>
         <?php if($bIsConnected && !empty($user['Nom']) && !empty($user['Prenom'])) { echo '<span class="user-connected">Bonjour ' . $user['Prenom'] . ' ' . $user['Nom'] . '</span>'; }?>
      </div>
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
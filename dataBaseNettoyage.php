<?php
$strNomFichierCSS = 'style/databaseNettoyage.css';
$bIsConnected = isset($_SESSION['user_id']);
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] != 1) {
    header('Location: index.php');
    exit();
}

$conn = connectDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clean_users'])) {
        // Retrait des utilisateurs non confirmés après 3 mois
        $query = "DELETE FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 3 MONTH)";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $usersDeleted = $stmt->rowCount();
        $message .= "$usersDeleted utilisateurs non confirmés ont été supprimés.<br>";
    }

    if (isset($_POST['clean_announcements'])) {
        // Retrait physique des annonces retirées logiquement
        $query = "DELETE FROM annonces WHERE Etat = 3"; // Supposons que 3 représente l'état "retiré"
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $announcesDeleted = $stmt->rowCount();
        $message .= "$announcesDeleted annonces retirées ont été supprimées définitivement.<br>";
    }
}

// Compter les utilisateurs et annonces à nettoyer
$queryUsers = "SELECT COUNT(*) FROM utilisateurs WHERE Statut = 0 AND Creation < DATE_SUB(NOW(), INTERVAL 3 MONTH)";
$stmtUsers = $conn->query($queryUsers);
$usersToDelete = $stmtUsers->fetchColumn();

$queryAnnounces = "SELECT COUNT(*) FROM annonces WHERE Etat = 3";
$stmtAnnounces = $conn->query($queryAnnounces);
$announcesToDelete = $stmtAnnounces->fetchColumn();
?>

<div class="contenu top">
    <h1>Nettoyage de la Base de Données</h1>
    
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?');">
        <div>
            <h2>Utilisateurs non confirmés</h2>
            <p>Il y a actuellement <?php echo $usersToDelete; ?> utilisateurs non confirmés depuis plus de 3 mois.</p>
            <button type="submit" name="clean_users">Nettoyer les utilisateurs</button>
        </div>
        
        <div>
            <h2>Annonces retirées</h2>
            <p>Il y a actuellement <?php echo $announcesToDelete; ?> annonces retirées logiquement.</p>
            <button type="submit" name="clean_announcements">Nettoyer les annonces</button>
        </div>
    </form>
</div>

<?php
require_once 'pied-page.php';
?>
<?php
$strNomFichierCSS = 'style/style.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'classe-mysql.php';

$message = '';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($email) || empty($token)) {
    $message = "Lien de confirmation invalide.";
} else {
    $mysql = new mysql("PJF_MARCELBEAUDRY", "424x-cgodin-qc-ca.php");

    $query = "SELECT * FROM utilisateurs WHERE Courriel = ? AND ConfirmationToken = ? AND Statut = 0";
    $stmt = mysqli_prepare($mysql->cBD, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $updateQuery = "UPDATE utilisateurs SET Statut = 9, ConfirmationToken = NULL WHERE Courriel = ?";
        $updateStmt = mysqli_prepare($mysql->cBD, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "s", $email);
        
        if (mysqli_stmt_execute($updateStmt)) {
            $message = "Votre compte a été confirmé avec succès. Vous pouvez maintenant vous connecter.";
        } else {
            $message = "Une erreur est survenue lors de la confirmation de votre compte.";
        }
    } else {
        $message = "Lien de confirmation invalide ou déjà utilisé.";
    }

    $mysql->deconnexion();
}
?>

<div class="contenu">
    <div class="card">
        <div class="card-header">
            <h1>Confirmation du compte</h1>
        </div>
        <div class="card-body">
            <p><?php echo $message; ?></p>
            <?php if (strpos($message, "succès") !== false): ?>
                <a href="login.php" class="btn btn-primary">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once 'pied-page.php';
?>
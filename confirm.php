<?php
require_once '424x-cgodin-qc-ca.php';
require_once 'classe-mysql.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($email) || empty($token)) {
    die("Lien de confirmation invalide.");
}

$mysql = new mysql("PJF_MARCELBEAUDRY", "424x-cgodin-qc-ca.php");

$query = "SELECT * FROM utilisateurs WHERE Courriel = ? AND ConfirmationToken = ? AND Statut = 0";
$stmt = mysqli_prepare($mysql->cBD, $query);
mysqli_stmt_bind_param($stmt, "ss", $email, $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $updateQuery = "UPDATE utilisateurs SET Statut = 1, ConfirmationToken = NULL WHERE Courriel = ?";
    $updateStmt = mysqli_prepare($mysql->cBD, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "s", $email);
    
    if (mysqli_stmt_execute($updateStmt)) {
        echo "Votre compte a été confirmé avec succès. Vous pouvez maintenant vous connecter.";
    } else {
        echo "Une erreur est survenue lors de la confirmation de votre compte.";
    }
} else {
    echo "Lien de confirmation invalide ou déjà utilisé.";
}

$mysql->deconnexion();
?>
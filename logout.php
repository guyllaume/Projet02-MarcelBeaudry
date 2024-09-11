<?php
session_start();
require_once 'librairies-communes-2018-mm-jj.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['connexion_id'])) {
    try {
        $conn = connectDB();

        // Enregistrer la déconnexion
        $stmt = $conn->prepare("UPDATE connexions SET Deconnexion = NOW() WHERE NoConnexion = :connexionId");
        $stmt->bindParam(':connexionId', $_SESSION['connexion_id']);
        $stmt->execute();

        // Fermer la connexion à la base de données
        $conn = null;
    } catch (PDOException $e) {
        // Log l'erreur ou affichez-la pour le débogage
        error_log("Erreur de déconnexion : " . $e->getMessage());
    }
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php?message=' . urlencode("Vous avez été déconnecté avec succès."));
exit();
?>
<?php
require_once '424x-cgodin-qc-ca.php';

// Fonction pour exécuter une requête SQL
function executeQuery($conn, $query) {
    try {
        $conn->exec($query);
        echo "Requête exécutée avec succès : " . substr($query, 0, 50) . "...<br>";
    } catch(PDOException $e) {
        echo "Erreur lors de l'exécution de la requête : " . $e->getMessage() . "<br>";
    }
}

try {
    // Connexion à MySQL sans sélectionner de base de données
    $conn = new PDO("mysql:host=localhost", $strNomAdmin, $strMotPasseAdmin);
    
    // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Supprimer la base de données si elle existe
    $conn->exec("DROP DATABASE IF EXISTS PJF_MARCELBEAUDRY");
    echo "Base de données supprimée si elle existait.<br>";
    
    // Créer la base de données
    $conn->exec("CREATE DATABASE PJF_MARCELBEAUDRY");
    echo "Base de données créée.<br>";
    
    // Sélectionner la base de données
    $conn->exec("USE PJF_MARCELBEAUDRY");
    
    // Définir le script SQL
    $sql = "
    -- Table utilisateurs
    CREATE TABLE utilisateurs (
        NoUtilisateur INT AUTO_INCREMENT PRIMARY KEY,
        Courriel VARCHAR(50) UNIQUE NOT NULL,
        MotDePasse VARCHAR(15) NOT NULL,
        Creation DATETIME NOT NULL,
        NbConnexions INT DEFAULT 0,
        Statut INT DEFAULT 0,
        NoEmpl INT,
        Nom VARCHAR(25),
        Prenom VARCHAR(20),
        NoTelMaison VARCHAR(15),
        NoTelTravail VARCHAR(21),
        NoTelCellulaire VARCHAR(15),
        Modification DATETIME,
        AutresInfos VARCHAR(50),
        ConfirmationToken VARCHAR(32),
        ResetToken VARCHAR(64)
    );

    -- Table connexions
    CREATE TABLE connexions (
        NoConnexion INT AUTO_INCREMENT PRIMARY KEY,
        NoUtilisateur INT,
        Connexion DATETIME NOT NULL,
        Deconnexion DATETIME,
        FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur)
    );

    -- Table categories
    CREATE TABLE categories (
        NoCategorie INT AUTO_INCREMENT PRIMARY KEY,
        Description VARCHAR(20) NOT NULL
    );

    -- Table annonces
    CREATE TABLE annonces (
        NoAnnonce INT AUTO_INCREMENT PRIMARY KEY,
        NoUtilisateur INT,
        Parution DATETIME NOT NULL,
        Categorie INT,
        DescriptionAbregee VARCHAR(50) NOT NULL,
        DescriptionComplete VARCHAR(250) NOT NULL,
        Prix DECIMAL(7,2) DEFAULT 0.00,
        Photo VARCHAR(50),
        MiseAJour DATETIME,
        Etat INT DEFAULT 1,
        FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur),
        FOREIGN KEY (Categorie) REFERENCES categories(NoCategorie)
    );

    -- Insertion des catégories prédéfinies
    INSERT INTO categories (NoCategorie, Description) VALUES
    (1, 'Location'),
    (2, 'Recherche'),
    (3, 'À vendre'),
    (4, 'À donner'),
    (5, 'Service offert'),
    (6, 'Autre');
    ";
    
    // Exécuter le script SQL
    $queries = explode(';', $sql);
    foreach($queries as $query) {
        if(trim($query) != '') {
            executeQuery($conn, $query);
        }
    }
    
    echo "La base de données a été créée et initialisée avec succès.";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Fermer la connexion
$conn = null;
?>
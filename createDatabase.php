<?php
require_once '424x-cgodin-qc-ca.php';

function connectDB() {
    global $strNomAdmin, $strMotPasseAdmin;
    $cBD = mysqli_connect("localhost", $strNomAdmin, $strMotPasseAdmin);

    if ($cBD === false) {
        die("Problème de connexion… Message d'erreur retourné par PHP: " . mysqli_connect_error());
    }
    return $cBD;
}


// Créer une connexion à la base de données
$conn = connectDB();

// Vérifier si la base de données existe déjà
$sql = "SHOW DATABASES LIKE 'PJF_MARCELBEAUDRY'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "Base de données existe déja<br>";
}

// Créer la base de données
$sql = "CREATE DATABASE IF NOT EXISTS PJF_MARCELBEAUDRY";
if ($conn->query($sql) === TRUE) {
    echo "Base de données créée ou déja créée<br>";
} else {
    echo "Erreur lors de la création de la base de données <br>";
}

// Choisir la nouvelle base de données
$conn->select_db("PJF_MARCELBEAUDRY");

// Créer la table "utilisateurs"
$sql = "CREATE TABLE IF NOT EXISTS utilisateurs (
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
)";
if ($conn->query($sql) === TRUE) {
    echo "Table utilisateurs créée ou déja créée<br>";
} else {
    echo "Erreur lors de la création de la table utilisateurs <br>";
}

// Créer la table "connexions"
$sql = "CREATE TABLE IF NOT EXISTS connexions (
    NoConnexion INT AUTO_INCREMENT PRIMARY KEY,
    NoUtilisateur INT NOT NULL,
    Connexion DATETIME NOT NULL,
    Deconnexion DATETIME,
    FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table connexions créée ou déja créée<br>";
} else {
    echo "Erreur lors de la création de la table connexions <br>";
}

// Vérifier si la table "categories" existe déjà
$tableExistsQuery = "SHOW TABLES LIKE 'categories'";
$result = $conn->query($tableExistsQuery);

if ($result->num_rows > 0) {
    echo "Table categories existe déja.<br>";
}else{
    //Créer la table "categories"
    $sql = "CREATE TABLE categories (
        NoCategorie INT AUTO_INCREMENT PRIMARY KEY,
        Description VARCHAR(20) NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        echo "Table categories créée ou déja créée<br>";

        //Insertion des catégories dans la table categories - Assure l'insertion seuelement a la creation de la table
        $sql = "INSERT INTO categories (Description) VALUES 
        ('Location'),
        ('Recherche'),
        ('À vendre'),
        ('À donner'),
        ('Service offert'),
        ('Autre');
        ";

        if($conn->query($sql) === TRUE){
            echo "Insertion des catégories dans la table categories reussie<br>";
        }else{
            echo "Erreur lors de l'insertion des catégories dans la table categories<br>";
        }
    } else {
        echo "Erreur lors de la création de la table categories <br>";
    }
}

//Créer la table "annonces"
$sql = "CREATE TABLE IF NOT EXISTS annonces (
    NoAnnonce INT AUTO_INCREMENT PRIMARY KEY,
    NoUtilisateur INT NOT NULL,
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
)";
if ($conn->query($sql) === TRUE) {
    echo "Table annonces créée ou déja créée<br>";
} else {
    echo "Erreur lors de la création de la table annonces <br>";
}


echo "Création de la base de données terminée <br>";

// Fermer explicitement la connexion au serveur
$conn = null;


?>
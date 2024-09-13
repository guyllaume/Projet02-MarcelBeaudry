<?php
require_once '424x-cgodin-qc-ca.php';

function connectDB() {
    global $strNomAdmin, $strMotPasseAdmin;
    try {
        $conn = new PDO("mysql:host=localhost;dbname=PJF_MARCELBEAUDRY", $strNomAdmin, $strMotPasseAdmin);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}


function importCSV($fileName, $tableName, $columns) {
    // Établir une connexion à la base de données
    $conn = connectDB();
    
    // Tenter d'ouvrir le fichier CSV
    if (($handle = fopen($fileName, "r")) !== FALSE) {
        // Ignorer la première ligne si c'est un en-tête
        fgetcsv($handle, 1000, ",");
        
        // Parcourir chaque ligne du fichier CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Créer une chaîne de placeholders pour la requête SQL (ex: "?, ?, ?")
            $placeholders = implode(", ", array_fill(0, count($columns), "?"));
            
            // Créer la partie "UPDATE" de la requête SQL pour gérer les doublons
            $updateSet = implode(", ", array_map(function($col) { 
                return "$col = VALUES($col)"; 
            }, $columns));
            
            // Construire la requête SQL complète
            $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") 
                    VALUES ($placeholders) 
                    ON DUPLICATE KEY UPDATE $updateSet";
            
            // Préparer la requête SQL
            $stmt = $conn->prepare($sql);
            
            try {
                // Exécuter la requête avec les données de la ligne courante
                $stmt->execute($data);
                //echo "Ligne traitée avec succès.<br>";
            } catch (PDOException $e) {
                // En cas d'erreur, afficher un message mais continuer le traitement
                echo "Erreur lors du traitement de la ligne : " . $e->getMessage() . "<br>";
                continue;
            }
        }
        // Fermer le fichier CSV
        fclose($handle);
        echo "Importation terminée pour $tableName<br>";
    } else {
        echo "Erreur lors de l'ouverture du fichier $fileName<br>";
    }
    
    // Fermer la connexion à la base de données
    $conn = null;
}


// Importer les utilisateurs
importCSV("csv_data/utilisateurs.csv", "utilisateurs", ["Courriel", "MotDePasse", "Creation", "NbConnexions", "Statut", "NoEmpl", "Nom", "Prenom", "NoTelMaison", "NoTelTravail", "NoTelCellulaire", "Modification", "AutresInfos"]);

// Importer les connexions
importCSV("csv_data/connexions.csv", "connexions", ["NoUtilisateur", "Connexion", "Deconnexion"]);

// Importer les annonces
importCSV("csv_data/annonces.csv", "annonces", ["NoUtilisateur", "Parution", "Categorie", "DescriptionAbregee", "DescriptionComplete", "Prix", "Photo", "MiseAJour", "Etat"]);

echo "Importation terminée";

// Fermer explicitement la connexion au serveur
$conn = null;


?>
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
//fermer connexion au serveur

function importCSV($fileName, $tableName, $columns) {
    $conn = connectDB();
    
    if (($handle = fopen($fileName, "r")) !== FALSE) {
        // Ignorer la première ligne si c'est un en-tête
        fgetcsv($handle, 1000, ",");
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") VALUES (" . implode(", ", array_fill(0, count($columns), "?")) . ")";
            $stmt = $conn->prepare($sql);
            $stmt->execute($data);
        }
        fclose($handle);
        echo "Importation réussie pour $tableName<br>";
    } else {
        echo "Erreur lors de l'ouverture du fichier $fileName<br>";
    }
}


// Importer les utilisateurs
importCSV("csv_data/utilisateurs.csv", "utilisateurs", ["Courriel", "MotDePasse", "Creation", "NbConnexions", "Statut", "NoEmpl", "Nom", "Prenom", "NoTelMaison", "NoTelTravail", "NoTelCellulaire", "Modification", "AutresInfos"]);

// Importer les connexions
importCSV("csv_data/connexions.csv", "connexions", ["NoUtilisateur", "Connexion", "Deconnexion"]);

// Importer les annonces
importCSV("csv_data/annonces.csv", "annonces", ["NoUtilisateur", "Parution", "Categorie", "DescriptionAbregee", "DescriptionComplete", "Prix", "Photo", "MiseAJour", "Etat"]);

echo "Importation terminée";

?>
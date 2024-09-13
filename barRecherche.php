<?php
require_once 'librairies-communes-2018-mm-jj.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'classe-mysql.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

$conn = connectDB();

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    $params = [':query' => $query];
    
    $sql = "SELECT DISTINCT a.DescriptionAbregee, a.NoAnnonce, a.DescriptionComplete, a.Photo,
                   u.Nom AS NomAuteur, u.Prenom AS PrenomAuteur, c.Description AS NomCategorie,
                   a.Parution
            FROM annonces a
            JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
            JOIN categories c ON a.Categorie = c.NoCategorie
            WHERE (LOWER(a.DescriptionAbregee) LIKE LOWER(:query) 
               OR LOWER(a.DescriptionComplete) LIKE LOWER(:query)
               OR LOWER(CONCAT(u.Prenom, ' ', u.Nom)) LIKE LOWER(:query)
               OR LOWER(c.Description) LIKE LOWER(:query))";

    if (isset($_GET['dateDebut'])) {
        $sql .= " AND a.Parution >= :dateDebut";
        $params[':dateDebut'] = $_GET['dateDebut'];
    }
    
    if (isset($_GET['dateFin'])) {
        $sql .= " AND a.Parution <= :dateFin";
        $params[':dateFin'] = $_GET['dateFin'];
    }
    
    $sql .= " LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} else {
    echo json_encode([]);
}
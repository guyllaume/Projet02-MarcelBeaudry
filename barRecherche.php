<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$conn = connectDB();

if (isset($_GET['query'])) {
    $queryWords = explode(' ', $_GET['query']);
    $params = [];
    $conditions = [];

    foreach ($queryWords as $index => $word) {
        $queryParam = ':query' . $index;
        $params[$queryParam] = '%' . $word . '%';
        $conditions[] = "(LOWER(a.DescriptionAbregee) LIKE LOWER($queryParam) 
                         OR LOWER(a.DescriptionComplete) LIKE LOWER($queryParam)
                         OR LOWER(CONCAT(u.Prenom, ' ', u.Nom)) LIKE LOWER($queryParam)
                         OR LOWER(c.Description) LIKE LOWER($queryParam))";
    }

    $sql = "SELECT DISTINCT a.DescriptionAbregee, a.NoAnnonce, a.DescriptionComplete, a.Photo,
                   u.Nom AS NomAuteur, u.Prenom AS PrenomAuteur, c.Description AS NomCategorie,
                   a.Parution, a.MiseAJour, u.NoTelMaison, u.NoTelTravail, u.NoTelCellulaire
            FROM annonces a
            JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
            JOIN categories c ON a.Categorie = c.NoCategorie
            WHERE " . implode(' AND ', $conditions);

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
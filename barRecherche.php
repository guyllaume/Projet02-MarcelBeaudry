<?php
require_once 'librairies-communes-2018-mm-jj.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'classe-mysql.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

$conn = connectDB();

if (isset($_GET['query'])) {
    $query = '%' . $_GET['query'] . '%';
    
    $sql = "SELECT NoAnnonce, DescriptionAbregee, DescriptionComplete, Photo 
        FROM annonces 
        WHERE LOWER(DescriptionAbregee) LIKE LOWER(:query) 
        OR LOWER(DescriptionComplete) LIKE LOWER(:query) 
        LIMIT 5";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':query', $query, PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} else {
    echo json_encode([]);
}
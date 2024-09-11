<?php
$strNomFichierCSS = 'style/style.css';
$bIsConnected = isset($_SESSION['user_id']);
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';
function displayValue($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] != 1) {
    header('Location: index.php');
    exit();
}

$conn = connectDB();

// Récupérer tous les utilisateurs triés par nom et prénom
$query = "SELECT NoUtilisateur, Courriel, Creation, NbConnexions, Statut, NoEmpl, Nom, Prenom, NoTelMaison, NoTelTravail, NoTelCellulaire, Modification FROM utilisateurs ORDER BY Nom, Prenom";
$stmt = $conn->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour obtenir les 5 dernières connexions/déconnexions
function getLastConnections($conn, $userId) {
    $query = "SELECT Connexion, Deconnexion FROM connexions WHERE NoUtilisateur = :userId ORDER BY Connexion DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir le nombre d'annonces par état
function getAnnouncesCount($conn, $userId) {
    $query = "SELECT Etat, COUNT(*) as count FROM annonces WHERE NoUtilisateur = :userId GROUP BY Etat";
    $stmt = $conn->prepare($query);
    $stmt->execute(['userId' => $userId]);
    $result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    return [
        'actives' => $result[1] ?? 0,
        'inactives' => $result[2] ?? 0,
        'retirees' => $result[3] ?? 0
    ];
}

?>

<div class="contenu">
    <h1>Gestion des Utilisateurs</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Courriel</th>
                    <th>Création</th>
                    <th>Nb Connexions</th>
                    <th>Statut</th>
                    <th>No Empl</th>
                    <th>Tél. Maison</th>
                    <th>Tél. Travail</th>
                    <th>Tél. Cellulaire</th>
                    <th>Dernière Modification</th>
                    <th>Dernières Connexions</th>
                    <th>Annonces</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= displayValue($user['NoUtilisateur']) ?></td>
                    <td><?= displayValue($user['Nom']) ?></td>
                    <td><?= displayValue($user['Prenom']) ?></td>
                    <td><?= displayValue($user['Courriel']) ?></td>
                    <td><?= displayValue($user['Creation']) ?></td>
                    <td><?= displayValue($user['NbConnexions']) ?></td>
                    <td><?= displayValue($user['Statut']) ?></td>
                    <td><?= displayValue($user['NoEmpl']) ?></td>
                    <td><?= displayValue($user['NoTelMaison']) ?></td>
                    <td><?= displayValue($user['NoTelTravail']) ?></td>
                    <td><?= displayValue($user['NoTelCellulaire']) ?></td>
                    <td><?= displayValue($user['Modification']) ?></td>
                    <td>
                        <?php
                        $connections = getLastConnections($conn, $user['NoUtilisateur']);
                        foreach ($connections as $connection) {
                            echo "Connexion: " . displayValue($connection['Connexion']) . "<br>";
                            echo "Déconnexion: " . displayValue($connection['Deconnexion'] ?? 'N/A') . "<br>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $announcesCount = getAnnouncesCount($conn, $user['NoUtilisateur']);
                        echo "Actives: " . $announcesCount['actives'] . "<br>";
                        echo "Inactives: " . $announcesCount['inactives'] . "<br>";
                        echo "Retirées: " . $announcesCount['retirees'];
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'pied-page.php';
?>
<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/style.css';
$bIsConnected = isset($_SESSION['user_id']); // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu">
    hello
    </div>

<?php
require_once 'pied-page.php';

?>
    
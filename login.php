<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
// Inclure les fichiers nécessaires
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';

// Fonction pour se connecter à la base de données
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

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $conn = connectDB();

    // Préparer et exécuter la requête pour vérifier les identifiants
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['MotDePasse'])) {
        // Identifiants corrects, rediriger vers index.php
        header('Location: index.php');
        exit();
    } else {
        // Identifiants incorrects, afficher un message d'erreur
        $erreur = "Email ou mot de passe incorrect.";
    }

    // Fermer la connexion à la base de données
    $conn = null;
}
?>

<!-- Code HTML existant pour le formulaire -->
<div class="contenu">
    <div class="card">
        <div class="card-header">
            <h1>Login</h1>
        </div>
        <div class="card-body">
            <form action="login.php" method="post">
                <div class="form-group">
                    <div class="form-subgroup">
                        <label for="email">Email</label>
                        <div class="input-group">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <span class="error" id="errorEmail">&nbsp;</span>
                        </div>
                    </div>
                    <div class="form-subgroup">
                        <label for="password">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="error" id="errorPassword">&nbsp;</span>
                        </div>
                    </div>
                    <a id="forgotPassword" href="forgotPassword.php">Mot de passe oublié?</a>
                    <button type="submit" id="btnSubmit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <?php if (isset($erreur)) echo "<p class='error'>$erreur</p>"; ?>
        </div>
    </div>
</div>

<?php
require_once 'pied-page.php';
?>

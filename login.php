<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

function customHash($password) {
    return substr(hash('sha256', $password), 0, 15);
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

    if ($user) {
        // Première tentative : vérifier le mot de passe tel quel
        if ($password === $user['MotDePasse']) {
            $passwordMatch = true;
        } else {
            // Deuxième tentative : vérifier avec le hash
            $passwordMatch = (customHash($password) === $user['MotDePasse']);
        }
    
        if ($passwordMatch) {
            // Vérifier le statut du compte
            if ($user['Statut'] != 0) {
                // Compte activé, enregistrer la connexion
                $_SESSION['user_id'] = $user['NoUtilisateur'];
                $_SESSION['user_status'] = $user['Statut'];

                // Enregistrer la connexion dans la table connexions
                $stmtConn = $conn->prepare("INSERT INTO connexions (NoUtilisateur, Connexion) VALUES (:userId, NOW())");
                $stmtConn->bindParam(':userId', $user['NoUtilisateur']);
                $stmtConn->execute();

                // Récupérer l'ID de la connexion pour la déconnexion ultérieure
                $_SESSION['connexion_id'] = $conn->lastInsertId();

                // Incrémenter NbConnexions dans la table utilisateurs
                $stmtUpdate = $conn->prepare("UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = :userId");
                $stmtUpdate->bindParam(':userId', $user['NoUtilisateur']);
                $stmtUpdate->execute();

                // Vérifier si le nom et le prénom sont enregistrés
                if (empty($user['Nom']) || empty($user['Prenom'])) {
                    header('Location: profil.php');
                } else {
                    header('Location: annonces.php');
                }
                exit();
            } else {
                $erreur = "Votre compte n'a pas encore été activé. Veuillez vérifier votre boîte de courriel pour le lien d'activation.";
            }
        } else {
            $erreur = "Le mot de passe ne correspond pas.";
        }
    } else {
        $erreur = "Aucun utilisateur trouvé avec cet email.";
    }

    // Fermer la connexion à la base de données
    $conn = null;
}

// Vérifier s'il y a un message de déconnexion
if (isset($_GET['message'])) {
    $message = $_GET['message'];
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

<script>
    document.getElementById('btnSubmit').addEventListener('click', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^[a-zA-Z0-9]{5,15}$/;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        let informationIsCorrect = true;
        if(!emailRegex.test(email)) {
            document.getElementById("errorEmail").innerHTML = "L'email n'est pas valide";
            informationIsCorrect = false;
        }else{
            document.getElementById("errorEmail").innerHTML = "&nbsp;";
        }
        if(!passwordRegex.test(password)) {
            document.getElementById("errorPassword").innerHTML = "Le mot de passe doit contenir entre 5 et 15 caractères";
            informationIsCorrect = false;
        }else{
            document.getElementById("errorPassword").innerHTML = "&nbsp;";
        }
        if(informationIsCorrect) {
            this.form.submit();
        }
    })
</script>

<?php
require_once 'pied-page.php';
?>

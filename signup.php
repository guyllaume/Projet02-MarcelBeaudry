<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = false; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
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

function sendConfirmationEmail($email, $token) {
    $to = $email;
    $subject = "Confirmation de votre inscription";
    $confirmationLink = "http://localhost/P2Local/Projet02-MarcelBeaudry/confirm.php?email=" . urlencode($email) . "&token=" . $token;
    $message = "Cliquez sur le lien suivant pour confirmer votre inscription : \n\n" . $confirmationLink;
    $headers = "From: noreply@votresite.com";

    // Simuler l'envoi d'email en enregistrant dans un fichier
    $logMessage = "To: $to\nSubject: $subject\nMessage: $message\nHeaders: $headers\n\n";
    file_put_contents('email_log.txt', $logMessage, FILE_APPEND);

    // Afficher le lien de confirmation (uniquement pour le développement)
    echo "<p>Lien de confirmation (pour développement seulement) : <a href='$confirmationLink'>$confirmationLink</a></p>";

    return true; // Simule un envoi réussi
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validation côté serveur
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[a-z0-9]{5,15}$/', $password)) {
        $mysql = new mysql("PJF_MARCELBEAUDRY", "424x-cgodin-qc-ca.php");
        
        // Vérifier si l'email existe déjà
        $mysql->requete = "SELECT * FROM utilisateurs WHERE Courriel = ?";
        $stmt = mysqli_prepare($mysql->cBD, $mysql->requete);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $message = "Cette adresse email est déjà utilisée.";
        } else {
            // Générer un token de confirmation
            $token = bin2hex(random_bytes(16));
            
            // Insérer le nouvel utilisateur
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $mysql->requete = "INSERT INTO utilisateurs (Courriel, MotDePasse, Creation, Statut, ConfirmationToken) VALUES (?, ?, NOW(), 0, ?)";
            $stmt = mysqli_prepare($mysql->cBD, $mysql->requete);
            mysqli_stmt_bind_param($stmt, "sss", $email, $hashedPassword, $token);
            
            if (mysqli_stmt_execute($stmt)) {
                // Envoyer l'email de confirmation
                if (sendConfirmationEmail($email, $token)) {
                    $message = "Inscription réussie. Un email de confirmation a été simulé. Veuillez vérifier le fichier email_log.txt ou utiliser le lien affiché ci-dessus.";
                } else {
                    $message = "Inscription réussie, mais la simulation de l'envoi de l'email a échoué.";
                }
            } else {
                $message = "Une erreur est survenue lors de l'inscription.";
            }
        }
        
        $mysql->deconnexion();
    } else {
        $message = "Les données saisies ne sont pas valides.";
    }
}

?>
    <div class="contenu">
        <?php
        if (!empty($message)) {
            echo "<p class='message'>$message</p>";
        }
        ?>
        <div class="card">
            <div class="card-header">
                <h1>Signup</h1>
            </div>
            
            <div class="card-body">
                <form action="signup.php" method="post">
                    <div class="form-group">
                        <div class="form-subgroup">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email">
                                <span class="error" id="errorEmail">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="email">Confirmation Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="confirmationEmail" name="email">
                                <span class="error" id="errorConfirmationEmail">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="password">Mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <span class="error" id="errorPassword">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="password">Confirmation Mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmationPassword" name="password">
                                <span class="error" id="errorConfirmationPassword">&nbsp;</span>
                            </div>
                        </div>
                    <button type="button" id="btnSubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnSubmit').addEventListener('click', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordRegex = /^[a-z0-9]{5,15}$/;
            const email = document.getElementById("email").value;
            const confirmationEmail = document.getElementById("confirmationEmail").value;
            const password = document.getElementById("password").value;
            const confirmationPassword = document.getElementById("confirmationPassword").value;
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
            if(email != confirmationEmail) {
                document.getElementById("errorConfirmationEmail").innerHTML = "Les emails ne sont pas identiques";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorConfirmationEmail").innerHTML = "&nbsp;";
            }
            if(password != confirmationPassword) {
                document.getElementById("errorConfirmationPassword").innerHTML = "Les mots de passe ne sont pas identiques";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorConfirmationPassword").innerHTML = "&nbsp;";
            }
            if(informationIsCorrect) {
                this.form.submit();
            }
        })
    </script>

<?php
require_once 'pied-page.php';
?>

<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = false; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
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

function sendResetEmail($email, $token) {
    $to = $email;
    $subject = "Réinitialisation de votre mot de passe";
    $resetLink = "http://localhost/P2Local/Projet02-MarcelBeaudry/resetPassword.php?email=" . urlencode($email) . "&token=" . $token;
    $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : \n\n" . $resetLink;
    $headers = "From: noreply@votresite.com";

    // Simuler l'envoi d'email en enregistrant dans un fichier
    $logMessage = "To: $to\nSubject: $subject\nMessage: $message\nHeaders: $headers\n\n";
    file_put_contents('reset_password_log.txt', $logMessage, FILE_APPEND);

    // Afficher le lien de réinitialisation (uniquement pour le développement)
    echo "<p>Lien de réinitialisation (pour développement seulement) : <a href='$resetLink'>$resetLink</a></p>";

    return true; // Simule un envoi réussi
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("UPDATE utilisateurs SET ResetToken = :token WHERE Courriel = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if (sendResetEmail($email, $token)) {
                $message = "Un email de réinitialisation a été envoyé à votre adresse email.";
            } else {
                $message = "Une erreur est survenue lors de l'envoi de l'email de réinitialisation.";
            }
        } else {
            $message = "Aucun compte n'est associé à cette adresse email.";
        }
    } else {
        $message = "L'adresse email n'est pas valide.";
    }
}
?>
    <div class="contenu">
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <div class="card">
            <div class="card-header">
                <h1>Mot de Passe Oublié?</h1>
            </div>
            <div class="card-body">
                <form action="forgotPassword.php" method="post">
                    <div class="form-group">
                        <div class="form-subgroup">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email">
                                <span class="error" id="errorEmail">&nbsp;</span>
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
        const email = document.getElementById("email").value;
        let informationIsCorrect = true;
        if(!emailRegex.test(email)) {
            document.getElementById("errorEmail").innerHTML = "L'email n'est pas valide";
            informationIsCorrect = false;
        }else{
            document.getElementById("errorEmail").innerHTML = "&nbsp;";
        }
        if(informationIsCorrect) {
            this.form.submit();
        }
    })
</script>

<?php
require_once 'pied-page.php';

?>
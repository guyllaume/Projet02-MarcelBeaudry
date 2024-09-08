<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = isset($_SESSION['user_id']); // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
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
    // Envoi d'un email de confirmation
    $mail = new PHPMailer(true);

    //lien de confirmation
    $resetLink = "http://localhost/P2Local/Projet02-MarcelBeaudry/resetPassword.php?email=" . urlencode($email) . "&token=" . $token;

    // message de confirmation
    $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : \n\n" . $resetLink;

    // Configuration du serveur SMTP
    try {
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com'; // choisir le serveur SMTP gmail
        $mail->SMTPAuth   = true; // Enable SMTP authentication
        $mail->Username   = 'noreply.guyllaume@gmail.com'; // SMTP username
        $mail->Password   = 'dcxdxviuhhnawlec'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587; // TCP port to connect to for TLS
    
        $mail->setFrom('noreply.guyllaume@gmail.com', 'Mailer'); // sender
        $mail->addAddress($email, 'New User'); // destinataire
        
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = "Reinitialisation de votre mot de passe"; // sujet
        $mail->Body    = $message; // message
        $mail->AltBody = $message; // Alternative plain text body for non-HTML mail clients
    
        $mail->send(); // Envoi du message
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
    return true; // Simule un envoi réussi
}

$message = '';
$error = true;

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
                $error = false;
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
                    <p class='<?php echo $error ? "error" : "success";?>'><?php echo empty($message) ? "&nbsp;" : $message;?></p>
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
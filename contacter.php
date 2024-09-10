<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/contacter.css';
$bIsConnected = isset($_SESSION['user_id']);
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

if (!$bIsConnected) {
    header('Location: login.php');
    exit();
}

$conn = connectDB();

$auteurId = $_POST['userToContact_id'] ?? '';

if (empty($auteurId)) {
    header('Location: annonces.php');
    exit();
}

// Récupérer les informations de l'auteur
$query = "SELECT Nom, Prenom, Courriel FROM utilisateurs WHERE NoUtilisateur = :auteurId";
$stmt = $conn->prepare($query);
$stmt->execute(['auteurId' => $auteurId]);
$auteur = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $sujet = $_POST['sujet'];
    
    // Utiliser PHPMailer pour envoyer l'email
    $mail = new PHPMailer(true);

    try {
        //Configuration du serveur
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply.guyllaume@gmail.com';
        $mail->Password   = 'dcxdxviuhhnawlec';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        //Destinataires
        $mail->setFrom('noreply.guyllaume@gmail.com', 'Votre Site');
        $mail->addAddress($auteur['Courriel'], $auteur['Prenom'] . ' ' . $auteur['Nom']);

        //Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        $messageConfirmation = "Votre message a été envoyé avec succès.";
    } catch (Exception $e) {
        $messageErreur = "Une erreur s'est produite lors de l'envoi du message. Erreur: {$mail->ErrorInfo}";
    }
}
?>

<div class="contenu">
    <div class="card">
        <div class="card-header">
            <h1>Contacter l'auteur de l'annonce</h1>
        </div>
        <div class="card-body">
            <?php if (isset($messageConfirmation)): ?>
                <p class="success"><?php echo $messageConfirmation; ?></p>
            <?php elseif (isset($messageErreur)): ?>
                <p class="error"><?php echo $messageErreur; ?></p>
            <?php else: ?>
                <form action="contacter.php" method="post">
                    <input type="hidden" name="userToContact_id" value="<?php echo htmlspecialchars($auteurId); ?>">
                    <div class="form-group">
                        <label for="sujet">Sujet :</label>
                        <input type="text" id="sujet" name="sujet" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message :</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Envoyer</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'pied-page.php'; ?>
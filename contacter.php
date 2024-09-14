<?php
header('Content-Type: text/html; charset=utf-8');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/contacter.css';
$bIsConnected = isset($_SESSION['user_id']);
require_once 'en-tete.php';
require_once 'db_connect.php';

$conn = connectDB();

$auteurId = $_POST['userToContact_id'] ?? '';
$annonceId = $_POST['annonce_id'] ?? '';

if (empty($auteurId) || empty($annonceId)) {
    header('Location: annonces.php');
    exit();
}

// Récupérer les informations de l'auteur
$query = "SELECT Nom, Prenom, Courriel FROM utilisateurs WHERE NoUtilisateur = :auteurId";
$stmt = $conn->prepare($query);
$stmt->execute(['auteurId' => $auteurId]);
$auteur = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les informations de l'annonce
$query = "SELECT DescriptionAbregee, DescriptionComplete, Prix, Parution FROM annonces WHERE NoAnnonce = :annonceId";
$stmt = $conn->prepare($query);
$stmt->execute(['annonceId' => $annonceId]);
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $sujet = $_POST['sujet'];
    
    // Utiliser PHPMailer pour envoyer l'email
    $mail = new PHPMailer(true);

    try {
        //Configuration du serveur
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
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
        $mail->Subject = '=?UTF-8?B?'.base64_encode($sujet).'?=';
        
        // Inclure les détails de l'annonce dans le corps du message
        $mailBody = "<h2>Détails de l'annonce:</h2>";
        $mailBody .= "<p><strong>Description:</strong> " . htmlspecialchars($annonce['DescriptionAbregee']) . "</p>";
        $mailBody .= "<p><strong>Prix:</strong> " . htmlspecialchars($annonce['Prix']) . " $</p>";
        $mailBody .= "<p><strong>Date de publication:</strong> " . htmlspecialchars($annonce['Parution']) . "</p>";
        $mailBody .= "<h2>Message de l'utilisateur:</h2>";
        $mailBody .= "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
    
        $mail->Body = $mailBody;
        $mail->AltBody = strip_tags($mailBody);
    
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
                <h2>Détails de l'annonce</h2>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($annonce['DescriptionAbregee']); ?></p>
                <p><strong>Prix:</strong> <?php echo htmlspecialchars($annonce['Prix']); ?> $</p>
                <p><strong>Date de publication:</strong> <?php echo htmlspecialchars($annonce['Parution']); ?></p>
                
                <form action="contacter.php" method="post">
                    <input type="hidden" name="userToContact_id" value="<?php echo htmlspecialchars($auteurId); ?>">
                    <input type="hidden" name="annonce_id" value="<?php echo htmlspecialchars($annonceId); ?>">
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
<?php
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = isset($_SESSION['user_id']);
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

function customHash($password) {
    return substr(hash('sha256', $password), 0, 15);
}

$message = '';
$error = true;
$conn = connectDB();

if ($bIsConnected) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT Courriel FROM utilisateurs WHERE NoUtilisateur = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $user['Courriel'];
}else {
    $email = $_GET['email'] ?? '';
    $token = $_GET['token'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    
    if ($password !== $confirmPassword) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (preg_match('/^[a-zA-Z0-9]{5,15}$/', $password)) {
        $hashedPassword = customHash($password);
        if ($bIsConnected) {
            $stmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = :password WHERE NoUtilisateur = :userId");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':userId', $userId);
        } else {
            $email = $_POST['email'];
            $token = $_POST['token'];
            $stmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = :password, ResetToken = NULL WHERE Courriel = :email AND ResetToken = :token");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);
        }
        
        if ($stmt->execute()) {
            $message = "Votre mot de passe a été réinitialisé avec succès.";
            $error = false;
            header("refresh:5;url=login.php");
        } else {
            $message = "Une erreur est survenue lors de la réinitialisation du mot de passe.";
        }
    } else {
        $message = "Le nouveau mot de passe n'est pas valide.";
    }
}
?>

<div class="contenu">
    <div class="card">
        <div class="card-header">
            <h1>Réinitialiser le mot de passe</h1>
        </div>
        <div class="card-body">
            <form action="resetPassword.php" method="post">
                <?php if (!$bIsConnected): ?>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <?php endif; ?>
                <div class="form-group">
                    <div class="form-subgroup">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="password" name="password" required>
                            <span class="error" id="errorPassword">&nbsp;</span>
                        </div>
                    </div>
                    <div class="form-subgroup">
                        <label for="confirmPassword">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            <span class="error" id="errorConfirmPassword">&nbsp;</span>
                        </div>
                    </div>
                    <p class='<?php echo $error ? "error" : "success";?>'><?php echo $message;?></p>
                    <button type="submit" class="large-button">Changer le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const passwordRegex = /^[a-zA-Z0-9]{5,15}$/;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        let informationIsCorrect = true;

        if(!passwordRegex.test(password)) {
            document.getElementById("errorPassword").innerHTML = "Le mot de passe doit contenir entre 5 et 15 caractères alphanumériques";
            informationIsCorrect = false;
        } else {
            document.getElementById("errorPassword").innerHTML = "&nbsp;";
        }

        if(password !== confirmPassword) {
            document.getElementById("errorConfirmPassword").innerHTML = "Les mots de passe ne correspondent pas";
            informationIsCorrect = false;
        } else {
            document.getElementById("errorConfirmPassword").innerHTML = "&nbsp;";
        }

        if(informationIsCorrect) {
            this.submit();
        }
    });
</script>

<?php require_once 'pied-page.php'; ?>
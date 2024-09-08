<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = false;
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

function customHash($password) {
    return substr(hash('sha256', $password), 0, 15);
}

$message = '';
$error = true;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    $token = $_GET['token'] ?? '';
    
    if (empty($email) || empty($token)) {
        $message = "Lien de réinitialisation invalide.";
    } else {
        // Vérifier si le token est valide
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = :email AND ResetToken = :token");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            $message = "Lien de réinitialisation invalide ou expiré.";
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $password = $_POST['password'];
    
    if (preg_match('/^[a-zA-Z0-9]{5,15}$/', $password)) {
        $conn = connectDB();
        $hashedPassword = customHash($password);
        $stmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = :password, ResetToken = NULL WHERE Courriel = :email AND ResetToken = :token");
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        
        if ($stmt->execute()) {
            $message = "Votre mot de passe a été réinitialisé avec succès.";
            $error = false;
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
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <div class="form-subgroup">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="error" id="errorPassword">&nbsp;</span>
                        </div>
                    </div>
                    <p class='<?php echo $error ? "error" : "success";?>'><?php echo empty($message) ? "&nbsp;" : $message;?></p>
                    <button type="button" id="btnSubmit" class="large-button">Changer le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnSubmit').addEventListener('click', function() {
        const passwordRegex = /^[a-zA-Z0-9]{5,15}$/;
        const password = document.getElementById("password").value;
        if(!passwordRegex.test(password)) {
            document.getElementById("errorPassword").innerHTML = "Le mot de passe doit contenir entre 5 et 15 caractères";
            informationIsCorrect = false;
        }else{
            document.getElementById("errorPassword").innerHTML = "&nbsp;";
            this.form.submit();
        }
    })
</script>

<?php require_once 'pied-page.php'; ?>
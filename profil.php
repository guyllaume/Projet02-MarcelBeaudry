<?php
$strNomFichierCSS = 'style/profil.css';
//SI UTILISATEUR CONNECTE MAIS PAS DE PROFIL ON REDIRIGE VERS LA PAGE DE PROFIL
// verirfier nom et prenom avant de rediriger. exclure admin == 1
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

$conn = connectDB();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    // Vérifier si nom et prénom sont remplis
    if (empty($nom) || empty($prenom)) {
        $erreur = "Le nom et le prénom sont obligatoires.";
    } else {
        // Les autres champs sont optionnels
        $statut = isset($_POST['statut']) ? $_POST['statut'] : null;
        $noEmpl = isset($_POST['noEmpl']) ? $_POST['noEmpl'] : null;
        $noTelMaison = isset($_POST['noTelMaison']) ? $_POST['noTelMaison'] : null;
        $noTeltravail = isset($_POST['noTeltravail']) ? $_POST['noTeltravail'] : null;
        $noTelCellulaire = isset($_POST['noTelCellulaire']) ? $_POST['noTelCellulaire'] : null;
        $access = isset($_POST['access']) ? $_POST['access'] : 'P'; // Valeur par défaut 'P' pour public
        $info = isset($_POST['info']) ? $_POST['info'] : null;
      
        if($access == "P"){
            if($noTelMaison != null) $noTelMaison .= "P";
            if($noTeltravail != null) $noTeltravail .= "P";
            if($noTelCellulaire != null) $noTelCellulaire .= "P";
        }else{
            if($noTelMaison != null) $noTelMaison .= "N";
            if($noTeltravail != null) $noTeltravail .= "N";
            if($noTelCellulaire != null) $noTelCellulaire .= "N";
        }

        $query = "UPDATE utilisateurs SET Statut = ?, NoEmpl = ?, Nom = ?, Prenom = ?, NoTelMaison = ?, NoTelTravail = ?, NoTelCellulaire = ?, AutresInfos = ?, Modification = NOW() WHERE NoUtilisateur = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$statut, $noEmpl, $nom, $prenom, $noTelMaison, $noTeltravail, $noTelCellulaire, $info, $userId]);

        $message = "Profil mis à jour avec succès.";
    }
}

// Récupération des données actuelles de l'utilisateur
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM utilisateurs WHERE NoUtilisateur = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="contenu">
    <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>
    <form class="profil-form" action="profil.php" method="post">
        <div class="profil-card">
            <h1>Profil</h1>
            <div>
                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="">Choisir Un statut</option>
                    <option value="2">Cadre</option>
                    <option value="3">Employé de soutien</option>
                    <option value="4">Enseignant</option>
                    <option value="5">Professionnel</option>
                </select>
            </div>
            <div>
                <label for="noEmpl">Numéro d'employé</label>
                <input type="text" name="noEmpl" id="noEmpl" maxlength="4">
            </div>
            <div>
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" maxlength="25" required value="<?php echo htmlspecialchars($user['Nom'] ?? ''); ?>">
            </div>
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" maxlength="20" required value="<?php echo htmlspecialchars($user['Prenom'] ?? ''); ?>">
            </div>
            <div>
                <label for="email">Courriel</label>
                <span id="email"><?php echo $user['Courriel']; ?></span>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <button id="btnModifierMotDePasse" type="button">Modifier Mot de Passe</button>
            </div>
            <div>
                <label for="noTelMaison">No Téléphone Maison</label>
                <input type="text" name="noTelMaison" id="noTelMaison" maxlength="15">
            </div>
            <div>
                <label for="noTeltravail">No Téléphone Travail</label>
                <input type="text" name="noTeltravail" id="noTeltravail" maxlength="21">
            </div>
            <div>
                <label for="noTelCellulaire">No Téléphone Cellulaire</label>
                <input type="text" name="noTelCellulaire" id="noTelCellulaire" maxlength="15">
            </div>
            <div>
                <label for="access">Niveau d'Accès</label>
                <select name="access" id="access">
                    <option value="P">Public</option>
                    <option value="N">Privée</option>
                </select>
            </div>
            <div>
                <label for="info">Autres informations</label>
                <textarea rows="2" cols="50" maxlength="50" name="info" id="info"></textarea>
            </div>
            <span class="error" id="errorMessage">&nbsp;</span>
            <button type="button" id="btnSubmit">Modifier Profil</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('btnModifierMotDePasse').addEventListener('click', function() {
        window.location.href = 'resetPassword.php';
    })
    document.getElementById('btnSubmit').addEventListener('click', function() {
        //Regex
        const phoneRegex = /^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/;
        const phoneWithPosteRegex = /^\(?\d{3}\)?[ .-]?\d{3}[ .-]?\d{4}\s?#?\d{4}$/;
        const noEmplRegex = /^(?!0000)[0-9]{4}$/;
        const nomRegex = /^[A-Za-z][A-Za-z .'-]{0,23}[A-Za-z]$/;
        const prenomRegex = /^[A-Za-z][A-Za-z .'-]{0,18}[A-Za-z]$/;

        //Valeur a valider
        let noEmpl = document.getElementById("noEmpl").value;
        let nom = document.getElementById("nom").value;
        let prenom = document.getElementById("prenom").value;
        let noTelMaison = document.getElementById("noTelMaison").value;
        let noTeltravail = document.getElementById("noTeltravail").value;
        let noTelCellulaire = document.getElementById("noTelCellulaire").value;
        let access = document.getElementById("access").value;

        let errorMessage = document.getElementById("errorMessage");

        //Validation des champs
        if(noEmpl.length > 0 && !noEmplRegex.test(noEmpl)) {
            errorMessage.innerHTML = "Le numéro d'employé est invalide (ex: 9999)";
            return;
        }
        if (nom.trim() === "" || prenom.trim() === "") {
            errorMessage.innerHTML = "Le nom et le prénom sont obligatoires.";
            return;
        }
        if(noTelMaison.length > 0 && !phoneRegex.test(noTelMaison)) {
            errorMessage.innerHTML = "Le numéro de telephone de la maison est invalide";
            return;
        }
        if(noTeltravail.length > 0 && !phoneWithPosteRegex.test(noTeltravail)) {
            errorMessage.innerHTML = "Le numéro de telephone de travail est invalide";
            return;
        }
        if(noTelCellulaire.length > 0 && !phoneRegex.test(noTelCellulaire)) {
            errorMessage.innerHTML = "Le numéro de telephone cellulaire est invalide";
            return;
        }

        //Envoi de la requête
        this.form.submit();

    })
</script>
<?php
require_once 'pied-page.php';

?>
    
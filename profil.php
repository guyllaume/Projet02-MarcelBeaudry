<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/profil.css';
$bIsConnected = true; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
//SI UTILISATEUR CONNECTE MAIS PAS DE PROFIL ON REDIRIGE VERS LA PAGE DE PROFIL
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
<div class="contenu">
    <form class="profil-form" action="profil.php" method="post">
        <div class="profil-card">
            <h1>Profil</h1>
            <div>
                <label for="statut">Statut</label>
                <select name="statut" id="statut"> <!--Génération automatique a partir du serveur-->
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
                <input type="text" name="nom" id="nom" maxlength="25" required>
            </div>
            <div>
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" maxlength="20" required>
            </div>
            <div>
                <label for="email">Courriel</label>
                <span id="email"><!--Email from user table-->Gggg@gmail.com</span>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <button id="btnModifierMotDePasse">Modifier Mot de Passe</button> <!--Redirects to resetPassword.php WIP-->
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
        window.location.href = 'resetPassword.php'; //WIP
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
        if(!nomRegex.test(nom)) {
            errorMessage.innerHTML = "Le nom est invalide";
            return;
        }
        if(!prenomRegex.test(prenom)) {
            errorMessage.innerHTML = "Le prenom est invalide";
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
        if(noTelCellulaire.length > 0 && !phoneWithPosteRegex.test(noTelCellulaire)) {
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
    
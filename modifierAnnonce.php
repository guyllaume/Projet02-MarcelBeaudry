<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/annonces.css';
$bIsConnected = true; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
<div class="contenu">
    <form class="ajouter-annonce-form" action="ajouterAnnonce.php" method="post">
        <div class="ajouter-annonce-card">
            <h1>Modifier une Annonce</h1>
            <div>
                <label for="descriptionAbregee">Description Abrégée</label>
                <textarea rows="2" cols="50" maxlength="50" name="descriptionAbregee" id="descriptionAbregee" required></textarea>
            </div>
            <div>
                <label for="descriptionComplete">Description Complète</label>
                <textarea rows="5" cols="50" maxlength="250" name="descriptionComplete" id="descriptionComplete" required></textarea>
            </div>
            <div>
                <label for="categorie">Catégorie</label>
                <select name="categorie" id="categorie" required> <!--Génération automatique a partir du serveur-->
                    <option value="">Choisir Une Catégorie</option>
                    <option value="1">Voiture</option>
                    <option value="2">Moto</option>
                    <option value="3">Velo</option>
                </select>
            </div>
            <div>
                <label for="prix">Prix</label>
                <input type="text" name="prix" id="prix" required>
            </div>
            <div>
                <label for="fileToUpload">Ajouter une image</label>
                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" required>
            </div>
            <div>
                <label for="etat">État</label>
                <select name="etat" id="etat" required> <!--Génération automatique a partir du serveur-->
                    <option value="1">Actif</option>
                    <option value="2">Inactif</option>
                </select>
            </div>
            <span class="error" id="errorMessage">&nbsp;</span>
            <button type="button" id="btnSubmit">Ajouter Annonce</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('btnSubmit').addEventListener('click', function() {
        let prixRegex = /^\d+(?:[.,]\d{1,2})?$/;
        let prix = document.getElementById("prix").value;
        let descriptionAbregee = document.getElementById("descriptionAbregee").value;
        let descriptionComplete = document.getElementById("descriptionComplete").value;
        let categorie = document.getElementById("categorie").value;
        const fileInput = document.getElementById('fileToUpload');
        const fileName = fileInput.value.split("\\").pop();
        const file = fileInput.files[0];
        let errorMessage = document.getElementById("errorMessage");

        //Validation des champs

        console.log(fileName.length);
        if(("photos-annonce/" + fileName).length > 50) {
            errorMessage.innerHTML = "Le nom de l'image est trop grand";
            return;
        }
        if (file) {
            // Vérifie le type MIME
            if (!file.type.startsWith('image/')) {
                errorMessage.innerHTML = 'Le fichier sélectionné n\'est pas une image.';
                return;
            }

            // Vérifie les dimensions de l'image
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    if (img.width < 50 || img.height < 50) {
                        errorMessage.textContent = 'L\'image est trop petite.';
                        return;
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            errorMessage.textContent = 'Veuillez sélectionner un fichier.';
            return;
        }
        if(descriptionAbregee.length < 3) {
            errorMessage.innerHTML = "La description abregee doit contenir au moins 3 caractères";
            return;
        }
        if(descriptionComplete.length < 3) {
            errorMessage.innerHTML = "La description complete doit contenir au moins 3 caractères";
            return;
        }
        if(categorie === "") {
            errorMessage.innerHTML = "Choisissez une categorie";
            return;
        }
        if(!prixRegex.test(prix) || prix === "") {
            errorMessage.innerHTML = "Le prix doit être un nombre";
            return;
        }
        let normalizedPrix = parseFloat(prix.replace(",", "."));
        if(normalizedPrix <= 0) {
            errorMessage.innerHTML = "Le prix doit être positif";
            return;
        }
        if(normalizedPrix >= 100000) {
            errorMessage.innerHTML = "Le prix doit être inférieur à 100 000 $";
            return;
        }
        document.getElementById("prix").value = normalizedPrix;

        errorMessage.innerHTML = "&nbsp;";
        this.form.submit();
    })
</script>

<?php
require_once 'pied-page.php';

?>
<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/annonces.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';

$mysql = new mysql("PJF_MARCELBEAUDRY", "424x-cgodin-qc-ca.php");
$bdd = $mysql->cBD;

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

//Manipulation d'annonces
if(isset($_POST['noAnnonce']) && isset($_POST['changeEtat'])) {
    mysqli_query($bdd, "UPDATE annonces SET Etat = " . $_POST['changeEtat'] . " WHERE NoAnnonce = " . $_POST['noAnnonce']);
}

// Récupérer les parametres d'affichage des annonces
if(isset($_POST['ddlOrdre']) && isset($_POST['ddlTri']) && isset($_POST['ddlNoPage'])) {
    $ordre = $_POST['ddlOrdre'];
    $tri = $_POST['ddlTri'];
    $page = $_POST['ddlNoPage'];
} else {
    $ordre = "Desc";
    $tri = "Parution";
    $page = 1;
}
$nbAnnoncesParPage = 10;

// Calculer le nombre total d'annonces
$result = mysqli_query($bdd, "SELECT * FROM annonces WHERE NoUtilisateur = " . $_SESSION['user_id']);
$nbAnnoncesTotal = mysqli_num_rows($result);

// Calculer le nombre total de pages
$nbPages = ceil($nbAnnoncesTotal / $nbAnnoncesParPage);

// Calculer le offset
$offset = ($page - 1) * $nbAnnoncesParPage;

if($tri == "Categorie") {
    $result = mysqli_query($bdd,
     "SELECT a.*, c.description
      FROM annonces a
      JOIN categories c 
      ON a.Categorie = c.NoCategorie 
      WHERE NoUtilisateur = " . $_SESSION['user_id'] . "
      ORDER BY c.description $ordre
      LIMIT $nbAnnoncesParPage OFFSET $offset");
}else{
    $result = mysqli_query($bdd, "SELECT * FROM annonces WHERE NoUtilisateur = " . $_SESSION['user_id'] . " ORDER BY $tri $ordre LIMIT $nbAnnoncesParPage OFFSET $offset");
}

?>
    <div class="contenu top">
        <form id="options" class="options" action="gestion.php" method="post">
            <label for="ddlOrdre">Ordre par</label>
            <select name="ddlOrdre" id="ddlOrdre" onchange="this.form.submit()">
                <option value="Asc" <?php if($ordre == "Asc") echo "selected"?>>Ascendant</option>
                <option value="Desc" <?php if($ordre == "Desc") echo "selected"?>>Descendant</option>
            </select>
            <label for="ddlOrdre">Trie par</label>
            <select name="ddlTri" id="ddlTri" onchange="this.form.submit()">
                <option value="Parution" <?php if($tri == "Parution") echo "selected"?>>Date de Parution</option>
                <option value="DescriptionAbregee" <?php if($tri == "DescriptionAbregee") echo "selected"?>>Description Abrégée</option>
                <option value="Categorie" <?php if($tri == "Categorie") echo "selected"?>>Catégorie</option>
                <option value="Etat" <?php if($tri == "Etat") echo "selected"?>>État</option>
            </select>
            <label for="ddlOrdre" class="grow right">Pages</label>
            <select name="ddlNoPage" id="ddlNoPage" onchange="this.form.submit()">
                <?php
                for($i = 1; $i <= $nbPages; $i++) {
                    echo "<option value='$i'";
                    if($page == $i) echo " selected";
                    echo ">$i</option>";
                }
                ?>
            </select>
            <img class="icon <?php echo $page == 1 ? "disabled" : ""?>" id="btnFirstPage" src="photos-annonce/first.png">
            <img class="icon <?php echo $page == 1 ? "disabled" : ""?>" id="btnPrecedentPage" src="photos-annonce/precedent.png">
            <img class="icon <?php echo $page == $nbPages ? "disabled" : ""?>" id="btnNextPage" src="photos-annonce/next.png">
            <img class="icon <?php echo $page == $nbPages ? "disabled" : ""?>" id="btnLastPage" src="photos-annonce/last.png">
            <button type="button" class="ajouterAnnonce" id="btnAjouterAnnonce">Ajouter Annonce</button>
        </form>
        <div class="nbAnnonces"><?php echo "Nombre d'annonces total : " . $nbAnnoncesTotal?></div>
        <?php
        if($result){
            $annonces = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $i = ($page - 1) * $nbAnnoncesParPage + 1;
            foreach ($annonces as $annonce) {
                    //userInfos
                    $mysql->requete = "SELECT * FROM utilisateurs WHERE NoUtilisateur = ?";
                    $stmt = mysqli_prepare($bdd, $mysql->requete);
                    mysqli_stmt_bind_param($stmt, "i", $annonce['NoUtilisateur']);
                    mysqli_stmt_execute($stmt);
                    $resultUser = mysqli_stmt_get_result($stmt);
                    $utilisateur = mysqli_fetch_assoc($resultUser);
                    //categorieInfos
                    $mysql->requete = "SELECT * FROM categories WHERE NoCategorie = ?";
                    $stmt = mysqli_prepare($bdd, $mysql->requete);
                    mysqli_stmt_bind_param($stmt, "i", $annonce['Categorie']);
                    mysqli_stmt_execute($stmt);
                    $resultCategorie = mysqli_stmt_get_result($stmt);
                    $categorie = mysqli_fetch_assoc($resultCategorie);
                ?>
        <div class="annonce-card"> 
            <img class="vignette" src="<?php echo $annonce['Photo']; ?>">
            <h2><?php echo $i++; ?>-</h2>
            <div class="grow">
                <h3><?php echo $annonce['NoAnnonce']; ?></h3>
                <p><a class="afficherDescriptionComplete"
                        data-img-src="<?php echo $annonce['Photo']; ?>"
                        data-descriptionComplete="<?php echo $annonce['DescriptionComplete']; ?>"
                        <?php
                        if(str_ends_with($utilisateur['NoTelMaison'],"N")) {
                            echo 'data-home-phone="privé"';
                            echo 'data-travail-phone="privé"';
                            echo 'data-cellulaire-phone="privé"';
                        }else{
                            echo 'data-home-phone="'.  substr($utilisateur['NoTelMaison'],0,-1).'"';
                            echo 'data-travail-phone="'.  substr($utilisateur['NoTelTravail'],0,-1).'"';
                            echo 'data-cellulaire-phone="'.  substr($utilisateur['NoTelCellulaire'],0,-1).'"';
                        }
                        ?>
                        data-mise-a-jour="<?php echo $annonce['MiseAJour']; ?>"
                        ><?php echo $annonce['DescriptionAbregee']; ?></a></p>
                <a><?php echo $utilisateur['Prenom'] . ' ' . $utilisateur['Nom']; ?></a>
            </div>
            <div class="right">
                <h3><?php echo $annonce['Parution']; ?></h3>
                <p><?php echo $categorie['Description']; ?></p>
                <p class="price"><?php echo $annonce['Prix'] == 0 ? "N/A" : $annonce['Prix']; ?>$</p>
                <div class="bottomOptions">
                    <span class="smallDate">Dernière Mise À Jour <?php echo $annonce['MiseAJour']; ?></span>
                    <button class="btnModifier">Modifier</button>
                    <button class="btnRetirer" data-noAnnonce="<?php echo $annonce['NoAnnonce']; ?>">Retirer</button>
                    <div class="center top">
                        <span><?php echo $annonce['Etat'] == 1 ? "Actif" : ($annonce['Etat'] == 2 ? "Inactif" : "Retiré"); ?></span> 
                        <button class="btnEtat" data-noAnnonce="<?php echo $annonce['NoAnnonce']; ?>"><?php echo $annonce['Etat'] == 1 ? "Désactiver" : "Activer" ?></button>
                    </div>
                </div>
            </div>
        </div>
                <?php
                }
            }
        ?>
    </div>
    <form id="annonceForm" method="POST" action="gestion.php">
        <input type="hidden" id="noAnnonce" name="noAnnonce" value="">
        <input type="hidden" id="changeEtat" name="changeEtat"  value="">
    </form>
    <div id="descriptionCompleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modalBody">
                <img id="modal-img" class="photoComplete" src="">
                <h2>Description Complete</h2>
                <p id="modal-descriptionComplete"></p>
                <div>
                    <p id="modal-home-phone"></p>
                    <p id="modal-travail-phone"></p>
                    <p id="modal-cellulaire-phone"></p>
                </div>
                <div id="modal-mise-a-jour" class="smallDate"></div>
            </div>
            <div class="modalOptions">
                <button id="btnFermer">Fermer</button>
            </div>
        </div>
    </div>
    <div id="retirerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modalTitle">Voulez-vous vraiment retirez cette annonce?</h2>
            <div class="modalOptions">
                <button id="btnConfirmRetirer">Confirmer</button>
                <button id="btnCancelRetirer">Annuler</button>
            </div>
        </div>
    </div>

    <script>
        // Get the modal
        let modalRetirer = document.getElementById("retirerModal");
        let modalDescriptionComplete = document.getElementById("descriptionCompleteModal");

        // Get the button that opens the modal - change for class eventually
        let btnRetirer = document.getElementsByClassName("btnRetirer");
        let btnModifier = document.getElementsByClassName("btnModifier");
        let btnEtat = document.getElementsByClassName("btnEtat");
        let hiddenChangeEtat = document.getElementById("changeEtat");
        let hiddenNoAnnonce = document.getElementById("noAnnonce");
        let btnConfirmRetirer = document.getElementById("btnConfirmRetirer"); //Devrait envoyer un post avec le NoAnnonce a retirer
        let btnCancelRetirer = document.getElementById("btnCancelRetirer");
        let btnAjouterAnnonce = document.getElementById("btnAjouterAnnonce");
        let annonceForm = document.getElementById("annonceForm");

        let btnsAfficherDescription = document.getElementsByClassName("afficherDescriptionComplete");
        let btnFermer = document.getElementById("btnFermer");

        // Get the <span> element that closes the modal
        let spans = document.getElementsByClassName("close");

        // When the user clicks the button, open the modal
        for (let i = 0; i < btnsAfficherDescription.length; i++) {
            btnsAfficherDescription[i].addEventListener('click', function(){
                let imgSrc = this.getAttribute('data-img-src');
                let descriptionComplete = this.getAttribute('data-descriptionComplete');
                let homePhone = this.getAttribute('data-home-phone');
                let travailPhone = this.getAttribute('data-travail-phone');
                let cellulairePhone = this.getAttribute('data-cellulaire-phone');
                let miseAJour = this.getAttribute('data-mise-a-jour');

                document.getElementById("modal-img").src = imgSrc;
                document.getElementById("modal-descriptionComplete").innerHTML = descriptionComplete;
                document.getElementById("modal-home-phone").innerHTML = 'Téléphone Maison : ' + homePhone;
                document.getElementById("modal-travail-phone").innerHTML = 'Téléphone Travail : ' + travailPhone;
                document.getElementById("modal-cellulaire-phone").innerHTML = 'Téléphone Cellulaire : ' + cellulairePhone;
                document.getElementById("modal-mise-a-jour").innerHTML = 'Dernière Mise à Jour : ' + miseAJour;

                modalDescriptionComplete.style.display = "block";
            });
        }

        for (let i = 0; i < btnRetirer.length; i++) {
            btnRetirer[i].addEventListener('click', function(){
                modalRetirer.style.display = "block";
                let noAnnonce = this.getAttribute('data-noAnnonce');
                hiddenNoAnnonce.value = noAnnonce;
                hiddenChangeEtat.value = 3;
            });
        }

        for (let i = 0; i < btnEtat.length; i++) {
            btnEtat[i].addEventListener('click', function(){
                let noAnnonce = this.getAttribute('data-noAnnonce');
                hiddenNoAnnonce.value = noAnnonce;
                btnEtat[i].innerHTML == "Activer" ? hiddenChangeEtat.value = 1 : hiddenChangeEtat.value = 2;
                annonceForm.submit();
            });
        }

        // When the user clicks to close the modal
        btnConfirmRetirer.onclick = function() {
            annonceForm.submit();
        }
        btnCancelRetirer.onclick = function() {
            hiddenNoAnnonce.value = noAnnonce;
            hiddenChangeEtat.value = null;
            modalRetirer.style.display = null;
        }
        btnFermer.onclick = function() {
            modalDescriptionComplete.style.display = "none";
        }
        for (let i = 0; i < btnModifier.length; i++) {
            btnModifier[i].onclick = function() {
                window.location.href = "modifierAnnonce.php";
            }
        }
        btnAjouterAnnonce.onclick = function() {
            window.location.href = "ajouterAnnonce.php";
        }
        // When the user clicks on <span> (x), close the modal
        for (let i = 0; i < spans.length; i++) {
            spans[i].onclick = function() {
                modalRetirer.style.display = "none";
                modalDescriptionComplete.style.display = "none";
                if(hiddenChangeEtat.value == 3) {
                    hiddenNoAnnonce.value = null;
                    hiddenChangeEtat.value = null;
                }
            }
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modalRetirer) {
                modalRetirer.style.display = "none";
                hiddenNoAnnonce.value = null;
                hiddenChangeEtat.value = null;
            }
            if(event.target == modalDescriptionComplete) {
                modalDescriptionComplete.style.display = "none";
            }
        }

        let btnFirstPage = document.getElementById("btnFirstPage");
        let btnPreviousPage = document.getElementById("btnPrecedentPage");
        let btnNextPage = document.getElementById("btnNextPage");
        let btnLastPage = document.getElementById("btnLastPage");
        let ddlNoPage = document.getElementById("ddlNoPage");
        let optionForm = document.getElementById("options");

        btnFirstPage.onclick = function() {
            if(ddlNoPage.value > 1){
                ddlNoPage.value = 1;
                optionForm.submit();
            }
        }
        btnPreviousPage.onclick = function() {
            if(ddlNoPage.value > 1){
                ddlNoPage.value = ddlNoPage.value - 1;
                optionForm.submit();
            }
        }
        btnNextPage.onclick = function() {
            if(ddlNoPage.value < <?php echo $nbPages;?>){
                ddlNoPage.value = parseInt(ddlNoPage.value) + 1;
                optionForm.submit();
            }
        }
        btnLastPage.onclick = function() {
            if(ddlNoPage.value < <?php echo $nbPages;?>){
                ddlNoPage.value = <?php echo $nbPages;?>;
                optionForm.submit();
            }
        }
    </script>

<?php
require_once 'pied-page.php';

?>
    
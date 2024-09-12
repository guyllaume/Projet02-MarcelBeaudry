<?php
$strNomFichierCSS = 'style/annonces.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
require_once 'classe-mysql.php';
require_once '424x-cgodin-qc-ca.php';
require_once 'db_connect.php';

$conn = connectDB();

$mysql = new mysql("PJF_MARCELBEAUDRY", "424x-cgodin-qc-ca.php");
$bdd = $mysql->cBD;

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Récupérer les parametres d'affichage des annonces
if(isset($_POST['ddlOrdre']) && isset($_POST['ddlTri']) && isset($_POST['ddlNbAnnoncesParPage'])) {
    $ordre = $_POST['ddlOrdre'];
    $tri = $_POST['ddlTri'];
    $nbAnnoncesParPage = $_POST['ddlNbAnnoncesParPage'];
    $page = $_POST['ddlNoPage'];
} else {
    $ordre = "Desc";
    $tri = "Parution";
    $nbAnnoncesParPage = 10;
    $page = 1;
}

// Calculer le nombre total d'annonces
$result = mysqli_query($bdd, "SELECT * FROM annonces WHERE Etat = 1");
$nbAnnoncesTotal = mysqli_num_rows($result);

// Calculer le nombre total de pages
$nbPages = ceil($nbAnnoncesTotal / $nbAnnoncesParPage);
if($nbPages == 0) $nbPages = 1; // Assure qu'il y a au moins une page

// Calculer le offset
$offset = ($page - 1) * $nbAnnoncesParPage;
if($offset > $nbAnnoncesTotal) { // Vérifie si la page est invalide
    $offset = 0;
    $page = 1;
}

if($tri == "Parution") {
    $result = mysqli_query($bdd, "SELECT * FROM annonces WHERE Etat = 1 ORDER BY $tri $ordre LIMIT $nbAnnoncesParPage OFFSET $offset");
}else if($tri == "Auteur") {
    $result = mysqli_query($bdd,
     "SELECT a.*, u.prenom, u.nom
      FROM annonces a
      JOIN utilisateurs u 
      ON a.NoUtilisateur = u.NoUtilisateur 
      WHERE a.Etat = 1
      ORDER BY u.nom $ordre, u.prenom $ordre
      LIMIT $nbAnnoncesParPage OFFSET $offset");
}else{
    $result = mysqli_query($bdd,
     "SELECT a.*, c.description
      FROM annonces a
      JOIN categories c 
      ON a.Categorie = c.NoCategorie 
      WHERE a.Etat = 1
      ORDER BY c.description $ordre
      LIMIT $nbAnnoncesParPage OFFSET $offset");
}

?>
    <div class="contenu top">
        <form id="options" class="options" action="annonces.php" method="post">
            <label for="ddlOrdre">Ordre par</label>
            <select name="ddlOrdre" id="ddlOrdre" onchange="this.form.submit()">
                <option value="Asc" <?php if($ordre == "Asc") echo "selected"?>>Ascendant</option>
                <option value="Desc" <?php if($ordre == "Desc") echo "selected"?>>Descendant</option>
            </select>
            <label for="ddlTri">Trie par</label>
            <select name="ddlTri" id="ddlTri" onchange="this.form.submit()">
                <option value="Parution" <?php if($tri == "Parution") echo "selected"?>>Date de Parution</option>
                <option value="Auteur" <?php if($tri == "Auteur") echo "selected"?>>Auteur</option>
                <option value="Categorie" <?php if($tri == "Categorie") echo "selected"?>>Catégorie</option>
            </select>
            <label for="ddlNbAnnoncesParPage">Nombre d'annonces par page</label>
            <select name="ddlNbAnnoncesParPage" id="ddlNbAnnoncesParPage" onchange="this.form.submit()">
                <option value="5" <?php if($nbAnnoncesParPage == "5") echo "selected"?>>5</option>
                <option value="10" <?php if($nbAnnoncesParPage == "10") echo "selected"?>>10</option>
                <option value="15" <?php if($nbAnnoncesParPage == "15") echo "selected"?>>15</option>
                <option value="20" <?php if($nbAnnoncesParPage == "20") echo "selected"?>>20</option>
            </select>
            <div class="grow center">
                <input type="text" id="txtRecherche" name="txtRecherche">
                <img class="icon" src="images/loupe.png" id="btnRecherche">
                <div id="resultatsRecherche" class="resultats-recherche"></div>
            </div>
            <label for="ddlNoPage">Pages</label>
            <select name="ddlNoPage" id="ddlNoPage" onchange="this.form.submit()">
                <?php
                for($i = 1; $i <= $nbPages; $i++) {
                    echo "<option value='$i'";
                    if($page == $i) echo " selected";
                    echo ">$i</option>";
                }
                ?>
            </select>
            <img class="icon <?php echo $page == 1 ? "disabled" : ""?>" id="btnFirstPage" src="images/first.png">
            <img class="icon <?php echo $page == 1 ? "disabled" : ""?>" id="btnPrecedentPage" src="images/precedent.png">
            <img class="icon <?php echo $page == $nbPages ? "disabled" : ""?>" id="btnNextPage" src="images/next.png">
            <img class="icon <?php echo $page == $nbPages ? "disabled" : ""?>" id="btnLastPage" src="images/last.png">
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
                    <a class="<?php echo $utilisateur['NoUtilisateur'] == $_SESSION["user_id"] ? "" : "sendUserId";?>" data-id="<?php echo $utilisateur['NoUtilisateur']; ?>"><?php echo $utilisateur['Prenom'] . ' ' . $utilisateur['Nom']; ?></a>
                </div>
                <div class="right">
                    <h3><?php echo $annonce['Parution']; ?></h3>
                    <p><?php echo $categorie['Description']; ?></p>
                    <p class="price"><?php echo $annonce['Prix'] == 0 ? "N/A" : $annonce['Prix']; ?>$</p> 
                </div>
            </div>
            <?php
            }
        }
        ?>
    </div>
    <form id="contacterForm" method="POST" action="contacter.php">
        <input type="hidden" id="userToContact_id" name="userToContact_id" value="">
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

    
    <script>
        // Get the modal
        let modal = document.getElementById("descriptionCompleteModal");

        // Get the button that opens the modal - change for class eventually
        let btnsAfficherDescription = document.getElementsByClassName("afficherDescriptionComplete");
        let btnFermer = document.getElementById("btnFermer");

        // Get the <span> element that closes the modal
        let span = document.getElementsByClassName("close")[0];

        let contactForm =document.getElementById("contacterForm");
        let userToContact_id = document.getElementById("userToContact_id");
        let sendUserId = document.getElementsByClassName("sendUserId");

        for (let i = 0; i < sendUserId.length; i++) {
            sendUserId[i].addEventListener('click', function(){
                userToContact_id.value = this.getAttribute('data-id');
                contactForm.submit();
            });
        }
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

                modal.style.display = "block";
            });
        }
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        btnFermer.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    console.log("Script de recherche chargé");
    const txtRecherche = document.getElementById('txtRecherche');
    const resultatsRecherche = document.getElementById('resultatsRecherche');

    txtRecherche.addEventListener('input', function() {
        console.log("Valeur de recherche:", this.value);
        // Nombre de caractère écrit avant l'envoie de la requête
        if (this.value.length >= 1) {
            console.log("Envoi de la requête à barRecherche.php");
            fetch(`barRecherche.php?query=${encodeURIComponent(this.value)}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Données reçues:", data);
                    afficherResultats(data);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        } else {
            resultatsRecherche.innerHTML = '';
            resultatsRecherche.style.display = 'none';
        }
    });

    function afficherResultats(data) {
    resultatsRecherche.innerHTML = '';
    if (data.length === 0) {
        resultatsRecherche.innerHTML = '<div>Aucun résultat trouvé</div>';
    } else {
        data.forEach(item => {
            const div = document.createElement('div');
            div.innerHTML = `
                <strong>${item.DescriptionAbregee}</strong><br>
                Auteur: ${item.PrenomAuteur} ${item.NomAuteur}<br>
                Catégorie: ${item.NomCategorie}
            `;
            div.addEventListener('click', () => afficherAnnonce(item));
            resultatsRecherche.appendChild(div);
        });
    }
    resultatsRecherche.style.display = 'block';
    console.log("Résultats affichés");
}

    // Fermer les résultats quand on clique en dehors
    document.addEventListener('click', function(e) {
        if (e.target !== txtRecherche && !resultatsRecherche.contains(e.target)) {
            resultatsRecherche.style.display = 'none';
        }
    });

    function afficherAnnonce(annonce) {
        let modal = document.getElementById("descriptionCompleteModal");
        document.getElementById("modal-img").src = annonce.Photo;
        document.getElementById("modal-descriptionComplete").innerHTML = annonce.DescriptionComplete;
        // Ajoutez d'autres détails si nécessaire
        modal.style.display = "block";
        resultatsRecherche.style.display = 'none'; // Cacher les résultats après sélection
    }
    });
    txtRecherche.addEventListener('input', function() {
        console.log("Valeur de recherche:", this.value);
        if (this.value.length >= 3) {
                console.log("Envoi de la requête à barRecherche.php");
                fetch(`barRecherche.php?query=${encodeURIComponent(this.value)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log("Données reçues:", data);
                        afficherResultats(data);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                    });
            } else {
                resultatsRecherche.innerHTML = '';
                resultatsRecherche.style.display = 'none';
            }
    });

    function afficherResultats(data) {
        resultatsRecherche.innerHTML = '';
        if (data.length === 0) {
            resultatsRecherche.innerHTML = '<div>Aucun résultat trouvé</div>';
        } else {
            data.forEach(item => {
                const div = document.createElement('div');
                div.textContent = item.DescriptionAbregee;
                div.addEventListener('click', () => afficherAnnonce(item));
                resultatsRecherche.appendChild(div);
            });
        }
        resultatsRecherche.style.display = 'block';
        console.log("Résultats affichés");
    }

    document.addEventListener('click', function(e) {
        if (e.target !== txtRecherche && !resultatsRecherche.contains(e.target)) {
            resultatsRecherche.style.display = 'none';
        }
    });

    function afficherAnnonce(annonce) {
    let modal = document.getElementById("descriptionCompleteModal");
    document.getElementById("modal-img").src = annonce.Photo;
    document.getElementById("modal-descriptionComplete").innerHTML = annonce.DescriptionComplete;
    // Ajoutez d'autres détails si nécessaire
    modal.style.display = "block";
    resultatsRecherche.style.display = 'none'; // Cacher les résultats après sélection
    }
</script>
<?php
require_once 'pied-page.php';

?>
<!-- <a href="https://www.flaticon.com/free-icons/search" title="search icons">Search icons created by Pixel perfect - Flaticon</a>  -->
<!-- <a href="https://www.flaticon.com/free-icons/arrows" title="arrows icons">Arrows icons created by gravisio - Flaticon</a> -->
<!-- https://www.flaticon.com/free-icon/right-arrow_271228?term=arrow+left&page=1&position=2&origin=search&related_id=271228 -->
<!-- https://www.flaticon.com/free-icon/left-arrow_271220?term=arrow+left&page=1&position=4&origin=search&related_id=271220 -->
<!-- https://www.flaticon.com/free-icon/back_11502464?term=double+arrow+left&page=1&position=5&origin=search&related_id=11502464 -->
<!-- https://www.flaticon.com/free-icon/forward_11502458?related_id=11502458 -->
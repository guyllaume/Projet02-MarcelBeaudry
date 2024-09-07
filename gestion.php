<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/annonces.css';
$bIsConnected = true; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu top">
        <div class="options">
            <label for="ddlOrdre">Ordre par</label>
            <select name="ddlOrdre" id="ddlOrdre">
                <option value="Asc">Ascendant</option>
                <option value="Desc" selected>Descendant</option>
            </select>
            <label for="ddlOrdre">Trie par</label>
            <select name="ddlTri" id="ddlTri">
                <option value="Date">Date de Parution</option>
                <option value="Auteur">Auteur</option>
                <option value="Categorie">Catégorie</option>
            </select>
            <label for="ddlOrdre" class="grow right">Pages</label>
            <select name="ddlNoPage" id="ddlNoPage">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <!-- A generer avec le nombre d'annonces et le nb d'annonces par page-->
            </select>
            <img class="icon disabled" id="btnFirstPage" src="images/first.png">
            <img class="icon disabled" id="btnPrecedentPage" src="images/precedent.png">
            <img class="icon" id="btnNextPage" src="images/next.png">
            <img class="icon" id="btnLastPage" src="images/last.png">
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces de l'utilisateur connecté -->
            <img class="vignette" src="images/manette.png"> <!-- src is from database -->
            <h2>1-</h2> <!-- 1 needs to represent the index of the for loop -->
            <div class="grow">
                <h3>999</h3> <!-- 999 = NoAnnonce -->
                <p><a href="description.php">Ceci est une manette</a></p> <!-- DescriptionAbregee -->
                <a href="contacter.php">Guyllaume Beaudry</a> <!-- nom prenom recu de NoUtilisateur  -->
            </div>
            <div class="right">
                <h3>0000/00/00 00h00</h3> <!-- Parution  -->
                <p>Électronique</p> <!-- description de categorie recu par NoCategorie  -->
                <p class="price">3$</p> <!-- Prix  -->
                <div class="bottomOptions">
                    <span class="smallDate">Dernière Mise À Jour 00/00/0000 00h00</span><!-- Mise a jour  -->
                    <button id="btnModifier">Modifier</button>
                    <button id="btnRetirer">Retirer</button>
                    <div class="center top">
                        <span>Inactif</span> <!-- État  -->
                        <button>Activer</button><!-- OU desactiver si deja actif  -->
                    </div>
                </div>
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
        let modal = document.getElementById("retirerModal");

        // Get the button that opens the modal - change for class eventually
        let btnRetirer = document.getElementById("btnRetirer");
        let btnConfirmRetirer = document.getElementById("btnConfirmRetirer"); //Devrait envoyer un post avec le NoAnnonce a retirer
        let btnCancelRetirer = document.getElementById("btnCancelRetirer");
        let btnModifier = document.getElementById("btnModifier");

        // Get the <span> element that closes the modal
        let span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal
        btnRetirer.onclick = function() {
            modal.style.display = "block";
        }
        btnConfirmRetirer.onclick = function() {
            modal.style.display = "none";
        }
        btnCancelRetirer.onclick = function() {
            modal.style.display = "none";
        }
        btnModifier.onclick = function() {
            window.location.href = "modifierAnnonce.php";
        }
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php
require_once 'pied-page.php';

?>
    
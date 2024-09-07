<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/annonces.css';
$bIsConnected = true; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu top">
        <form action="annonces.php" method="post"></form>
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
                <label for="ddlOrdre">Nombre d'annonces</label>
                <select name="ddlNbPages" id="ddlNbPages">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                <div class="grow center">
                    <input type="text" id="txtRecherche" name="txtRecherche">
                    <img class="icon" src="photos-annonces/loupe.png" id="btnRecherche">
                </div>
                <label for="ddlOrdre">Pages</label>
                <select name="ddlNoPage" id="ddlNoPage">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <!-- A generer avec le nombre d'annonces et le nb d'annonces par page-->
                </select>
                <img class="icon disabled" id="btnFirstPage" src="photos-annonces/first.png">
                <img class="icon disabled" id="btnPrecedentPage" src="photos-annonces/precedent.png">
                <img class="icon" id="btnNextPage" src="photos-annonces/next.png">
                <img class="icon" id="btnLastPage" src="photos-annonces/last.png">
            </div>
        </form>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <img class="vignette" src="photos-annonces/manette.png"> <!-- src is from database -->
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
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <img class="vignette" src="photos-annonces/manette.png"> <!-- src is from database -->
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
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <img class="vignette" src="photos-annonces/manette.png"> <!-- src is from database -->
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
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <img class="vignette" src="photos-annonces/manette.png"> <!-- src is from database -->
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
            </div>
        </div>
    </div>

<?php
require_once 'pied-page.php';

?>
<!-- <a href="https://www.flaticon.com/free-icons/search" title="search icons">Search icons created by Pixel perfect - Flaticon</a>  -->
<!-- <a href="https://www.flaticon.com/free-icons/arrows" title="arrows icons">Arrows icons created by gravisio - Flaticon</a> -->
<!-- https://www.flaticon.com/free-icon/right-arrow_271228?term=arrow+left&page=1&position=2&origin=search&related_id=271228 -->
<!-- https://www.flaticon.com/free-icon/left-arrow_271220?term=arrow+left&page=1&position=4&origin=search&related_id=271220 -->
<!-- https://www.flaticon.com/free-icon/back_11502464?term=double+arrow+left&page=1&position=5&origin=search&related_id=11502464 -->
<!-- https://www.flaticon.com/free-icon/forward_11502458?related_id=11502458 -->
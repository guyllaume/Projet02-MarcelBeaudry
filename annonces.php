<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/annonces.css';
$bIsConnected = true; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu top">
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <h2>1-</h2> <!-- 1 needs to represent the index of the for loop -->
            <img src="images/manette.png"> <!-- src is from database -->
            <div class="grow">
                <h3>999</h3> <!-- 999 = NoAnnonce -->
                <p><a href="description.php">Ceci est une manette</a></p> <!-- DescriptionAbregee -->
                <a href="contacter.php">Guyllaume Beaudry</a> <!-- nom prenom recu de NoUtilisateur  -->
            </div>
            <div>
                <h3>0000/00/00 00h00</h3> <!-- Parution  -->
                <p>Électronique</p> <!-- description de categorie recu par NoCategorie  -->
                <p>3$</p> <!-- Prix  -->
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <h2>1-</h2> <!-- 1 needs to represent the index of the for loop -->
            <img src="images/manette.png"> <!-- src is from database -->
            <div class="grow">
                <h3>999</h3> <!-- 999 = NoAnnonce -->
                <p><a href="description.php">Ceci est une manette</a></p> <!-- DescriptionAbregee -->
                <a href="contacter.php">Guyllaume Beaudry</a> <!-- nom prenom recu de NoUtilisateur  -->
            </div>
            <div>
                <h3>0000/00/00 00h00</h3> <!-- Parution  -->
                <p>Électronique</p> <!-- description de categorie recu par NoCategorie  -->
                <p>3$</p> <!-- Prix  -->
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <h2>1-</h2> <!-- 1 needs to represent the index of the for loop -->
            <img src="images/manette.png"> <!-- src is from database -->
            <div class="grow">
                <h3>999</h3> <!-- 999 = NoAnnonce -->
                <p><a href="description.php">Ceci est une manette</a></p> <!-- DescriptionAbregee -->
                <a href="contacter.php">Guyllaume Beaudry</a> <!-- nom prenom recu de NoUtilisateur  -->
            </div>
            <div>
                <h3>0000/00/00 00h00</h3> <!-- Parution  -->
                <p>Électronique</p> <!-- description de categorie recu par NoCategorie  -->
                <p>3$</p> <!-- Prix  -->
            </div>
        </div>
        <div class="annonce-card"> <!--Affiche seulement les annonces ACTIF (1) -->
            <h2>1-</h2> <!-- 1 needs to represent the index of the for loop -->
            <img src="images/manette.png"> <!-- src is from database -->
            <div class="grow">
                <h3>999</h3> <!-- 999 = NoAnnonce -->
                <p><a href="description.php">Ceci est une manette</a></p> <!-- DescriptionAbregee -->
                <a href="contacter.php">Guyllaume Beaudry</a> <!-- nom prenom recu de NoUtilisateur  -->
            </div>
            <div>
                <h3>0000/00/00 00h00</h3> <!-- Parution  -->
                <p>Électronique</p> <!-- description de categorie recu par NoCategorie  -->
                <p>3$</p> <!-- Prix  -->
            </div>
        </div>
    </div>

<?php
require_once 'pied-page.php';

?>
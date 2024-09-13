<?php
$strNomFichierCSS = 'style/style.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu top">
        <h1>Bienvenue aux petites annonces GG</h1>
        <p>Veuiller vous créer un compte ou vous connecter</p>
        <button type="button" id="btnCreationDB">Création de la base de données</button>
        <button type="button" id="btnInjectionDB">Injection de données de la base de données</button>
        <span id="message">&nbsp;</span>
    </div>

    <script>
        let message = document.getElementById('message');
        document.getElementById('btnCreationDB').addEventListener('click', function() {
            fetch('dataBaseCreation.php')
                .then(response => response.text())
                .then(data => {
                    message.innerHTML = data;
                })
                .catch(error => {
                    message.innerHTML = 'Une erreur s\'est produite';
                    console.error('Erreur:', error);
                })
        })

        document.getElementById('btnInjectionDB').addEventListener('click', function() {
            fetch('dataBaseImport_csv.php')
                .then(response => response.text())
                .then(data => {
                    message.innerHTML = data;
                })
                .catch(error => {
                    message.innerHTML = 'Une erreur s\'est produite';
                    console.error('Erreur:', error);
                })
        })
    </script>
<?php
require_once 'pied-page.php';

?>
    
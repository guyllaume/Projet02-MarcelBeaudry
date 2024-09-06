<?php
$strTitreApplication = 'Projet PHP';
$strNomFichierCSS = 'style/loginSignup.css';
$bIsConnected = false; // VÉRIFIE SI L'UTILISATEUR EST CONNECTÉ
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';

?>
    <div class="contenu">
        <div class="card">
            <div class="card-header">
                <h1>Signup</h1>
            </div>
            
            <div class="card-body">
                <form action="signup.php" method="post">
                    <div class="form-group">
                        <div class="form-subgroup">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email">
                                <span class="error" id="errorEmail">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="email">Confirmation Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="confirmationEmail" name="email">
                                <span class="error" id="errorConfirmationEmail">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="password">Mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <span class="error" id="errorPassword">&nbsp;</span>
                            </div>
                        </div>
                        <div class="form-subgroup">
                            <label for="password">Confirmation Mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmationPassword" name="password">
                                <span class="error" id="errorConfirmationPassword">&nbsp;</span>
                            </div>
                        </div>
                    <button type="button" id="btnSubmit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnSubmit').addEventListener('click', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordRegex = /^[a-z0-9]{5,15}$/;
            const email = document.getElementById("email").value;
            const confirmationEmail = document.getElementById("confirmationEmail").value;
            const password = document.getElementById("password").value;
            const confirmationPassword = document.getElementById("confirmationPassword").value;
            let informationIsCorrect = true;
            if(!emailRegex.test(email)) {
                document.getElementById("errorEmail").innerHTML = "L'email n'est pas valide";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorEmail").innerHTML = "&nbsp;";
            }
            if(!passwordRegex.test(password)) {
                document.getElementById("errorPassword").innerHTML = "Le mot de passe doit contenir entre 5 et 15 caractères";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorPassword").innerHTML = "&nbsp;";
            }
            if(email != confirmationEmail) {
                document.getElementById("errorConfirmationEmail").innerHTML = "Les emails ne sont pas identiques";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorConfirmationEmail").innerHTML = "&nbsp;";
            }
            if(password != confirmationPassword) {
                document.getElementById("errorConfirmationPassword").innerHTML = "Les mots de passe ne sont pas identiques";
                informationIsCorrect = false;
            }else{
                document.getElementById("errorConfirmationPassword").innerHTML = "&nbsp;";
            }
            if(informationIsCorrect) {
                this.form.submit();
            }
        })
    </script>

<?php
require_once 'pied-page.php';

?>
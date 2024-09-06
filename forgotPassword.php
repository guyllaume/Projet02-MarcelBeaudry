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
                <h1>Mot de Passe Oublié?</h1>
            </div>
            <div class="card-body">
                <form action="forgotPassword.php" method="post">
                    <div class="form-group">
                        <div class="form-subgroup">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control" id="email" name="email">
                                <span class="error" id="errorEmail">&nbsp;</span>
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
        const email = document.getElementById("email").value;
        let informationIsCorrect = true;
        if(!emailRegex.test(email)) {
            document.getElementById("errorEmail").innerHTML = "L'email n'est pas valide";
            informationIsCorrect = false;
        }else{
            document.getElementById("errorEmail").innerHTML = "&nbsp;";
        }
        if(informationIsCorrect) {
            this.form.submit();
        }
    })
</script>

<?php
require_once 'pied-page.php';

?>
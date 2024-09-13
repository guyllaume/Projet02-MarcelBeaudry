<?php
$strNomFichierCSS = 'style/annonces.css';
require_once 'librairies-communes-2018-mm-jj.php';
require_once 'en-tete.php';
function generateUniqueFileName($originalName) {
    // Get the file extension
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    
    // Create a base filename with timestamp
    $baseName = 'file_' . time();
    
    // Ensure the base name plus extension does not exceed 50 characters
    $maxBaseNameLength = 50 - strlen($fileExtension) - 1;
    
    if (strlen($baseName) > $maxBaseNameLength) {
        $baseName = substr($baseName, 0, $maxBaseNameLength);
    }
    
    // Construct the full filename
    $newFileName = $baseName . '.' . $fileExtension;
    
    return $newFileName;
}

$message = '&nbsp;';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['descriptionAbregee'], $_POST['descriptionComplete'], $_POST['categorie'], $_POST['prix'], $_POST['etat'], $_POST['NoAnnonceToModify'], $_POST['dateParution'])) {

        $dateParution = $_POST['dateParution'];
        $dateParution == date('Y-m-d') ? $dateParution = date('Y-m-d H:i:s') : $dateParution = $dateParution . ' 00:00:00';
        $descriptionAbregee = $_POST['descriptionAbregee'];
        $descriptionComplete = $_POST['descriptionComplete'];
        $categ = $_POST['categorie'];
        $prix = $_POST['prix'];
        $etat = $_POST['etat'];
        $user_id = $_SESSION['user_id'];
        $NoAnnonceToModify = $_POST['NoAnnonceToModify'];
    
        if(isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
            // Upload de l'image dans le dossier photos-annonce
            $targetDir = "photos-annonce/";

            // Creation du dossier si inexistant
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Création du chemin relatif de l'image
            $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1; // check if upload is ok

            // Vérification du type de l'image
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $message = "Le fichier est une image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                $message = "Le fichier n'est pas une image.";
                $uploadOk = 0;
            }

             // Check file size (limit set to 5MB in this example)
            if ($_FILES["fileToUpload"]["size"] > 5000000) {
                $message = "Désolez, le fichier est trop volumineux.";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($targetFile)) {
                // Compare file contents
                $existingFileContent = file_get_contents($targetFile);
                $newFileContent = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
                
                if ($existingFileContent === $newFileContent) {
                    // The files are identical
                    $message = "Le fichier est déjà présent et identique.";
                } else {
                    // Generate a unique filename
                    $fileBaseName = basename($targetFile);
                    $newFileName = generateUniqueFileName($fileBaseName);
                    $targetFile = $targetDir . $newFileName;
                }
            }

             // Check if everything is okay before uploading
            if ($uploadOk == 1) {
                if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                    $message = "Désolez, une erreur s'est produite lors de l'upload.";
                }else{
                    $conn = connectDB();

                    $stmt = $conn->prepare("UPDATE annonces 
                                            SET Parution = ?,
                                                DescriptionAbregee = ?, 
                                                DescriptionComplete = ?, 
                                                Categorie = ?, 
                                                Prix = ?, 
                                                Photo = ?, 
                                                Etat = ?, 
                                                MiseAJour = NOW() 
                                            WHERE NoAnnonce = ?");

                    $stmt->bindParam(1, $dateParution);
                    $stmt->bindParam(2, $descriptionAbregee);
                    $stmt->bindParam(3, $descriptionComplete);
                    $stmt->bindParam(4, $categ);
                    $stmt->bindParam(5, $prix);
                    $stmt->bindParam(6, $targetFile);
                    $stmt->bindParam(7, $etat);
                    $stmt->bindParam(8, $NoAnnonceToModify);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                        $message = "Modification effectuée avec succès";
                    } else {
                        $message = "Aucune modification n'a été effectuée.";
                    }
                    $conn = null;
                }
            }
        }else{
            $conn = connectDB();

            $stmt = $conn->prepare("UPDATE annonces 
                                    SET Parution = ?,
                                        DescriptionAbregee = ?, 
                                        DescriptionComplete = ?, 
                                        Categorie = ?, 
                                        Prix = ?, 
                                        Etat = ?, 
                                        MiseAJour = NOW() 
                                    WHERE NoAnnonce = ?");

            $stmt->bindParam(1, $dateParution);
            $stmt->bindParam(2, $descriptionAbregee);
            $stmt->bindParam(3, $descriptionComplete);
            $stmt->bindParam(4, $categ);
            $stmt->bindParam(5, $prix);
            $stmt->bindParam(6, $etat);
            $stmt->bindParam(7, $NoAnnonceToModify);
            $stmt->execute();
            $conn = null;
            if ($stmt->rowCount() > 0) {
                $message = "Modification effectuée avec succès";
            } else {
                $message = "Aucune modification n'a été effectuée.";
            }
        }
    }else if(isset($_POST['NoAnnonceToModify'])) {
        $NoAnnonceToModify = $_POST['NoAnnonceToModify'];
    }else{
        $message = 'Probleme de requete serveur';
    }
}

// Affichage de la page
$conn = connectDB();
$stmt = $conn->prepare("SELECT * FROM annonces WHERE NoAnnonce = ?");
$stmt->bindParam(1, $NoAnnonceToModify);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rowAnnonce = $result[0];
$conn = null;

$dateOnlyParution = substr($rowAnnonce['Parution'], 0, 10);

?>
<div class="contenu">
    <form class="ajouter-annonce-form" action="modifierAnnonce.php" method="post" enctype="multipart/form-data">
        <div class="ajouter-annonce-card">
            <h1>Modifier une Annonce</h1>
            <div>
                <label for="dateParution">Date de Parution</label>
                <input type="date" name="dateParution" id="dateParution" min="<?php echo $dateOnlyParution ?>" max="<?php echo date('Y-m-d')?>" value="<?php echo $dateOnlyParution?>" required>
            </div>
            <div>
                <label for="descriptionAbregee">Description Abrégée</label>
                <textarea rows="2" cols="50" maxlength="50" name="descriptionAbregee" id="descriptionAbregee" required><?php echo $rowAnnonce['DescriptionAbregee'];?></textarea>
            </div>
            <div>
                <label for="descriptionComplete">Description Complète</label>
                <textarea rows="5" cols="50" maxlength="250" name="descriptionComplete" id="descriptionComplete" required><?php echo $rowAnnonce['DescriptionComplete'];?></textarea>
            </div>
            <div>
                <label for="categorie">Catégorie</label>
                <select name="categorie" id="categorie" required>
                    <?php
                    $conn = connectDB();
                    $stmt = $conn->prepare("SELECT * FROM categories");
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                        echo "<option value='" . $row['NoCategorie'] . "' ". ($rowAnnonce['Categorie'] == $row['NoCategorie'] ? 'selected' : '') . ">" . $row['Description'] . "</option>";
                    }
                    $conn = null;
                    ?>
                </select>
            </div>
            <div>
                <label for="prix">Prix</label>
                <input type="text" name="prix" id="prix" value="<?php echo $rowAnnonce['Prix'] ?>" required>
            </div>
            <div>
                <label for="fileToUpload">Ajouter une nouvelle image</label>
                <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
            </div>
            <div>
                <label for="previousPhoto">Image entreposée</label>
                <img class="previewImage" src="<?php echo $rowAnnonce['Photo'] ?>">
            </div>
            <div>
                <label for="etat">État</label>
                <select name="etat" id="etat" required>
                    <option value="1" <?php echo $rowAnnonce['Etat'] == 1 ? 'selected' : ''?>>Actif</option>
                    <option value="2" <?php echo $rowAnnonce['Etat'] != 1 ? 'selected' : ''?>>Inactif</option>
                </select>
            </div>
            <input type="hidden" name="NoAnnonceToModify" value="<?php echo $NoAnnonceToModify ?>">
            <span class="error" id="errorMessage">&nbsp;</span>
            <span class="success" id="successMessage"><?php echo $message?></span>
            <button type="button" id="btnSubmit">Modifier Annonce</button>
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
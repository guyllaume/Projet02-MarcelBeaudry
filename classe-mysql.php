<?php
   /*
   |----------------------------------------------------------------------------------------|
   | class mysql
   |----------------------------------------------------------------------------------------|
   */
   class mysql {
      /*
      |----------------------------------------------------------------------------------|
      | Attributs
      |----------------------------------------------------------------------------------|
      */
      public $cBD = null;                       /* Identifiant de connexion */
      public $listeEnregistrements = null;      /* Liste des enregistrements retournés */
      public $nomFichierInfosSensibles = "";    /* Nom du fichier 'InfosSensibles' */
      public $nomBD = "";                       /* Nom de la base de données */
      public $OK = false;                       /* Opération réussie ou non */
      public $requete = "";                     /* Requête exécutée */
      /*
      |----------------------------------------------------------------------------------|
      | __construct
      |----------------------------------------------------------------------------------|
      */
      function __construct($strNomBD, $strNomFichierInfosSensibles) {
         $this->nomBD = $strNomBD;
         $this->nomFichierInfosSensibles = $strNomFichierInfosSensibles;
         $this->connexion();
         $this->selectionneBD();
      }
      /*
      |----------------------------------------------------------------------------------|
      | connexion()
      |----------------------------------------------------------------------------------|
      */
      function connexion() {
         $this->cBD = mysqli_connect("localhost", "root", "", $this->nomBD);

         if ($this->cBD === false) {
             die("Problème de connexion… Message d'erreur retourné par PHP: " . mysqli_connect_error());
         }
         return $this->cBD;
      }
      /*
      |----------------------------------------------------------------------------------|
      | copieEnregistrements
      |----------------------------------------------------------------------------------|
      */
      function copieEnregistrements($strNomTableSource, $strListeChampsSource, $strNomTableCible, $strListeChampsCible, $strListeConditions="") {
         if (empty($strListeChampsCible)) {
             $strListeChampsCible = $strListeChampsSource;
         }
     
         $this->requete = "INSERT INTO $strNomTableCible ($strListeChampsCible) SELECT $strListeChampsSource FROM $strNomTableSource";
     
         if (!empty($strListeConditions)) {
             $this->requete .= " WHERE $strListeConditions";
         }
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | creeTable
      |----------------------------------------------------------------------------------|
      */
      function creeTable($strNomTable) {
         $args = func_get_args();
         array_shift($args);
     
         $this->requete = "CREATE TABLE $strNomTable (";
     
         for ($i = 0; $i < count($args) - 1; $i += 2) {
             $this->requete .= "{$args[$i]} {$args[$i + 1]}, ";
         }
     
         $this->requete .= "{$args[count($args) - 1]}) ENGINE=InnoDB";
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true: $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | creeTableGenerique()
      |----------------------------------------------------------------------------------|
      */
      function creeTableGenerique($strNomTable, $strDefinitions, $strCles) {
         $this->requete = "CREATE TABLE $strNomTable (";
     
         $fields = explode(';', $strDefinitions);
         foreach ($fields as $field) {
             list($type, $name) = explode(',', $field);
     
             switch ($type[0]) {
                 case 'B': // Boolean
                     $this->requete .= "$name BOOL, ";
                     break;
                 case 'C': // Decimal
                     $size = substr($type, 1);
                     $this->requete .= "$name DECIMAL($size), ";
                     break;
                 case 'D': // Date
                     $this->requete .= "$name DATE, ";
                     break;
                 case 'E': // Integer
                     $this->requete .= "$name INT, ";
                     break;
                 case 'F': // Fixed string
                     $size = substr($type, 1);
                     $this->requete .= "$name CHAR($size), ";
                     break;
                 case 'M': // Monetary
                     $this->requete .= "$name DECIMAL(10,2), ";
                     break;
                 case 'N': // Non-null integer
                     $this->requete .= "$name INT NOT NULL, ";
                     break;
                 case 'V': // Variable string
                     $size = substr($type, 1);
                     $this->requete .= "$name VARCHAR($size), ";
                     break;
             }
         }
     
         $this->requete .= "PRIMARY KEY($strCles)) ENGINE=InnoDB";
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | deconnexion
      |----------------------------------------------------------------------------------|
      */
      function deconnexion() {
         mysqli_close($this->cBD);
      }
      /*
      |----------------------------------------------------------------------------------|
      | insereEnregistrement
      |----------------------------------------------------------------------------------|
      */
      function insereEnregistrement($strNomTable) {
         $values = func_get_args();
         
         array_shift($values);
     
         $escapedValues = array_map(function($value) {
             if (is_string($value)) {
                 return "'" . mysqli_real_escape_string($this->cBD, $value) . "'";
             } elseif (is_bool($value)) {
                 return $value ? '1' : '0';
             } elseif (is_null($value)) {
                 return 'NULL';
             } else {
                 return $value;
             }
         }, $values);
     
         $valuesList = implode(', ', $escapedValues);
     
         $this->requete = "INSERT INTO $strNomTable VALUES ($valuesList)";
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | modifieChamp
      |----------------------------------------------------------------------------------|
      */
      function modifieChamp($strNomTable, $strNomChamp, $strNouvelleDefinition) {
         $this->requete = "ALTER TABLE $strNomTable CHANGE $strNomChamp $strNouvelleDefinition";
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | selectionneBD()
      |----------------------------------------------------------------------------------|
      */
      function selectionneBD() {
         mysqli_select_db($this->cBD, $this->nomBD) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | supprimeEnregistrements
      |----------------------------------------------------------------------------------|
      */
      function supprimeEnregistrements($strNomTable, $strListeConditions="") {
         $this->requete = "DELETE FROM $strNomTable";
     
         if (!empty($strListeConditions)) {
             $this->requete .= " WHERE $strListeConditions";
         }
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | supprimeTable()
      |----------------------------------------------------------------------------------|
      */
      function supprimeTable($strNomTable) {
         $this->requete = "DROP TABLE IF EXISTS $strNomTable";
     
         mysqli_query($this->cBD, $this->requete) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
      /*
      |----------------------------------------------------------------------------------|
      | afficheInformationsSurBD()
      | Affiche la structure et le contenu de chaque table de la base de données recherchée
      |----------------------------------------------------------------------------------|
      */
      function afficheInformationsSurBD()
      {/* Si applicable, récupération du nom de la table recherchée */
      $strNomTableRecherchee = "";
      if (func_num_args() == 1) {
         $strNomTableRecherchee = func_get_arg(0);
      }
      
      /* Variables de base pour les styles */
      $strTable = "border-collapse:collapse;";
      $strCommande = "font-family:verdana; font-size:12pt; font-weight:bold; color:black; border:solid 1px black; padding:3px;";
      $strMessage = "font-family:verdana; font-size:10pt; font-weight:bold; color:red;";
      $strBorduresMessage = "border:solid 1px red; padding:3px;";
      $strContenu = "font-family:verdana; font-size:10pt; color:blue;";
      $strBorduresContenu = "border:solid 1px red; padding:3px;";
      $strTypeADefinir = "color:red;font-weight:bold;";
      $strDetails = "color:magenta;";
      
      /* Application des styles */
      $sTable = "style=\"$strTable\"";
      $sCommande = "style=\"$strCommande\"";
      $sMessage = "style=\"$strMessage\"";
      $sMessageAvecBordures = "style=\"$strMessage $strBorduresMessage\"";
      $sContenu = "style=\"$strContenu\"";
      $sContenuAvecBordures = "style=\"$strContenu $strBorduresContenu\"";
      $sTypeADefinir = "style=\"$strTypeADefinir\"";
      $sDetails = "style=\"$strDetails\"";

      /* --- Entreposage des noms de table --- */
      $ListeTablesBD = array_column(mysqli_fetch_all(mysqli_query($this->cBD, 'SHOW TABLES')),0);
      $intNbTables = count($ListeTablesBD);
      
      /* --- Parcours de chacune des tables --- */
      echo "<span $sCommande>Informations sur " . (!empty($strNomTableRecherchee) ? "la table '$strNomTableRecherchee' de " : "") . "la base de données '$this->nomBD'</span><br />";
      $binTablePresente = false;
      for ($i=0; $i<$intNbTables; $i++)
      {
         /* Récupération du nom de la table courante */
         $strNomTable = $ListeTablesBD[$i];
         if (empty($strNomTableRecherchee) || strtolower($strNomTable) == strtolower($strNomTableRecherchee)) {
            $binTablePresente = true;
            echo "<p $sMessage>Table no ".strval($i+1)." : ".$strNomTable."</p>";
         
            /* Récupération des enregistrements de la table courante */
            $ListeEnregistrements = mysqli_query($this->cBD, "SELECT * FROM $strNomTable");

            /* Décompte du nombre de champs et d'enregistrements de la table courante */
            $NbChamps = mysqli_field_count($this->cBD);
            $NbEnregistrements = mysqli_num_rows($ListeEnregistrements);
            echo "<p $sContenu>$NbChamps champs ont été détectés dans la table.<br />";
            echo "    $NbEnregistrements enregistrements ont été détectés dans la table.</p>";
            
            /* Affichage de la structure de table courante */
            echo "<p $sContenu>";
            $j=0;
            $tabNomChamp = array();
            while ($champCourant = $ListeEnregistrements->fetch_field()) {
               $intDivAjustement = 1;
               $tabNomChamp[$j] = $champCourant->name;
               $strType = $champCourant->type;
               switch ($strType) {
                  case 1   : $strType = "BOOL"; break;
                  case 3   : $strType = "INTEGER"; break;
                  case 10  : $strType = "DATE"; break;
                  case 12  : $strType = "DATETIME"; break;
                  case 246 : $strType = "DECIMAL"; break;
                  case 253 : $strType = "VARCHAR"; 
                             /* Ajustement temporaire */
                             if ($_SERVER["SERVER_NAME"] == "lmbrousseau.ca") { $intDivAjustement = 3; }
                             break;
                  case 254 : $strType = "CHAR"; break;
                  default  : $strType = "<span $sTypeADefinir>$strType à définir</span>"; break;
               }
               $strLongueur = intval($champCourant->length) / $intDivAjustement;
               $intDetails = $champCourant->flags;
               $strDetails = "";
               if ($intDetails & 1     ) $strDetails .= "[NOT_NULL] ";
               if ($intDetails & 2     ) $strDetails .= "<span style=\"font-weight:bold;\">[PRI_KEY]</span> ";
               if ($intDetails & 4     ) $strDetails .= "[UNIQUE_KEY] ";
               if ($intDetails & 16    ) $strDetails .= "[BLOB] ";
               if ($intDetails & 32    ) $strDetails .= "[UNSIGNED] ";
               if ($intDetails & 64    ) $strDetails .= "[ZEROFILL] ";
               if ($intDetails & 128   ) $strDetails .= "[BINARY] ";
               if ($intDetails & 256   ) $strDetails .= "[ENUM] ";
               if ($intDetails & 512   ) $strDetails .= "[AUTO_INCREMENT] ";
               if ($intDetails & 1024  ) $strDetails .= "[TIMESTAMP] ";
               if ($intDetails & 2048  ) $strDetails .= "[SET] ";
               if ($intDetails & 32768 ) $strDetails .= "[NUM] ";
               if ($intDetails & 16384 ) $strDetails .= "[PART_KEY] ";
               if ($intDetails & 32768 ) $strDetails .= "[GROUP] "; 
               if ($intDetails & 65536 ) $strDetails .= "[UNIQUE] ";
               echo ($j+1).". $tabNomChamp[$j], $strType($strLongueur) <span $sDetails>$strDetails</span><br />";
               $j++;
            }
            echo "</p>";

            /* Affichage des enregistrements composant la table courante */
            echo "<table $sTable>";
            echo "<tr>";
            for ($k=0; $k<$NbChamps; $k++)
               echo "<td $sMessageAvecBordures>" . $tabNomChamp[$k] . "</td>";
            echo "</tr>";               
            if (empty($NbEnregistrements)) {
               echo "<tr>";
               echo "<td $sContenuAvecBordures colspan=\"$NbChamps\">";
               echo " Aucun enregistrement";
               echo "</td>";
               echo "</tr>";
            }
            while ($listeChampsEnregistrement = $ListeEnregistrements->fetch_row()) {
               echo "<tr>";
               echo "<tr>";
               for ($j=0; $j<count($listeChampsEnregistrement); $j++)
                  echo "      <td $sContenuAvecBordures>" . $listeChampsEnregistrement[$j] . "</td>";
               echo "   </tr>";
            }
            echo "</table>";
            $ListeEnregistrements->free();
         }
      }
      if (!$binTablePresente)
         echo "<p $sMessage>Aucune table !</p>";
      }
   }
?>
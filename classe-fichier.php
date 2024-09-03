<?php
class fichier
{
   /*
   |----------------------------------------------------------------------------------|
   | attributs
   |----------------------------------------------------------------------------------|
   */
   private $fp = null;
   public $intNbLignes = null;
   public $intTaille = null;
   public $strLigneCourante = null;
   public $strNom = null;
   public $strContenu = null;
   public $strContenuHTML = null;
   public $tContenu = array();
   public $strMode = null;
   /*
   |----------------------------------------------------------------------------------|
   | constructeur
   |----------------------------------------------------------------------------------|
   */
   function __construct($strNomFichier)
   {
      $this->strNom = $strNomFichier;
      if (func_num_args() == 2) {
         $this->ouvre(func_get_arg(1));
      }
   }
   /*
   |----------------------------------------------------------------------------------|
   | chargeEnMemoire() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://php.net/manual/fr/function.count.php
   |       http://ca.php.net/manual/fr/function.file.php
   |       http://php.net/manual/fr/function.file-get-contents.php
   |       http://ca.php.net/manual/fr/function.str-replace.php
   |       http://php.net/manual/fr/function.strlen.php
   |----------------------------------------------------------------------------------|
   */
   function chargeEnMemoire()
   {
      /* Récupère toutes les lignes et les entrepose dans un tableau
      Retrait de tous les CR et LF
      Récupère le nombre de lignes */
      $this->tContenu = file($this->strNom);
      $this->tContenu = str_replace("\n", "", str_replace("\r", "", $this->tContenu));
      $this->intNbLignes = count($this->tContenu);
      /* Récupère toutes les lignes et les entrepose dans une chaîne */
      $this->strContenu = file_get_contents($this->strNom);
      $this->intTaille = strlen($this->strContenu);
      /* Entrepose la chaîne résultante dans une autre après l'avoir XHTMLisé ! */
      $this->strContenuHTML = str_replace(
         "\n\r",
         "<br />",
         str_replace("\r\n", "<br />", $this->strContenu)
      );
   }

   /*
   |----------------------------------------------------------------------------------|
   | compteLignes() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.count.php
   |       http://ca.php.net/manual/fr/function.file.php
   |----------------------------------------------------------------------------------|
   */
   function compteLignes()
   {
      $this->intNbLignes = -1;
      if ($this->existe()) {
         $this->intNbLignes = count(file($this->strNom));
      }
      return $this->intNbLignes;
   }

   /*
   |----------------------------------------------------------------------------------|
   | copie() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.count.php
   |       http://ca.php.net/manual/fr/function.file.php
   |----------------------------------------------------------------------------------|
   */
   function copie()
   {
      if (func_num_args() == 1) {
         $backupFileName = "nouveau-" . pathinfo(func_get_arg(0), PATHINFO_FILENAME);
         $backupFilePath = pathinfo(func_get_arg(0), PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $backupFileName;
         if (file_exists($backupFileName)) {
            return false;
         } else {
            return copy(func_get_arg(0), $backupFilePath);
         }
      } else {
         $backupFileName = pathinfo($this->strNom, PATHINFO_FILENAME) . "(001).txt";
         $newFileNameFound = false;
         $iterator = 2;
         do {
            if (file_exists($backupFileName)) {
               if ($iterator < 10) {
                  $backupFileName = pathinfo($this->strNom, PATHINFO_FILENAME) . "(00" . $iterator++ . ").txt";
               } else if ($iterator < 100) {
                  $backupFileName = pathinfo($this->strNom, PATHINFO_FILENAME) . "(0" . $iterator++ . ").txt";
               } else {
                  $backupFileName = pathinfo($this->strNom, PATHINFO_FILENAME) . "(" . $iterator++ . ").txt";
               }
            } else {
               $newFileNameFound = true;
            }
         } while (!$newFileNameFound);

         $backupFilePath = pathinfo($this->strNom, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $backupFileName;
         return copy($this->strNom, $backupFilePath);
      }
   }


   /*
   |----------------------------------------------------------------------------------|
   | detecteFin() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://php.net/manual/fr/function.feof.php
   |----------------------------------------------------------------------------------|
   */
   function detecteFin()
   {
      $binVerdict = true;
      if ($this->fp) {
         $binVerdict = feof($this->fp);
      }
      return $binVerdict;
   }

   /*
   |----------------------------------------------------------------------------------|
   | ecritLigne() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://php.net/manual/fr/function.fputs.php
   |       http://php.net/manual/fr/function.gettype.php
   |----------------------------------------------------------------------------------|
   */
   function ecritLigne($strLigneCourante, $binSaut_intNbLignesSaut = false)
   {
      $binVerdict = fputs($this->fp, $strLigneCourante);
      if ($binVerdict) {
         switch (gettype($binSaut_intNbLignesSaut)) {
            case "integer":
               for ($i = 1; $i <= $binSaut_intNbLignesSaut && $binVerdict; $i++) {
                  $binVerdict = fputs($this->fp, "\r\n");
               }
               break;
            case "boolean":
               if ($binSaut_intNbLignesSaut) {
                  $binVerdict = fputs($this->fp, "\r\n");
               }
               break;
         }
      }
      return $binVerdict;
   }

   /*
   |----------------------------------------------------------------------------------|
   | existe() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.file-exists.php
   |----------------------------------------------------------------------------------|
   */
   function existe()
   {
      return file_exists($this->strNom);
   }

   /*
   |----------------------------------------------------------------------------------|
   | ferme() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.fclose.php
   |----------------------------------------------------------------------------------|
   */
   function ferme()
   {
      $binVerdict = false;
      if ($this->fp) {
         $binVerdict = fclose($this->fp);
      }
      return $binVerdict;
   }

   /*
   |----------------------------------------------------------------------------------|
   | identiqueA() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.fclose.php
   |----------------------------------------------------------------------------------|
   */
   function identiqueA($strNomFichierAComparer)
   {
      if (!file_exists($strNomFichierAComparer)) {
         return false;
      }

      $fileContent1 = file_get_contents($this->strNom);
      $fileContent2 = file_get_contents($strNomFichierAComparer);

      return $fileContent1 === $fileContent2;
   }

   /*
   |----------------------------------------------------------------------------------|
   | litDonneesLigne() (2018-03-13; 2019-03-12; 2020-03-22)
   | Ref. : http://php.net/manual/fr/function.array-combine.php
   |        http://php.net/manual/fr/function.func-get-arg.php
   |        http://php.net/manual/fr/function.func-num-args.php
   |        http://stackoverflow.com/questions/6814760/php-using-explode-function-to-assign-values-to-an-associative-array
   |----------------------------------------------------------------------------------|
   */
   function litDonneesLigne(&$tValeurs, $strSeparateur)
   { /*0*/
      for ($i = 2; $i <= func_num_args() - 1; $i++) { /*1*/
         $tValeurs[func_get_arg($i)] = func_get_arg($i); /*2*/
      } /*3*/
      $tValeurs = array_combine($tValeurs, explode($strSeparateur, $this->litLigne())); /*4*/
   }

   /*
   |----------------------------------------------------------------------------------|
   | litLigne() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.fgets.php
   |       http://ca.php.net/manual/fr/function.str-replace.php
   |----------------------------------------------------------------------------------|
   */
   function litLigne()
   {
      $this->strLigneCourante = str_replace(
         "\n",
         "",
         str_replace("\r", "", fgets($this->fp))
      );
      return $this->strLigneCourante;
   }

   /*
   |----------------------------------------------------------------------------------|
   | ouvre() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.fopen.php
   |       http://ca.php.net/manual/fr/function.strtoupper.php
   |----------------------------------------------------------------------------------|
   */
   function ouvre($strMode = "L")
   {
      switch (strtoupper($strMode)) {
         case "A":
            $this->strMode = "a";
            break;
         case "E":
         case "W":
            $this->strMode = "w";
            break;
         case "L":
         case "R":
            $this->strMode = "r";
            break;
      }
      $this->fp = fopen($this->strNom, $this->strMode);
      return $this->fp;
   }

   /*
   |----------------------------------------------------------------------------------|
   | renommePour() (2018-03-13; 2019-03-12; 2020-03-22)
   | Réf.: http://ca.php.net/manual/fr/function.fopen.php
   |       http://ca.php.net/manual/fr/function.strtoupper.php
   |----------------------------------------------------------------------------------|
   */
   function renommePour($nouveauNom)
   {
       if (!$this->existe()) {
           return false;
       }
   
       $nouveauChemin = pathinfo($this->strNom, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $nouveauNom;
   
       if (rename($this->strNom, $nouveauChemin)) {
           $this->strNom = $nouveauNom;
           return true;
       }
   
       return false;
   }


}
?>
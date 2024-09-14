<?php
   require_once '424x-cgodin-qc-ca.php';
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
         if($this->connexion())
            $this->selectionneBD();
      }
      /*
      |----------------------------------------------------------------------------------|
      | connexion()
      |----------------------------------------------------------------------------------|
      */
      function connexion() {
         global $strNomAdmin, $strMotPasseAdmin;
         $this->cBD = mysqli_connect("localhost", $strNomAdmin, $strMotPasseAdmin);
     
         if ($this->cBD === false) {
            throw new Exception("Impossible de se connecter à MySQL : " . mysqli_connect_error());
         }
         // Query the information_schema to check if the database exists
         $result = $this->cBD->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . $this->cBD->real_escape_string($this->nomBD) . "'");
         
         if ($result->num_rows > 0) {
            return $this->cBD;
         } else {
            $this->OK = false;
            return false;
         }
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
      | selectionneBD()
      |----------------------------------------------------------------------------------|
      */
      function selectionneBD() {
         mysqli_select_db($this->cBD, $this->nomBD) ? $this->OK = true : $this->OK = false;
     
         return $this->OK;
      }
   }
?>
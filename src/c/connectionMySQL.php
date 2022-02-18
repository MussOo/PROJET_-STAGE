<?php
    // Objet connexionBDD
    require_once("src/m/ConnexionBDD.php");
    
    $ConnexionBDD = new ConnexionBDD(DB_HOST,DB_USER,DB_PWD,DB_NAME);
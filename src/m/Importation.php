<?php
class Importation
{
    public function importManual($ConnexionBDD) {
        global $_CONST_IMPORT_ERROR, $_CONST_IMPORT_NEW_ENTRIES, $_CONST_IMPORT_DOUBLE_ENTRIES, $_CONST_IMPORT_REFUSED_ENTRIES;
        $row = 1;
        
        $ARR_ENTREPRISE = $this->getEntrepriseID($ConnexionBDD);
        
        $_EXPORT_CLASSES = "";
            $_EXPORT_SIO = 0;
            $_EXPORT_NDRC = 0;
            $_EXPORT_CG = 0;
        
        $_EXPORT_ENTREPRISE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_ENTREPRISE"]));
        $_EXPORT_MAIL = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_MAIL"]));
        $_EXPORT_FIXE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_FIXE"]));
        $_EXPORT_MOBILE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_MOBILE"]));
        $_EXPORT_ADRESSE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_ADRESSE"]));
        $_EXPORT_POSTAL = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_POSTAL"]));
        $_EXPORT_VILLE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_VILLE"]));
        $_EXPORT_PAYS = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_PAYS"]));
        $_EXPORT_ANNEE = utf8_encode(str_replace("'","",$_POST["IMPORT_DATA_ANNEE"]));
        
        
        if (strpos($_POST["IMPORT_DATA_SECTION"], "SIO1") !== false) $_EXPORT_SIO += 1;
        if (strpos($_POST["IMPORT_DATA_SECTION"], "SIO2") !== false) $_EXPORT_SIO += 2;
        if ($_EXPORT_SIO === 0 && strpos($_POST["IMPORT_DATA_SECTION"], "SIO") !== false) $_EXPORT_SIO = 3;
        
        if (strpos($_POST["IMPORT_DATA_SECTION"], "NRC1") !== false || strpos($_POST["IMPORT_DATA_SECTION"], "NDRC1") !== false) $_EXPORT_NDRC += 1;
        if (strpos($_POST["IMPORT_DATA_SECTION"], "NRC2") !== false || strpos($_POST["IMPORT_DATA_SECTION"], "NDRC2") !== false) $_EXPORT_NDRC += 2;
        if ($_EXPORT_NDRC == 0 && (strpos($_POST["IMPORT_DATA_SECTION"], "NRC") !== false || strpos($_POST["IMPORT_DATA_SECTION"], "NDRC") !== false)) $_EXPORT_NDRC = 3;
        
        if (strpos($_POST["IMPORT_DATA_SECTION"], "CG1") !== false) $_EXPORT_CG += 1;
        if (strpos($_POST["IMPORT_DATA_SECTION"], "CG2") !== false) $_EXPORT_CG += 2;
        if ($_EXPORT_CG === 0 && strpos($_POST["IMPORT_DATA_SECTION"], "CG") !== false) $_EXPORT_CG = 3;
        
        
        $_entreprise = $this->cleanString(strtolower($_EXPORT_ENTREPRISE));
        if ($_entreprise === "") {$_CONST_IMPORT_REFUSED_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE,"Aucun nom d'entreprise définis."];$row++;return;}
        if (in_array($_entreprise,$ARR_ENTREPRISE)) {$_CONST_IMPORT_DOUBLE_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE];$row++;return;}
        
        
        $query = "insert into entreprise (SIO,NDRC,CG,entreprise,mail,fixe,mobile,adresse,postal,ville,pays,annee) values ('$_EXPORT_SIO','$_EXPORT_NDRC','$_EXPORT_CG','$_EXPORT_ENTREPRISE','$_EXPORT_MAIL','$_EXPORT_FIXE','$_EXPORT_MOBILE','$_EXPORT_ADRESSE','$_EXPORT_POSTAL','$_EXPORT_VILLE','$_EXPORT_PAYS','$_EXPORT_ANNEE')";
        $ConnexionBDD->querySQL($query);
        $_CONST_IMPORT_NEW_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE];
        
        $_CONST_IMPORT_ERROR = "MANUAL_SUCCESS";
    }
    
    public function importFile($ConnexionBDD) {
        global $_CONST_IMPORT_ERROR, $_CONST_IMPORT_NEW_ENTRIES, $_CONST_IMPORT_DOUBLE_ENTRIES, $_CONST_IMPORT_REFUSED_ENTRIES;
        
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            $_CONST_IMPORT_ERROR = "FILE_TOO_LARGE";
        } else if( strtolower(pathinfo("../../import/" . basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION)) != "csv" ) {
            $_CONST_IMPORT_ERROR = "FILE_NOT_CSV";
        } else {
            $this->readCSV($_FILES["fileToUpload"]["tmp_name"],",",$ConnexionBDD);
        }
    }

    public function readCSV($filename,$delimiter,$ConnexionBDD) {
        global $_CONST_IMPORT_ERROR, $_CONST_IMPORT_ERROR_ARGS, $_CONST_IMPORT_NEW_ENTRIES, $_CONST_IMPORT_DOUBLE_ENTRIES, $_CONST_IMPORT_REFUSED_ENTRIES;
        
        // NOM DES COLONNES A RECUPERER DANS LE CSV
        $_FIELDNAME_CLASSES = explode(",",$_POST["IMPORT_DATA_SECTION"]);
        $_FIELDNAME_ENTREPRISE = explode(",",$_POST["IMPORT_DATA_ENTREPRISE"]);
        $_FIELDNAME_MAIL = explode(",",$_POST["IMPORT_DATA_MAIL"]);
        $_FIELDNAME_FIXE = explode(",",$_POST["IMPORT_DATA_FIXE"]);
        $_FIELDNAME_MOBILE = explode(",",$_POST["IMPORT_DATA_MOBILE"]);
        $_FIELDNAME_ADRESSE = explode(",",$_POST["IMPORT_DATA_ADRESSE"]);
        $_FIELDNAME_POSTAL = explode(",",$_POST["IMPORT_DATA_POSTAL"]);
        $_FIELDNAME_VILLE = explode(",",$_POST["IMPORT_DATA_VILLE"]);
        $_FIELDNAME_PAYS = explode(",",$_POST["IMPORT_DATA_PAYS"]);
        
        $csv_headers = [];
        $row = 1;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            // Créer la liste des entreprises existantes /!\ CLEANSTRING + NOSPACE /!\
            $ARR_ENTREPRISE = $this->getEntrepriseID($ConnexionBDD);
            
            // Créer le squelette du JSON
            foreach(fgetcsv($handle, 4000, $delimiter) as $fieldname) $csv_headers[] = $fieldname;
    
            while (($data = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
                // VARIABLE A METTRE DANS LE SQL
                $_EXPORT_CLASSES = "";
                    $_EXPORT_SIO = 0;
                    $_EXPORT_NDRC = 0;
                    $_EXPORT_CG = 0;
                
                $_EXPORT_ENTREPRISE = "";
                $_EXPORT_MAIL = "";
                $_EXPORT_FIXE = "";
                $_EXPORT_MOBILE = "";
                $_EXPORT_ADRESSE = "";
                $_EXPORT_POSTAL = "";
                $_EXPORT_VILLE = "";
                $_EXPORT_PAYS = "";
                $_EXPORT_ANNEE = $_POST["IMPORT_DATA_ANNEE"];
                
                if($row === 1) {$row++;continue;}
                
                // Récupérer la ligne CSV sous forme d'objet JSON
                $rowJSON = array_combine($csv_headers,$data);
                
                // FIELD "CLASSE" (sections)
                foreach($_FIELDNAME_CLASSES as $fieldname) {
                    $_EXPORT_CLASSES .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_CLASSES === "ERROR") {
                        $_EXPORT_CLASSES = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }
                
                if (strpos($_EXPORT_CLASSES, "SIO1") !== false) $_EXPORT_SIO += 1;
                if (strpos($_EXPORT_CLASSES, "SIO2") !== false) $_EXPORT_SIO += 2;
                
                if (strpos($_EXPORT_CLASSES, "NRC1") !== false || strpos($_EXPORT_CLASSES, "NDRC1") !== false) $_EXPORT_NDRC += 1;
                if (strpos($_EXPORT_CLASSES, "NRC2") !== false || strpos($_EXPORT_CLASSES, "NDRC2") !== false) $_EXPORT_NDRC += 2;
                
                if (strpos($_EXPORT_CLASSES, "CG1") !== false) $_EXPORT_CG += 1;
                if (strpos($_EXPORT_CLASSES, "CG2") !== false) $_EXPORT_CG += 2;
                
                
                // FIELD "ENTREPRISE" (nom de l'entreprise)
                foreach($_FIELDNAME_ENTREPRISE as $fieldname) {
                    $_EXPORT_ENTREPRISE .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_ENTREPRISE === "ERROR") {
                        $_EXPORT_ENTREPRISE = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }
                
                // FIELD "MAIL" (mail de contacte)
                foreach($_FIELDNAME_MAIL as $fieldname) {
                    $_EXPORT_MAIL .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_MAIL === "ERROR") {
                        $_EXPORT_MAIL = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }

                
                // FIELD "FIXE" (numéro de téléphone fixe de contacte)
                $_fixe = "";
                foreach($_FIELDNAME_FIXE as $fieldname) {
                    $_fixe .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    $__fixe = (strlen($_fixe) === 9 ? '0'.$_fixe : $_fixe);
                    $_EXPORT_FIXE .= (substr($__fixe,0,2) == "33" ? "0".substr($__fixe,2) : $__fixe );
                    
                    if ($_fixe === "ERROR") {
                        $_fixe = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }

                
                // FIELD "MOBILE" (numéro de téléhone mobile de contacte)
                $_mobile = "";
                foreach($_FIELDNAME_MOBILE as $fieldname) {
                    $_mobile .= (array_key_exists(trim($fieldname),$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "");
                    $__mobile = (strlen($_mobile) === 9 ? '0'.$_mobile : $_mobile);
                    $_EXPORT_MOBILE .= (substr($__mobile,0,2) == "33" ? "0".substr($__mobile,2) : $__mobile );
                    
                    if ($_mobile === "ERROR") {
                        $_mobile = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }
                
                // FIELD "ADRESSE" (adresse de l'entreprise)
                foreach($_FIELDNAME_ADRESSE as $fieldname) {
                    $_EXPORT_ADRESSE .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_ADRESSE === "ERROR") {
                        $_EXPORT_ADRESSE = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }

                
                // FIELD "POSTAL" (adresse postald de l'entreprise)
                foreach($_FIELDNAME_POSTAL as $fieldname) {
                    $_EXPORT_POSTAL .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_POSTAL === "ERROR") {
                        $_EXPORT_POSTAL = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }

                
                // FIELD "VILLE" (ville)
                foreach($_FIELDNAME_VILLE as $fieldname) {
                    $_EXPORT_VILLE .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_VILLE === "ERROR") {
                        $_EXPORT_VILLE = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }
                

                // FIELD "PAYS" (pays)
                foreach($_FIELDNAME_PAYS as $fieldname) {
                    $_EXPORT_PAYS .= (array_key_exists($fieldname,$rowJSON) ? utf8_encode(str_replace("'","",$rowJSON[$fieldname])) : "ERROR");
                    if ($_EXPORT_PAYS === "ERROR") {
                        $_EXPORT_PAYS = "";
                        $_CONST_IMPORT_ERROR = "FILE_UNKNOW_INPUT";
                        $_CONST_IMPORT_ERROR_ARGS[] = "Le champs '". $fieldname ."' n'existe pas.";
                    }
                }
                
                
                if ($_CONST_IMPORT_ERROR != NULL) {
                    break;
                }
                
                
                // Nom de l'entreprise brut
                $_clean_entreprise = strtolower($this->cleanString(preg_replace('/-|_| |\.|\'|\"/','',$_EXPORT_ENTREPRISE)));
                
                // Vérifie si l'entreprise n'est pas déjà dans la bdd
                if (in_array($_clean_entreprise, $ARR_ENTREPRISE)) {$_CONST_IMPORT_DOUBLE_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE];$row++;continue;}
                
                // Vérifie si le nom de l'entreprise n'est pas vide
                if ($_clean_entreprise === "") {$_CONST_IMPORT_REFUSED_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE,"Aucun nom d'entreprise définis."];$row++;continue;}
                if ($_EXPORT_SIO === 0 && $_EXPORT_NDRC === 0 && $_EXPORT_CG === 0) {$_CONST_IMPORT_REFUSED_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE,"Est un stage pour aucun BTS de Monge."];$row++;continue;}
                
                
                // Créer & Envoie la query SQL
                $query = "insert into entreprise (SIO,NDRC,CG,entreprise,mail,fixe,mobile,adresse,postal,ville,pays,annee) values ('$_EXPORT_SIO','$_EXPORT_NDRC','$_EXPORT_CG','$_EXPORT_ENTREPRISE','$_EXPORT_MAIL','$_EXPORT_FIXE','$_EXPORT_MOBILE','$_EXPORT_ADRESSE','$_EXPORT_POSTAL','$_EXPORT_VILLE','$_EXPORT_PAYS','$_EXPORT_ANNEE')";
                $ConnexionBDD->querySQL($query);
                // print $query ."<br>";
                
                // Ajouter l'entreprise à la liste (anti doublons)
                $ARR_ENTREPRISE[] = $_clean_entreprise;
                
                // Compteur de nouvelles entrés
                $_CONST_IMPORT_NEW_ENTRIES[] = [$row,$_EXPORT_ENTREPRISE];
                
                // Incrémenter
                $row++;
            }
            fclose($handle);
            if ($_CONST_IMPORT_ERROR === NULL) $_CONST_IMPORT_ERROR = "FILE_SUCCESS";
        } else {
            // Problème avec le fichier CSV
            $_CONST_IMPORT_ERROR = "CANT_READ_FILE";
        }
    }

    public function cleanString($text) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
    
    
    public function getEntrepriseID($ConnexionBDD) {
        $____array = [];
        $query = $ConnexionBDD->querySQL("select entreprise from entreprise");
        foreach ($query as $row) {
            $_v = strtolower($this->cleanString(preg_replace('/-|_| |\.|\'|\"/','',$row['entreprise'])));
            if (!in_array($_v,$____array)) {
                $____array[] = $_v;
            }
        }
        return $____array;
    }
}
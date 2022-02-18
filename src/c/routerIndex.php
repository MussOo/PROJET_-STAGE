<?php
	// Récupérer le routage
	if (empty($_CONST_ROUTER)) $_CONST_ROUTER="liste"; // Valeur par défaut
	if (!empty($_GET)) if (!empty($_GET["page"])) $_CONST_ROUTER = $_GET["page"];

	// Nom de la page
	$_CONST_PAGENAME = "Unknow";

	// ================================================================================================ //

	// Affichage de la page
	switch ($_CONST_ROUTER) {
		case "liste":
			$_CONST_PAGENAME = "Liste des stages";
			require("src/v/liste.php");
		break;
		
		case "importation":
			$_CONST_PAGENAME = "Importation données";
			header('Location: admin.php?page=importation');
		break;
		
		default:
			header("Refresh: 0;url=".$_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0]);
		break;
	}
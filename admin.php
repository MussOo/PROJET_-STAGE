<?php
	//IMPORT CONFIG FILE
	require_once("config/config.php");

	//CONNECTION MYSQL
	require_once("src/c/connectionMySQL.php");

	//CREATION DE L'OBJET IMPORTATION
	require_once("src/c/objetImportation.php");
	
	//ROUTER POUR AFFICHER LES PAGES
	require_once("src/c/routerAdmin.php");
<?php	
	// Variables pour les message d'importation
	$_CONST_IMPORT_ERROR = NULL;
	$_CONST_IMPORT_ERROR_ARGS = [];
	$_CONST_IMPORT_NEW_ENTRIES = [];
	$_CONST_IMPORT_DOUBLE_ENTRIES = [];
	$_CONST_IMPORT_REFUSED_ENTRIES = [];
	
	if (ISSET($_POST)) if (isset($_POST["IMPORT_MODE"])) {
		
		if ($_POST["IMPORT_MODE"] === "pronote") $Importation->importFile($ConnexionBDD);
		if ($_POST["IMPORT_MODE"] === "manual") $Importation->importManual($ConnexionBDD);
		
		$_POST = Array();
	}
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">

		<title>Stage en entreprise</title>
		<link rel="icon" href="public/img/icon.png" />
		<meta name="description" content="Liste des stages possible">
		<meta name="author" content="Dereims Corentin">
		
		<link rel="stylesheet" href="public/css/general.css">
	</head>
	<body>
		<?php
			require("src/v/login.php");
			require("src/v/header.php");
		?>
		
		<div class="row col-100 console-background">
			<div class="column col-40">
				<div class="margin-2pr">
					<?php
						// Traitement du code d'erreur & Affichage du message associé.
						if ($_CONST_IMPORT_ERROR !== NULL) {
							switch($_CONST_IMPORT_ERROR) {
								// CODES POUR L'IMPORTATION MANUELLE
								case "MANUAL_SUCCESS":
									echo '<p class="message console success">Entreprise importée avec succès.</p>';
								break;
								
								
								// CODES POUR L'IMPORTATION VIA UN CSV (de Pronote par défaut)
								case "FILE_SUCCESS":
									echo '<p class="message console success">Fichier importé avec succès.</p>';
								break;
								case "FILE_TOO_LARGE":
									echo '<p class="message console error">Le fichier est trop volumineux</p>';
								break;
								case "FILE_NOT_CSV":
									echo '<p class="message console error">Le fichier n\'est pas au format CSV.</p>';
								break;
								case "CANT_READ_FILE":
									echo '<p class="message console error">Le fichier n\'a pas pus être lu.</p>';
								break;
								
								case "FILE_UNKNOW_INPUT":
									echo '<p class="message console error">Un ou plusieurs champs renseigné n\'existe pas dans le fichier.</p>';
									echo "<div class='col-80 margin-2pr console-log-bis log-scroll'>";
									foreach($_CONST_IMPORT_ERROR_ARGS as $e) {echo '<p class="message console">'. $e .'</p>';}
									echo "</div>";
								break;
								
								
								// case "ERROR_NAME":
									// echo '<p class="message console error">Fichier</p>';
								// break;
								
								
								
								// ERREUR NON PRISE EN CHARGE
								default:
									echo '<p class="message console error">Une erreur inconnue s\'est produite.</p>';
								break;
							}
							echo "<br>";
						}
					?>
					
					<!-- Entrées -->
					<p class="message console success">Nombre de nouvelles entrées : <?php print count($_CONST_IMPORT_NEW_ENTRIES); ?></p>
					<p class="message console warn">Nombre de doublons évités : <?php print count($_CONST_IMPORT_DOUBLE_ENTRIES); ?></p>
					<p class="message console error">Nombre d'entrées refusés : <?php print count($_CONST_IMPORT_REFUSED_ENTRIES); ?></p>
				</div>
			</div>
			<div class="column">
				<div class="col-75 margin-2pr console-log log-scroll">
					<?php
						if (count($_CONST_IMPORT_NEW_ENTRIES)) {
							echo "<p class='message console success margin-1pr'>=== NOUVELLES ENTREES ===</p>";
							echo "<div class='col-80 margin-2pr console-log-bis log-scroll'>";
							foreach($_CONST_IMPORT_NEW_ENTRIES as $e) {echo '<p class="message console">ligne '. $e[0] .' : "'. $e[1] .'"</p>';}
							echo "</div><br>";
						}
						
						if (count($_CONST_IMPORT_DOUBLE_ENTRIES)) {
							echo "<p class='message console warn margin-1pr'>=== DOUBLONS EVITES ===</p>";
							echo "<div class='col-80 margin-2pr console-log-bis log-scroll'>";
							foreach($_CONST_IMPORT_DOUBLE_ENTRIES as $e) {echo '<p class="message console">ligne '. $e[0] .' : "'. $e[1] .'"</p>';}
							echo "</div><br>";
						}
						
						if (count($_CONST_IMPORT_REFUSED_ENTRIES)) {
							echo "<p class='message console error margin-1pr'>=== ENTREES REFUSES ===</p>";
							echo "<div class='col-80 margin-2pr console-log-bis log-scroll'>";
							foreach($_CONST_IMPORT_REFUSED_ENTRIES as $e) {echo '<p class="message console">ligne '. $e[0] .' : "'. $e[1] .'"<br><span>Raison : '. $e[2] .'</span></p>';}
							echo "</div>";
						}
					?>
				</div>
			</div>
		</div>
		
		<div class="colonne-importation col-100">
			<br><br><br>
			<!-- FORMULAIRES IMPORTATION PRONOTE -->
			
			<div class="importation-pronote margin-2pr col-75 padding-1pr border">
				<h2>Importation depuis Pronote</h2>
				<form enctype="multipart/form-data" method="post">
					<h3>Entrez le nom des colonnes du fichier CSV</h3>
					<h4>Pour entrez plusieurs colonnes dans le même champ, séparer les noms par une virgule.</h4>
					<h4>* Tout les espaces seront remplacer par un undescore ( ' ' → '_' )</h4>
					
					<input type="hidden" name="IMPORT_MODE" value="pronote" />
					<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
					
					<table id="table-importation">
						<tr>
							<td class="col-40">Section</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_SECTION"  type="text" placeholder="Section(s)" value="SESSION_STAGE" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Entreprise</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ENTREPRISE"  type="text" placeholder="Entreprise" value="RAISONSOC" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Adresse Email</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_MAIL"  type="text" placeholder="Adresse Mail" value="MEMAIL" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Numéro de téléphone</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_FIXE"  type="text" placeholder="Téléphone fixe" value="MFIXENum" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Numéro de mobile</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_MOBILE"  type="text" placeholder="Téléphone mobile" value="MPORTABLEComplet" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Adresse</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ADRESSE"  type="text" placeholder="Adresse" value="LADRES_1,LADRES_2,LADRES_3,LADRES_4" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Code postal</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_POSTAL"  type="text" placeholder="Code postal" value="LCP" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Ville</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_VILLE"  type="text" placeholder="Ville" value="LVILLE" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Pays</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_PAYS"  type="text" placeholder="Pays" value="LPAYS" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Année</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ANNEE"  type="text" placeholder="Année" value="<?php print date("Y"); ?>" /></td>
						</tr>
						<tr>
							<td class>Fichier CSV</td>
							<td class="col-auto padding-0"><input class="input-importation-file" name="fileToUpload" type="file" /></td>
						</tr>
						
						<tr>
							<td class="padding-1pr" colspan="2"><input type="submit" value="Envoyé !"></td>
						</tr>
						
					</table>
				</form>
			</div>
			
			<br><br><br>
			<!-- FORMULAIRES IMPORTATION MANUELLE -->
			
			<div class="import-manuelle margin-2pr col-75 padding-1pr position-relative border">
				<h2>Importation manuelle</h2>
				<form enctype="multipart/form-data" method="post">
					<h3>Entrez les informations de l'entreprise</h3>
					<input type="hidden" name="IMPORT_MODE" value="manual" />
					
					<table id="table-importation">
						<tr>
							<td class="col-40">Section</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_SECTION"  type="text" placeholder="Section(s)" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Entreprise</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ENTREPRISE"  type="text" placeholder="Entreprise" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Adresse Email</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_MAIL"  type="text" placeholder="Adresse Mail" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Numéro de téléphone</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_FIXE"  type="text" placeholder="Téléphone fixe" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Numéro de mobile</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_MOBILE"  type="text" placeholder="Téléphone mobile" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Adresse</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ADRESSE"  type="text" placeholder="Adresse" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Code postal</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_POSTAL"  type="text" placeholder="Code postal" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Ville</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_VILLE"  type="text" placeholder="Ville" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Pays</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_PAYS"  type="text" placeholder="Pays" value="" /><br/></td>
						</tr>
						<tr>
							<td class="col-40">Année</td>
							<td class="col-auto padding-0"><input class="input-importation" name="IMPORT_DATA_ANNEE"  type="text" placeholder="Année" value="<?php print date("Y"); ?>" /></td>
						</tr>
						<tr>
							<td class="padding-1pr" colspan="2"><input type="submit" value="Envoyé !"></td>
						</tr>
						
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
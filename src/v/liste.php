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
		
		<div>
			<h1>Filtres de recherches</h1>
			<form>
				<label>Section : </label>
				<select name="section">
					<option value="default">-- Sélectionnez la section --</option>
					<option value="SIO">SIO</option>
					<option value="SIO1">SIO 1</option>
					<option value="SIO2">SIO 2</option>
					<option value="NDRC">NDRC</option>
					<option value="NDRC1">NDRC 1</option>
					<option value="NDRC2">NDRC 2</option>
					<option value="CG">CG</option>
					<option value="CG1">CG 1</option>
					<option value="CG2">CG 2</option>
				</select>
				
				<label>Villes : </label>
				<select name="ville">
					<option value="default">-- Sélectionnez la ville --</option>
					<?php
						$rows = $ConnexionBDD->querySQL("select distinct ville from entreprise");
						foreach($rows as $row) {
							$v = $row["ville"];
							if ($v !== "") echo '<option value="'. $v .'">'. $v .'</option>';
						}
					?>
				</select>
				
				<label>Pays : </label>
				<select name="pays">
					<option value="default">-- Sélectionnez le Pays --</option>
					<?php
						$rows = $ConnexionBDD->querySQL("select distinct pays from entreprise");
						foreach($rows as $row) {
							$v = $row["pays"];
							if ($v !== "") echo '<option value="'. $v .'">'. $v .'</option>';
						}
					?>
				</select>
				
				<label>Année : </label>
				<select name="annee">
					<option value="default">-- Sélectionnez l'année --</option>
					<?php
						$rows = $ConnexionBDD->querySQL("select distinct annee from entreprise");
						foreach($rows as $row) {
							$v = $row["annee"];
							if ($v !== "") echo '<option value="'. $v .'">'. $v .'</option>';
						}
					?>
				</select>
				
				<br><br>
				<button type="submit">Valider</button>
			</form>
		</div>
		
		<br>
		
		<div>
			<table>
				<thead>
					<tr>
					<th class="col-10">Section</th>
					<th class="col-20">Nom de l'entreprise</th>
					<th class="col-20">Adresse Mail</th>
					<th class="col-10">Numéro</th>
					<th class="col-30">Adresse</th>
					<th class="col-10">Date</th>
					</tr>
				</thead>
				
				<?php
					$_FILTRE_SECTION = "default";
					$_FILTRE_VILLE = "default";
					$_FILTRE_PAYS = "default";
					$_FILTRE_ANNEE = "default";
					
					if (ISSET($_GET)) {
						if (ISSET($_GET["section"])) if ($_GET["section"] != "default") $_FILTRE_SECTION = $_GET["section"];
						if (ISSET($_GET["ville"])) if ($_GET["ville"] != "default") $_FILTRE_VILLE = "ville='". $_GET["ville"] ."'";
						if (ISSET($_GET["pays"])) if ($_GET["pays"] != "default") $_FILTRE_PAYS = "pays='". $_GET["pays"] ."'";
						if (ISSET($_GET["annee"])) if ($_GET["annee"] != "default") $_FILTRE_ANNEE = "annee='". $_GET["annee"] ."'";
					}
					
					$_FILTRE = "";
					
					
					if ($_FILTRE_SECTION != "default" || $_FILTRE_VILLE != "default" || $_FILTRE_PAYS != "default" || $_FILTRE_ANNEE != "default") $_FILTRE .= " where ";
					
					if ($_FILTRE_SECTION != "default") {
						// SIO
						if ($_FILTRE_SECTION == "SIO1") $_FILTRE .= "(SIO='1' OR SIO='3')";
						if ($_FILTRE_SECTION == "SIO2") $_FILTRE .= "(SIO='2' OR SIO='3')";
						if ($_FILTRE_SECTION == "SIO") $_FILTRE .= "(SIO='1' OR SIO='2' OR SIO='3')";
						
						// NDRC
						if ($_FILTRE_SECTION == "NDRC1") $_FILTRE .= "NDRC='1'";
						if ($_FILTRE_SECTION == "NDRC2") $_FILTRE .= "NDRC='2'";
						if ($_FILTRE_SECTION == "NDRC") $_FILTRE .= "(NDRC='1' OR NDRC='2' OR NDRC='3')";
						
						// CG
						if ($_FILTRE_SECTION == "CG1") $_FILTRE .= "CG='1'";
						if ($_FILTRE_SECTION == "CG2") $_FILTRE .= "CG='2'";
						if ($_FILTRE_SECTION == "CG") $_FILTRE .= "(CG='1' OR CG='2' OR CG='3')";
						
						
						// AND
						$_FILTRE .=  ($_FILTRE_VILLE != "default" || $_FILTRE_PAYS != "default" || $_FILTRE_ANNEE != "default" ? " AND " : "");
					}
					if ($_FILTRE_VILLE != "default") $_FILTRE .= $_FILTRE_VILLE . ($_FILTRE_PAYS != "default" || $_FILTRE_ANNEE != "default" ? " AND " : "");
					if ($_FILTRE_PAYS != "default") $_FILTRE .= $_FILTRE_PAYS . ($_FILTRE_ANNEE != "default" ? " AND " : "");
					if ($_FILTRE_ANNEE != "default") $_FILTRE .= $_FILTRE_ANNEE;
					
					$query = "select * from entreprise". ($_FILTRE != "" ? $_FILTRE : "");
					$rows = $ConnexionBDD->querySQL($query);
					foreach($rows as $row) {
						$_SECTION = [];
						
						if ($_FILTRE_SECTION == "default") {
							if ($row['SIO'] == 1 || $row['SIO'] == 3) $_SECTION[] = 'SIO1';
							if ($row['SIO'] == 2 || $row['SIO'] == 3) $_SECTION[] = 'SIO2';
							
							if ($row['NDRC'] == 1 || $row['NDRC'] == 3) $_SECTION[] = 'NDRC1';
							if ($row['NDRC'] == 2 || $row['NDRC'] == 3) $_SECTION[] = 'NDRC2';
							
							if ($row['CG'] == 1 || $row['CG'] == 3) $_SECTION[] = 'CG1';
							if ($row['CG'] == 2 || $row['CG'] == 3) $_SECTION[] = 'CG2';
						} else {
							if ($_FILTRE_SECTION == 'SIO1' || $_FILTRE_SECTION == 'SIO') if ($row['SIO'] == 1 || $row['SIO'] == 3) $_SECTION[] = 'SIO1';
							if ($_FILTRE_SECTION == 'SIO2' || $_FILTRE_SECTION == 'SIO') if ($row['SIO'] == 2 || $row['SIO'] == 3) $_SECTION[] = 'SIO2';
							
							if ($_FILTRE_SECTION == 'NDRC1' || $_FILTRE_SECTION == 'NDRC') if ($row['NDRC'] == 1 || $row['NDRC'] == 3) $_SECTION[] = 'NDRC1';
							if ($_FILTRE_SECTION == 'NDRC2' || $_FILTRE_SECTION == 'NDRC') if ($row['NDRC'] == 2 || $row['NDRC'] == 3) $_SECTION[] = 'NDRC2';
							
							if ($_FILTRE_SECTION == 'CG1' || $_FILTRE_SECTION == 'CG') if ($row['CG'] == 1 || $row['CG'] == 3) $_SECTION[] = 'CG1';
							if ($_FILTRE_SECTION == 'CG2' || $_FILTRE_SECTION == 'CG') if ($row['CG'] == 2 || $row['CG'] == 3) $_SECTION[] = 'CG2';
						}
						
					
						
						echo "<tr>".
								"<td>". join(", ",$_SECTION) ."</td>".
								"<td>". $row["entreprise"] ."</td>".
								"<td>". $row["mail"] ."</td>".
								"<td>". trim($row["fixe"] ." ". $row["mobile"]) ."</td>".
								"<td>". trim($row["adresse"] ."<br>". $row["ville"] .", ". $row["postal"] .", ". $row["pays"]) ."</td>".
								"<td>". $row["annee"] ."</td>".
							"</tr>";
					}
				?>
				
			</table>
		</div>
		
	</body>
</html>
<link rel="stylesheet" href="public/css/header.css">

<!-- HEADER -->
<div class="_header">
	<img src="public/img/Monge.png"/>
	<h1><?php global $_CONST_PAGENAME;echo ucfirst($_CONST_PAGENAME); ?></h1>
</div>

<!-- MENU DE DROITE -->
<div class="_header_menu close">
	<div class="_header_menu_link" action="page" page="liste">Liste Stage</div> 
</div>
<div class="_header_button" >
	<a href="index.php?page=liste"><button type="submit" >Liste</button><a>
	<a href="index.php?page=importation"><button type="submit" >importation <img class="button_image_importation"src="public/img/cadenas.png" /></button><a>
</div>




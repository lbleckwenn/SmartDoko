<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="stylesheet/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet/extra.css">
<link rel="apple-touch-icon" sizes="57x57" href="./images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="./images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="./images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="./images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="./images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="./images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="./images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="./images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="./images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="./images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="./images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="./images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="./images/favicon-16x16.png">
<link rel="manifest" href="./images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="./images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<script src="./javascript/jquery-3.2.1.min.js"></script>
<script src="./javascript/popper.min.js"></script>
<script src="./javascript/bootstrap.min.js"></script>
<link rel="stylesheet" href="stylesheet/fontawesome/css/fontawesome-all.css">
<title>SmartDoko</title>
</head>
<body>
	{include file='navbar.tpl'}{assign var="fullPathToTemplate" value="./templates/$page.tpl"} {if file_exists($fullPathToTemplate)} {include file="$page.tpl"} {else}
	<div class="container">
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">Fehler beim Seitenaufruf!</h4>
			<p>Die gewählte Seite "{$page}" ist nicht vorhanden.</p>
			<hr>
			<p class="mb-0">{$fullPathToTemplate}</p>
		</div>
	</div>
	{/if}
	<footer class="footer">
		<div class="container">
			<span class="text-muted">&copy; 2018 Lars Bleckwenn<span class="float-right"><a href="https://github.com/lbleckwenn/SmartDoko" target="_blank">SmartDoko</a></span>
			</span>
		</div>
	</footer>
</body>
</html>
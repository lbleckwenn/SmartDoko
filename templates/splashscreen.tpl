<!-- Main jumbotron for a primary marketing message or call to action -->
{if $success}
<div class="alert alert-success" role="alert">
	<h4 class="alert-heading">Erfolg</h4>
	<p>
		{$success}
		</a>
	</p>
</div>
{elseif $error}
<div class="alert alert-danger" role="alert">
	<h4 class="alert-heading">Fehler</h4>
	<p>{$error}</p>
</div>
{/if}
<div class="jumbotron">
	<div class="container">
		<h1 class="display-4">SmartDoko</h1>
		<p class="lead">Herzlich Willkommen zu SmartDoko. SmartDoko ist eine Webseite zur Auswertung von Doppelkopfrunden mit dem Smartphone, Tablet oder am PC.</p>
		<p class="lead">
			SmartDoko ist unter der GPLv3 lizenziert, kann nach belieben auf eigenen Webseiten zur Verfügung gestellt und verändern werden. Nur der kommerzielle Verkauf des Programms ist nicht gestattet.
			SmartDoko kann auf
			<a href="https://github.com/lbleckwenn/SmartDoko" target="_blank">GitHub.com</a>
			heruntergeladen werden.
		</p>
		<p class="lead">Um SmartDoko nutzen zu können musst du dich zunächst anmelden.</p>
		<p>
			<a class="btn btn-success btn-lg mb-1 col-12 col-sm-6 col-md-4 col-lg-2" href="index.php?page=login" role="button">Jetzt anmelden</a>
			<a class="btn btn-primary btn-lg mb-1 col-12 col-sm-6 col-md-4 col-lg-2" href="index.php?page=register" role="button">Jetzt registrieren</a>
		</p>
	</div>
</div>
<div class="container">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-md-4">
			<h2>Features</h2>
			<ul>
				<li>Abrechnung von Doppelkopfrunden</li>
				<li>Detalierte Anzeige der Spielwertung</li>
				<li>Benutzereigene Spielrunden</li>
				<li>Mitspielerverwaltung</li>
				<li>Teilen von Ergebnissen mit Feunden</li>
				<li>Statistiken</li>
				<li>Responsives Webdesign, für PC, Tablet und Smartphone</li>
			</ul>
		</div>
		<div class="col-md-4">
			<h2>Regelwerk</h2>
			<p>SmartDoko basiert auf den Turnierspielregeln des Deutschen Doppelkopf Verband. Beim Normalspiel erhalten die Spieler der Siegerpartei Spielpunkte mit positivem, die Spieler der Verliererpartei
				mit negativem Vorzeichen (PLUS-MINUS-Wertung). Daneben können die Punkte der Gewinnerparteien auch summiert angezeigt werden. Eine Vervielfachung durch Ansagen oder Bockrunden erfolgt nicht.</p>
			<p>
				<a class="float-right" href="http://www.doko-verband.de/" target="_blank">zum Doko-Verband</a>
			</p>
		</div>
		<div class="col-md-4">
			<h2>Bla bla blub</h2>
			<p>Hier könnte noch weiterer Text stehen, wenn mir etwas gescheites einfallen würde. Alternativ könnte hier eine Ministatistik mit den erfolgreichsten Spielern, erspielten Doppelköpfen oder Anzahl
				der Hochzeiten angezeigt werden. Aber es gibt wichtigeres - zum Beispiel die Fertigstellung aller geplanten Funktionen und Abhärtung gegen Fehlerkonstellationen.</p>
		</div>
	</div>
</div>

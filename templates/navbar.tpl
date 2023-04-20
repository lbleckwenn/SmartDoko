<header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
		<div class="container">
			<a class="navbar-brand" href="index.php">
				<img src="./images/logo_small.png" width="30" height="30" class="d-inline-block align-top" alt=""> SmartDoko
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			{if $login}
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item dropdown {if $page==" runde" || $page=="round"}active{/if}"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-dice"></i> Doppelkopf
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?page=runde">
								<i class="fas fa-dice"></i> Smartphone
							</a>
							<a class="dropdown-item" href="index.php?page=round">
								<i class="fas fa-dice"></i> PC / Tablet
							</a>
						</div></li>
					<!-- <li class="nav-item {if $page=="round"}active{/if}"><a class="nav-link" href="index.php?page=runde"><i class="fas fa-dice"></i> Doppelkopf</a></li> -->
					<li class="nav-item {if $page=="history"}active{/if}"><a class="nav-link" href="index.php?page=history">
							<i class="fas fa-list-ol"></i> Historie
						</a></li>
					<li class="nav-item dropdown {if $page=="statistics"}active{/if}"><a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-chart-bar"></i> Statistiken
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?page=stat_rangliste">
								<i class="fas fa-list-ol"></i> Gesamtrangliste
							</a>
							<a class="dropdown-item" href="index.php?page=stat_besteRunde">
								<i class="fas fa-list-ol"></i> Beste Runden
							</a>
							<a class="dropdown-item" href="index.php?page=stat_ansagen">
								<i class="fas fa-list-ol"></i> Ansagen
							</a>
							<a class="dropdown-item" href="index.php?page=stat_absagen">
								<i class="fas fa-list-ol"></i> Absagen
							</a>
							<a class="dropdown-item" href="index.php?page=stat_sonderpunkte">
								<i class="fas fa-list-ol"></i> Sonderpunkte
							</a>
							<a class="dropdown-item" href="index.php?page=stat_teilnehmer">
								<i class="fas fa-list-ol"></i> Teilnehmer
							</a>
						</div></li>
					<li class="nav-item {if $page=="history"}active{/if}"><a class="nav-link" href="index.php?page=regeln">
							<i class="fas fa-gavel"></i> Regeln
						</a></li>
					<!-- <li class="nav-item {if $page=="user"}active{/if}"><a class="nav-link" href="index.php?page=user"><i class="fas fa-users"></i> Benutzer</a></li> -->
				</ul>
				<ul class="navbar-nav my-2 my-lg-0">
					<li class="nav-item dropdown {if $page=="settings"}active{/if}"><a class="nav-link dropdown-toggle" href="index.php?page=config" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-wrench"></i> Einstellungen
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?page=settings">
								<i class="fas fa-sliders-h"></i> Einstellungen
							</a>
							<a class="dropdown-item" href="index.php?page=account">
								<i class="fas fa-user-cog"></i> Benutzerkonto
							</a>
							<a class="dropdown-item" href="index.php?page=player">
								<i class="fas fa-users"></i> Mitspieler
							</a>
						</div></li>
					<li class="nav-item {if $page=="player"}active{/if}"><a class="nav-link" href="index.php?page=logout">
							<i class="fas fa-sign-out-alt"></i> Abmelden
						</a></li>
				</ul>
				{else}
				<!-- 
				<form class="form-inline" action="index.php?page=login" method="post">{$token}
					<input class="form-control mr-sm-2" placeholder="E-Mail" name="email" type="email" required> <input class="form-control mr-sm-2"
						placeholder="Passwort" name="passwort" type="password" value="" required>
					</td>
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Anmelden</button>
				</form> -->
			</div>
			{/if}
		</div>
	</nav>
</header>

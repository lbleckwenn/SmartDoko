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
					<li class="nav-item {if $page=="round"}active{/if}"><a class="nav-link" href="index.php?page=round">Doppelkopfrunde</a></li>
					<li class="nav-item {if $page=="player"}active{/if}"><a class="nav-link" href="index.php?page=player">Mitspieler</a></li>
					<li class="nav-item {if $page=="statistics"}active{/if}"><a class="nav-link" href="index.php?page=statistics">Statistiken</a></li>
				</ul>
				<ul class="navbar-nav my-2 my-lg-0">
					<li class="nav-item dropdown {if $page=="settings"}active{/if}"><a class="nav-link dropdown-toggle" href="index.php?page=config" id="navbarDropdown" role="button" data-toggle="dropdown"
							aria-haspopup="true" aria-expanded="false">Profil</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?page=settings">Einstellungen</a>
							<a class="dropdown-item" href="index.php?page=logout">Abmelden</a>
						</div></li>
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

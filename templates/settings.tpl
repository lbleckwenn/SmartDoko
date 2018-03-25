<div class="container">
	<h2>Einstellungen</h2>
	{if $success}
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		{$success}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	{/if} {if $error}
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		{$error}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	{/if}
	<div>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item"><a class="nav-link active" href="#data" aria-controls="home" role="tab" data-toggle="tab">Persönliche Daten</a></li>
			<li class="nav-item"><a class="nav-link" href="#email" aria-controls="profile" role="tab" data-toggle="tab">E-Mail</a></li>
			<li class="nav-item"><a class="nav-link" href="#passwort" aria-controls="messages" role="tab" data-toggle="tab">Passwort</a></li>
		</ul>

		<!-- Persönliche Daten-->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="data">
				<br>
				<form action="index.php?page=settings&save=personal_data" method="post">
					<div class="form-group row">
						<label for="inputVorname" class="col-sm-3 text-right col-form-label">Vorname:</label>
						<div class="col-sm-6">
							<input type="text" id="inputVorname" maxlength="250" name="vorname" value="{$user.vorname}" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputNachname" class="col-sm-3 text-right col-form-label">Nachname:</label>
						<div class="col-sm-6">
							<input type="text" id="inputNachname" maxlength="250" name="nachname" class="form-control" value="{$user.nachname}" required>
						</div>
					</div>
					<button type="submit" class="btn btn-primary col-sm-2">Speichern</button>
				</form>
			</div>

			<!-- Änderung der E-Mail-Adresse -->
			<div role="tabpanel" class="tab-pane" id="email">
				<br>
				<p>Zum Änderen deiner E-Mail-Adresse gib bitte dein aktuelles Passwort sowie die neue E-Mail-Adresse ein.</p>
				<form action="index.php?page=settings&save=email" method="post">
					<div class="form-group row">
						<label for="inputPasswort" class="col-sm-3 text-right col-form-label">Dein Passwort:</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="passwort" id="inputPasswort">
						</div>
					</div>
					<div class="form-group row">
						<label for="inputEmail" class="col-sm-3 text-right col-form-label">E-Mail:</label>
						<div class="col-sm-6">
							<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputEmail2" class="col-sm-3 text-right col-form-label">E-Mail (wiederholen):</label>
						<div class="col-sm-6">
							<input type="email" id="inputEmail2" maxlength="250" name="email2" class="form-control" required>
						</div>
					</div>
					<button type="submit" class="btn btn-primary col-sm-2">Speichern</button>
				</form>
			</div>

			<!-- Änderung des Passworts -->
			<div role="tabpanel" class="tab-pane" id="passwort">
				<br>
				<p>Zum Änderen deines Passworts gib bitte dein aktuelles Passwort sowie das neue Passwort ein.</p>
				<form action="index.php?page=settings&save=passwort" method="post" class="form-horizontal">
					<div class="form-group row">
						<label for="inputPasswort" class="col-sm-3 text-right col-form-label">Altes Passwort:</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="passwortAlt" id="inputPasswort">
						</div>
					</div>
					<div class="form-group row">
						<label for="inputPasswortNeu" class="col-sm-3 text-right col-form-label">Neues Passwort:</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="passwortNeu" id="inputPasswortNeu">
						</div>
					</div>
					<div class="form-group row">
						<label for="inputPasswortNeu2" class="col-sm-3 text-right col-form-label">Neues Passwort (wiederholen):</label>
						<div class="col-sm-6">
							<input type="password" class="form-control" name="passwortNeu2" id="inputPasswortNeu2">
						</div>
					</div>
					<button type="submit" class="btn btn-primary col-sm-2">Speichern</button>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<h2>Neues Benutzerkonto erstellen</h2>
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Benutzerkonto erstellt</h4>
		<p>
			Du wurdest erfolgreich registriert.
			<a href="index.php?page=login">Zur Anmeldung</a>
		</p>
	</div>
	{else} {if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$error}</p>
	</div>
	{/if}
	<form action="?page=register&register=1" class="mt-5" method="post">
		<div class="form-group row">
			<label for="inputVorname" class="col-sm-2 offset-sm-2 col-form-label">Vorname:</label>
			<div class="col-sm-6">
				<input type="text" id="inputVorname" maxlength="250" name="vorname" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputNachname" class="col-sm-2 offset-sm-2 col-form-label">Nachname:</label>
			<div class="col-sm-6">
				<input type="text" id="inputNachname" maxlength="250" name="nachname" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 offset-sm-2 col-form-label">E-Mail:</label>
			<div class="col-sm-6">
				<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort" class="col-sm-2 offset-sm-2 col-form-label">Dein Passwort:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort" id="inputPasswort">
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort2" class="col-sm-2 offset-sm-2 col-form-label">Passwort wiederholen:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort2" id="inputPasswort2">
			</div>
		</div>
		<button type="submit" class="btn btn-primary col-sm-2">Registrieren</button>
	</form>
	{/if}
</div>

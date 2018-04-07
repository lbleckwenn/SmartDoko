<div class="container">
	<h2>Neues Passwort vergeben</h2>
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Benutzerkonto erstellt</h4>
		<p>
			Dein Passwort wurde erfolgreich geändert. <a href="index.php?page=login">Zur Anmeldung</a>
		</p>
	</div>
	{elseif $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$error}</p>
	</div>
	{else} {if $msg}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$msg}</p>
	</div>
	{/if}
	<form action="index.php?page=passwortzuruecksetzen&amp;send=1&amp;userid={$userid}&amp;code={$code}" method="post">{$token}
		<label for="passwort">Bitte gib ein neues Passwort ein:</label><br> <input type="password" id="passwort" name="passwort" class="form-control"
			required><br> <label for="passwort2">Passwort erneut eingeben:</label><br> <input type="password" id="passwort2" name="passwort2"
			class="form-control" required><br> <input type="submit" value="Passwort speichern" class="btn btn-lg btn-primary btn-block">
	</form>
	{/if}
</div>
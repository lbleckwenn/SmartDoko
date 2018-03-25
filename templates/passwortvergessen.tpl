<div class="container">
	<h2>Passwort vergessen</h2>
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Passwort zurückgesetzt</h4>
		<p>
			Ein Link um dein Passwort zurückzusetzen wurde an deine E-Mail-Adresse gesendet.
		</p>
	</div>
	{else}	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$error}</p>
	</div>
	{/if}
	<form action="index.php?page=passwortvergessen&send=1" class="mt-5" method="post">
		Gib hier deine E-Mail-Adresse ein, um ein neues Passwort anzufordern.<br>
		<br><div
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 offset-sm-2 col-form-label">E-Mail:</label>
			<div class="col-sm-6">
				<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" value="{$email_value}" required>
			</div>
		</div>
		<button class="btn btn-primary col-sm-2 offset-sm-8" type="submit">Neues Passwort</button>
	</form>
	{/if}
</div>

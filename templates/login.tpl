<div class="container">
	<h2>Anmeldung</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$error}</p>
	</div>
	{/if}
	<form action="index.php?page=login" class="mt-5" method="post">{$token}
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 offset-sm-2 col-form-label">E-Mail:</label>
			<div class="col-sm-6">
				<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" value="{$email_value}" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort" class="col-sm-2 offset-sm-2 col-form-label">Dein Passwort:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort" id="inputPasswort">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-2 offset-sm-2"></div>
			<div class="col-sm-6">
				<div class="form-check">
					<input type="checkbox" class="form-check-input" value="remember-me" name="angemeldet_bleiben" value="1" checked>
					<label class="form-check-label" for="gridCheck1"> Angemeldet bleiben </label>
				</div>
			</div>
		</div>
		<a href="index.php?page=passwortvergessen" class="btn btn-secondary offset-sm-6" role="button">Passwort vergessen</a>
		<button class="btn btn-primary" type="submit">Anmelden</button>
	</form>
</div>
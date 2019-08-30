<div class="container">
	<h2>Übersicht</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Später soll hier eine Übersichtsseite über die letzten Doppelkopfrunden mit eigener Beteiligung und/oder der Freunde entspehen</p>
			<p>Bis es soweit ist werden hier lediglich die registrierten Benutzer für Testzwecke während der Programmierung aufgelistet</p>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>#</th>
				<th>Vorname</th>
				<th>Nachname</th>
				<th>E-Mail</th>
			</tr>
			{foreach $users as $user}
			<tr>
				<td>{$user.id}</td>
				<td>{$user.vorname}</td>
				<td>{$user.nachname}</td>
				<td><a href="mailto:{$user.email}">{$user.email}</a></td>
			</tr>
			{/foreach}
		</table>
	</div>
	{/if}
</div>
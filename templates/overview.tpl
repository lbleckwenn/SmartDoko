<div class="container">
	<h2>Übersicht</h2>
	{if isset($error)}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p>{$error}</p>
	</div>
	{else}
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
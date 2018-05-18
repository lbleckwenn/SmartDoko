<div class="container">
	<h2>Spielzettel und Statistiken</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Hier soll eine Übersichtsseite über die letzten Doppelkopfrunden mit eigener Beteiligung und/oder der Freunde entspehen.</p>
			<p>Darüber hinaus soll es Statiskiken über die meisten gewonnenen Spiele, erfolgreichste Paarungen, gewonnene oder verlorene Karlchen oder
				gefangene Füchse geben.</p>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-sm">
			<thead>
				<tr>
					<th>Ort</th>
					<th>Datum</th>
					<th>Punkte</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				{foreach $runden as $runde}
				<tr>
					<td>{$runde.location}</td>
					<td>{$runde.date}</td>
					<td>{foreach $runde.player as $player_id => $punkte}{$players.$player_id}<span class="float-right">{$punkte}&nbsp;</span><br>{/foreach}
					</td>
					<td>{if $runde.is_running}läuft{else}beendet{/if}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	{/if}
</div>
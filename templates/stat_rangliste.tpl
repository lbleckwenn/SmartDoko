<div class="container">
	<h2>Rangliste</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Rangliste über alle gespielten Runden.</p>
			<p>Die Sortierung erfolgt nach den durchschnittlichen Punkten (Summe) pro Spiel.</p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th rowspan="2">Platz</th>
							<th rowspan="2">Spielername</th>
							<th rowspan="2">Runden</th>
							<th rowspan="2">Spiele</th>
							<th colspan="2">Spielpunkte</th>
							<th rowspan="2">Siege</th>
							<th colspan="3">Durchschnitt</th>
						</tr>
						<tr class="text-center">
							<th>Summe</th>
							<th>+/-</th>
							<th>Summe</th>
							<th>+/-</th>
							<th>Siege/Spiel</th>
						</tr>
					</thead>
					<tbody>
						{foreach $rangliste as $spieler}
						<tr>
							<td class="col-1 text-right pr-4">{$spieler@iteration}</td>
							<td class="col-3">{$spieler.vorname}</td>
							<td class="col-1 text-right pr-4">{$spieler.runden}</td>
							<td class="col-1 text-right pr-4">{$spieler.spiele}</td>
							<td class="col-1 text-right pr-4">{$spieler.punkteSumme}</td>
							<td class="col-1 text-right pr-4">{$spieler.punktePlusMinus}</td>
							<td class="col-1 text-right pr-4">{$spieler.siege}</td>
							<td class="col-1 text-right pr-4">{$spieler.schnittSpielePunkteSumme|number_format:3:",":"."}</td>
							<td class="col-1 text-right pr-4">{$spieler.schnittSpielePunktePlusMinus|number_format:3:",":"."}</td>
							<td class="col-1 text-right pr-4">{$spieler.schnittSiegeSpiele|number_format:1:",":"."} %</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/if}
</div>
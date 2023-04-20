<div class="container">
	<h2>Beste Runden</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Liste der besten Runden pro Spieler.</p>
			<p>Die Sortierung erfolgt nach den durchschnittlichen Punkten (Summe) pro Spiel.</p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th rowspan="2">Platz</th>
							<th rowspan="2">Spielername</th>
							<th rowspan="2">Datum</th>
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
							<th class="col-1 text-right pr-4">{$spieler@iteration}</th>
							<th class="col-3 pl-2">{$spieler.vorname}</th>
							<td class="col-1 text-center">{$spieler.datum|date_format:"d.m.Y"}</td>
							<td class="col-1 text-right pr-2">{$spieler.spiele}</td>
							<td class="col-1 text-right pr-2">{$spieler.punkteSumme}</td>
							<td class="col-1 text-right pr-2">{$spieler.punktePlusMinus}</td>
							<td class="col-1 text-right pr-2">{$spieler.siege}</td>
							<th class="col-1 text-right pr-2">{$spieler.schnittSpielePunkteSumme|number_format:3:",":"."}</th>
							<td class="col-1 text-right pr-2">{$spieler.schnittSpielePunktePlusMinus|number_format:3:",":"."}</td>
							<td class="col-1 text-right pr-2">{$spieler.schnittSiegeSpiele|number_format:1:",":"."} %</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/if}
</div>
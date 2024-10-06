<div class="container">
	<h2>Doppelkopfrunden</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Teilnahmen und Ausrichter</p>
			<p></p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th>Spielername</th>
							<th>Anzahl <br>Teilnahmen
							</th>
							<th>letzte <br>Teilnahme
							</th>
							<th>Anzahl als <br>Ausrichter
							</th>
							<th>zuletzt <br>Ausrichter
							</th>
							<th>Ausrichter / Teilnahme</th>
						</tr>
					</thead>
					<tbody>
						{foreach $ausrichter as $spieler}
						<tr>
							<th class="col-2 pl-2">{$spieler.vorname}</th>
							<td class="col-1 text-right pr-2">{$spieler.teilnahmen}</td>
							<td class="col-1 text-center pr-2">{$spieler.lteilnahme|date_format:"d.m.Y"}</td>
							<td class="col-1 text-right pr-2">{$spieler.ausrichter}</td>
							<td class="col-1 text-center pr-2">{$spieler.lausrichtung|date_format:"d.m.Y"}</td>
							<td class="col-1 text-right pr-2">{$spieler.ausrichter_teilnahmen|number_format:1:",":"."} %</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<!-- <p>Vom {$statistik.ersteRunde|date_format:"d.m.Y"} bis zum {$statistik.letzteRunde|date_format:"d.m.Y"} bei
				{$statistik.letzterAusrichter} haben wir in {$statistik.aufzeichnung} ungefähr alle {$statistik.wochen} Wochen
				insgesamt {$statistik.runden} Runden Doppelkopf gespielt.</p> -->
		</div>
	</div>
	{/if}
</div>
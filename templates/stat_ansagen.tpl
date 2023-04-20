<div class="container">
	<h2>Ansagen</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Alle freiwilligen Ansagen und deren Erfolg</p>
			<p>Re-Ansagen aufgrund von Solos, Armut (Trumpfabgabe) oder einer Hochzeit werden nicht berücksichtigt.</p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th rowspan="2">Spielername</th>
							<th rowspan="2">Spiele</th>
							<th rowspan="2">Ansagen</th>
							<th rowspan="2">Ansagen /<br>Spiel
							</th>
							<th colspan="2">davon Re</th>
							<th colspan="2">davon Kontra</th>
						</tr>
						<tr class="text-center">
							<th>Anzahl</th>
							<th>Gewonnen</th>
							<!-- <th>in Prozent</th> -->
							<th>Anzahl</th>
							<th>Gewonnen</th>
							<!-- <th>in Prozent</th> -->
						</tr>
					</thead>
					<tbody>
						{foreach $ansagen as $spieler}
						<tr>
							<th class="col-2 pl-2">{$spieler.vorname}</th>
							<td class="col-1 text-right pr-2">{$spieler.spiele}</td>
							<td class="col-1 text-right pr-2">{$spieler.ansagen}</td>
							<td class="col-1 text-right pr-2">{$spieler.ansagen_spiel|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.re}</td>
							<!-- <td class="col-1 text-right pr-2">{$spieler.re_gew_abs}</td> -->
							<td class="col-1 text-right pr-2">{$spieler.re_gew_proz|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.kontra}</td>
							<!-- <td class="col-1 text-right pr-2">{$spieler.kontra_gew_abs}</td> -->
							<td class="col-1 text-right pr-2">{$spieler.kontra_gew_proz|number_format:1:",":"."}&nbsp;%</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/if}
</div>
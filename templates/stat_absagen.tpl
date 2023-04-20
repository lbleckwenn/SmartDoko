<div class="container">
	<h2>Absagen</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Alle Absagen und deren Erfolg</p>
			<p></p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th rowspan="2">Spielername</th>
							<th rowspan="2">Spiele</th>
							<th rowspan="2">Absagen</th>
							<th rowspan="2">Absagen / Spiel</th>
							<th colspan="2">keine 90</th>
							<th colspan="2">keine 60</th>
							<th colspan="2">keine 30</th>
							<th colspan="2">schwarz</th>
						</tr>
						<tr class="text-center">
							<th>Anzahl</th>
							<th>Gewonnen</th>
							<th>Anzahl</th>
							<th>Gewonnen</th>
							<th>Anzahl</th>
							<th>Gewonnen</th>
							<th>Anzahl</th>
							<th>Gewonnen</th>
						</tr>
					</thead>
					<tbody>
						{foreach $ansagen as $spieler}
						<tr>
							<th class="col-2 pl-2">{$spieler.vorname}</th>
							<td class="col-1 text-right pr-2">{$spieler.spiele}</td>
							<td class="col-1 text-right pr-2">{$spieler.absagen}</td>
							<td class="col-1 text-right pr-2">{$spieler.absagenp|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.keine90}</td>
							<td class="col-1 text-right pr-2">{$spieler.keine90ep|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.keine60}</td>
							<td class="col-1 text-right pr-2">{$spieler.keine60ep|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.keine30}</td>
							<td class="col-1 text-right pr-2">{$spieler.keine30ep|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.schwarz}</td>
							<td class="col-1 text-right pr-2">{$spieler.schwarzep|number_format:1:",":"."}&nbsp;%</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/if}
</div>
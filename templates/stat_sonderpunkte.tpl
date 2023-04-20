<div class="container">
	<h2>Sonderpunkte</h2>
	{if $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{else}
	<div class="row">
		<div class="col-12">
			<p class="lead">Erhaltene Sonderpunkte oder verursachte Sonderpunkte pro Spiel</p>
			<p></p>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr class="text-center">
							<th>Spielername</th>
							<th>Spiele</th>
							<th>Dk</th>
							<th>Fgf</th>
							<th>Kgw</th>
							<th>Kgf</th>
							<th>Fv</th>
							<th>Kv</th>
						</tr>
					</thead>
					<tbody>
						{foreach $sonderpunkte as $spieler}
						<tr>
							<th class="col-3 pl-2">{$spieler.vorname}</th>
							<td class="col-1 text-right pr-2">{$spieler.spiele}</td>
							<td class="col-1 text-right pr-2">{$spieler.doppelkopf|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.fuchs_gefangen|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.karlchen_gewonnen|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.karlchen_gefangen|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.fuchs_verloren|number_format:1:",":"."}&nbsp;%</td>
							<td class="col-1 text-right pr-2">{$spieler.karlchen_verloren|number_format:1:",":"."}&nbsp;%</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<h4>Legende</h4>
			<dl class="row">
				<dt class="col-1">Dk</dt>
				<dd class="col-3">Doppelkopf</dd>
				<dt class="col-1">Fgf</dt>
				<dd class="col-3">Fuchs gefangen</dd>
				<dt class="col-1">Kgw</dt>
				<dd class="col-3">Karlchen gewonnen</dd>
				<dt class="col-1">Kgf</dt>
				<dd class="col-3">Karlchen gefangen</dd>
				<dt class="col-1">Fv</dt>
				<dd class="col-3">Fuchs verloren</dd>
				<dt class="col-1">Kv</dt>
				<dd class="col-3">Karlchen verloren</dd>
			</dl>
		</div>
	</div>
	{/if}
</div>
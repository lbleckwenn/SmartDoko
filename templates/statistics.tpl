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
			<p>Darüber hinaus soll es Statiskiken über die meisten gewonnenen Spiele, erfolgreichste Paarungen, gewonnene oder verlorene Karlchen oder gefangene Füchse geben.</p>
		</div>
	</div>
	<ul class="nav nav-pills mb-3">
		<li class="nav-item"><a class="nav-link active" id="statisticTab" data-toggle="tab" href="#statistic">Statistik</a></li>
		<li class="nav-item"><a class="nav-link" id="bestRoundTab" data-toggle="tab" href="#bestRound">Beste Runden</a></li>
		<li class="nav-item"><a class="nav-link" id="historyTab" data-toggle="tab" href="#history">Historie</a></li>

	</ul>
	<div class="tab-content" id="myTabContent">
		<div class="tab-pane fade show active" id="statistic" role="tabpanel" aria-labelledby="home-tab">
			<h3>Statistiken aus {$gamesOverall} Spielen</h3>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr>
							<th>Spieler</th>
							<th>Anzahl Spiele</th>
							<th>Punkte</th>
							<th>Durchschnitt</th>
						</tr>
					</thead>
					<tbody>
						{foreach $averagePoints as $playerData}
						<tr>
							<td>{$playerData.playerName}</td>
							<td>{$playerData.games}</td>
							<td>{$playerData.points}</td>
							<td>{$playerData.average|number_format:1:",":"."}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<h3>Häufigkeit der angesagten Spiele</h3>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr>
							<th>Spieler</th> {foreach $gameTypes as $gameTypeID => $gameTypeData}
							<th>{$gameTypeID}</th>{/foreach}
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Gesamt</th>{foreach $gameTypes as $gameTypeID => $gameTypeData}
							<td class="text-right">{$gameTypeData.overall.percent}% ({$gameTypeData.overall.absolut})</td>{/foreach}
						</tr>
						{foreach $players as $playerID => $player}
						<tr>
							<th>{$player}</th>{foreach $gameTypes as $gameTypeID => $gameTypeData}
							<td class="text-right">{if $gameTypeData.$playerID.absolut}{$gameTypeData.$playerID.percent}% ({$gameTypeData.$playerID.absolut}){else}-{/if}</td>{/foreach}
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			<h3>Erspielte Extrapunkte</h3>
			<div class="table-responsive">
				<table class="table table-bordered table-sm">
					<thead>
						<tr>
							<th>Extrapunkt</th> {foreach $players as $player}
							<th>{$player}</th>{/foreach}
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Fuchs gefangen</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.fuchs_gefangen)}{$extraPoints.$playerId.fuchs_gefangen}{else}-{/if}</td>{/foreach}
						</tr>
						<tr>
							<th>Doppelkopf</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.doppelkopf)}{$extraPoints.$playerId.doppelkopf}{else}-{/if}</td>{/foreach}
						</tr>
						<tr>
							<th>Karlchen</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.karlchen_gewonnen)}{$extraPoints.$playerId.karlchen_gewonnen}{else}-{/if}</td>{/foreach}
						</tr>
						<tr>
							<th>Karlchen gefangen</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.karlchen_gefangen)}{$extraPoints.$playerId.karlchen_gefangen}{else}-{/if}</td>{/foreach}
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<th>Fuchs verloren</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.fuchs_verloren)}{$extraPoints.$playerId.fuchs_verloren}{else}-{/if}</td>{/foreach}
						</tr>
						<tr>
							<th>Karlchen verloren</th>{foreach $players as $playerId => $player}
							<td class="text-right">{if isset($extraPoints.$playerId.karlchen_verloren)}{$extraPoints.$playerId.karlchen_verloren}{else}-{/if}</td>{/foreach}
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="tab-pane fade" id="bestRound" role="tabpanel" aria-labelledby="profile-tab">
			<h3>Die besten Doppelkopfrunden</h3>
		</div>
		<div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="contact-tab">
			<h3>Alle Doppelkopfrunden</h3>
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
							<td>{foreach $runde.player as $playerId => $punkte}{$players.$playerId}<span class="float-right">{$punkte}&nbsp;</span><br>{/foreach}
							</td>
							<td>{if $runde.is_running}läuft{else}beendet{/if}</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	{/if}
</div>
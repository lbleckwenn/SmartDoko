<div class="container">
	<h2>Spielzettel</h2>
	<div class="tab-pane fade show active" id="summen" role="tabpanel" aria-labelledby="summen-tab">
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th class="col-1">Spiel</th> {foreach $players_round as $player_id => $player}
						<th class="col-1">{$player.anzeige|truncate:7:""}</th> {/foreach}
						<th class="col-1">Punkte</th>
						<th>Sieger</th>
						<th>Augen</th>
						<th colspan="2">An-/Absagen, Sonderpunkte</th>
					</tr>
				</thead>
				<tbody>
					{for $i=1 to $aktuellesSpiel}
					<tr>
						<th class="pr-3 text-right">{$i}</th> {foreach $players_round as $player_id => $player}
						<td class="pr-4 text-right {if $punkteliste.$i.$player_id.sieger}table-success{/if}">{if $punkteliste.$i.$player_id.spielte}{$punkteliste.$i.$player_id.summe}{/if}</td>{/foreach}
						<th class="pr-4 text-right">{$punkteliste.$i.spiel}</th>
						<td>{$punkteliste.$i.sieger|ucfirst}</td>
						<td>{$punkteliste.$i.augen}</td>
						<td nowrap>{$punkteliste.$i.re}</td>
						<td nowrap>{$punkteliste.$i.kontra}</td>
					</tr>
					{/for}
				</tbody>
			</table>
		</div>
	</div>
</div>

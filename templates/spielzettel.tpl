<div class="container">
	<h2>Spielzettel</h2>
	<div class="tab-pane fade show active" id="summen" role="tabpanel" aria-labelledby="summen-tab">
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Spiel</th> {foreach $players_round as $player}
						<th>{$player|truncate:7:""}</th> {/foreach}
						<th>Punkte</th>
						<th colspan="2">An-/Absagen</th>
					</tr>
				</thead>
				<tbody>
					{for $i=1 to $aktuellesSpiel}
					<tr>
						<th class="text-right">{$i}</th>{foreach $players_round as $player_id => $player}
						<td class="text-right">{if $punkteliste.$i.$player_id.spielte}{$punkteliste.$i.$player_id.summe}{/if}</td>{/foreach}
						<th class="text-right">{$punkteliste.$i.spiel}</th>
						<td>{$punkteliste.$i.re}</td>
						<td>{$punkteliste.$i.kontra}</td>
					</tr>
					{/for}
				</tbody>
			</table>
		</div>
	</div>
</div>

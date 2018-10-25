<div class="container">
	<h2>Doppelkopfrunden</h2>
	<div class="table-responsive">
		<table class="table table-hover table-sm">
			<thead>
				<tr>
					<th rowspan="2">Ort</th>
					<th class="text-center" rowspan="2">Datum</th>
					<th class="text-center" rowspan="2">Spiele</th>
					<th rowspan="2">Mitspieler</th>
					<th class="text-center" colspan="2">Punkte</th>
					<th class="text-center col-1" rowspan="2">Siege</th>
					<th class="col-1" rowspan="2">&nbsp;</th>
				</tr>
				<tr>
					<th class="text-center col-1">Summe</th>
					<th class="text-center col-1">Plus/Minus</th>
				</tr>
			</thead>
			<tbody>
				{foreach $runden as $runde_id => $runde}
				<tr>
					<td>{$runde.location}</td>
					<td class="text-center">{$runde.date|date_format:"d.m.Y"}</td>
					<td class="text-center pr-4">{$runde.games}</td>
					<td>{foreach $runde.player as $key => $rang}{$players[$rang.player_id]}<br>{/foreach}
					</td>
					<td class="text-right pr-4">{foreach $runde.player as $key => $rang}{$rang.punkte_se}<br>{/foreach}
					</td>
					<td class="text-right pr-4">{foreach $runde.player as $key => $rang}{$rang.punkte_pm}<br>{/foreach}
					</td>
					<td class="text-right pr-4">{foreach $runde.player as $key => $rang}{$rang.anz_siege}<br>{/foreach}
					</td>
					<td>{if $runde.is_running} <a class="btn btn-success btn-sm btn-block" href="index.php?page=spielzettel&round_id={$runde_id}" role="button">laufende Runde</a>{else} <a
						class="btn btn-primary btn-sm btn-block" href="index.php?page=spielzettel&round_id={$runde_id}" role="button">Details</a>{/if}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>
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
					<tr data-toggle="modal" data-target="#spielDetails" data-gameid="{$punkteliste.$i.gameId}">
						<th class="pr-3 text-right">{$i}</th> {foreach $players_round as $player_id => $player}
						<td class="pr-4 text-right {if isset($punkteliste.$i.$player_id.sieger)}table-success{/if}">{if $punkteliste.$i.$player_id.spielte}{$punkteliste.$i.$player_id.summe}{/if}</td>{/foreach}
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
<!-- Modal -->
<div class="modal fade" id="spielDetails" tabindex="-1" role="dialog" aria-labelledby="Spieldetails" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="Spieldetails">Spieldetails</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<span id="testtext">...</span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
			</div>
		</div>
	</div>
</div>
<script>
$('#spielDetails').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var gameId = button.data('gameid')
	  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	  // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
	  //$('#testtext').text(gameId)
	  $(this).find('.modal-body').load('index.php?page=spieldetails&spielid=' + gameId);
	})
</script>
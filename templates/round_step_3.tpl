<h2>Spiel {$aktuellesSpiel}</h2>
<div class="row">
	<div class="col-sm-12 col-md-3 mt-3">
		<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#reservation">Vorbehalt</button>
		<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#announcement">An-/Absagen</button>
		<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#extraPoint">Sonderpunkte</button>
		<button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#gameCalculate">Spielabrechnung</button>
		<button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#endOfRound">Runde beenden</button>
	</div>
	<div class="col-sm-12 col-md-9">
		<div class="row">
			<div class="col-sm-12 col-md-7">
				<h3 class="mt-3">An- und Absagen</h3>
				<!-- Auflistung der An- und Absagen sowie Sonderpunkte -->
				{if $vorbehalt}
				<p class="mb-1">
					<a class="btn btn-danger btn-sm" href="index.php?page=round&reservation=1&delete=1" role="button">löschen</a> {$vorbehalt}
				</p>
				{/if} {if sizeof($ansagen)>0} {foreach $ansagen as $id => $ansage}
				<p class="mb-1">
					<a class="btn btn-danger btn-sm" href="index.php?page=round&ansage=1&delete={$id}" role="button">löschen</a> {$ansage}
				</p>
				{/foreach} {/if}{if sizeof($absagen)>0} {foreach $absagen as $id => $absage}
				<p class="mb-1">
					<a class="btn btn-danger btn-sm" href="index.php?page=round&absage=1&delete={$id}" role="button">löschen</a> {$absage}
				</p>
				{/foreach} {/if} {if sizeof($sonderpunkte)>0} {foreach $sonderpunkte as $id => $sonderpunkt}
				<p class="mb-1">
					<a class="btn btn-danger btn-sm" href="index.php?page=round&extraPoint=1&delete={$id}" role="button">löschen</a> {$sonderpunkt}
				</p>
				{/foreach} {/if}
			</div>
			<div class="col-sm-12 col-md-5">
				<h3 class="mt-3">Spieler</h3>
				<div class="row">
					{if sizeof($parteien) == 1} {$col="col-12"} {elseif isset($parteien.unklar)} {$col="col-4"} {else} {$col="col-6"} {/if} {foreach $parteien as $name => $spieler_partei}
					<div class="{$col}">
						<strong>{$name|ucfirst}</strong><br> {foreach $spieler_partei as $spieler} {$spieler}{if !$spieler@last}<br>{/if} {/foreach}
					</div>
					{/foreach}
				</div>
			</div>
			<div class="col-sm-12">
				<h3 class="mt-3">Punktestand</h3>
				<ul class="nav nav-tabs" id="punkte" role="tablist">
					<li class="nav-item"><a class="nav-link" id="plusminus-tab" data-toggle="tab" href="#plusminus" role="tab" aria-controls="plusminus" aria-selected="false">Plus/Minus</a></li>
					<li class="nav-item"><a class="nav-link active" id="summen-tab" data-toggle="tab" href="#summen" role="tab" aria-controls="summen" aria-selected="true">Summen</a></li>
				</ul>
				<div class="tab-content" id="myTabContent">
					<div class="tab-pane fade" id="plusminus" role="tabpanel" aria-labelledby="plusminus-tab">
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
									{for $i=$aktuellesSpiel to 1 step -1}
									<tr>
										<th class="text-right">{$i}</th>{foreach $players_round as $player_id => $player} {$h=$i-1}
										<td class="text-right {if $punkteliste.$i.$player_id.plusminus>$punkteliste.$h.$player_id.plusminus}table-success{/if}">{if !$i@first &&
											$punkteliste.$i.$player_id.spielte}{$punkteliste.$i.$player_id.plusminus}<!--  ({$punkteliste.$i.$player_id.punkte_spiel}) -->{/if}
										</td>{/foreach}
										<td class="text-right">{if !$i@first}{$punkteliste.$i.spiel}{/if}</td>
										<td>{$punkteliste.$i.re}</td>
										<td>{$punkteliste.$i.kontra}</td>
									</tr>
									{/for}
								</tbody>
							</table>
						</div>
					</div>
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
									{for $i=$aktuellesSpiel to 1 step -1}
									<tr>
										<th class="text-right">{$i}</th>{foreach $players_round as $player_id => $player}
										<td class="text-right">{if !$i@first && $punkteliste.$i.$player_id.spielte}{$punkteliste.$i.$player_id.summe}{/if}</td>{/foreach}
										<td class="text-right">{if !$i@first}{$punkteliste.$i.spiel}{/if}</td>
										<td>{$punkteliste.$i.re}</td>
										<td>{$punkteliste.$i.kontra}</td>
									</tr>
									{/for}
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- reservation -->
<div class="modal fade" id="reservation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Vorbehalt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=round&reservation=1" class="" method="post">
				{$token}
				<div class="modal-body">
					<div class="form-group row">
						<label for="player" class="col-sm-3 col-form-label">Spieler:</label>
						<div class="col-sm-9">
							<select class="form-control" id="player" name="spieler" required>
								<option value="">bitte auswählen</option>{html_options options=$players_game}
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="vorbehalt" class="col-sm-3 col-form-label">Vorbehalt:</label>
						<div class="col-sm-9">
							<select class="form-control" id="vorbehalt" name="vorbehalt" required> {html_options options=$gameTypes}
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="partner" class="col-sm-3 col-form-label">Partner:</label>
						<div class="col-sm-9">
							<select class="form-control" id="partner" name="partner" disabled>
								<option value="">bitte auswählen</option>{html_options options=$players_game}
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-primary">Spieler speichern</button>
				</div>
			</form>
			<script>
				$( '#vorbehalt').change(function() {
					var select = $('#vorbehalt').val();
					if (select != '2' && select != '4') {
						$('#partner').prop('required',false);
						$('#partner').prop('disabled',true);
						$('#partner').val('');
					} else {
						$('#partner').prop('required',true);
						$('#partner').prop('disabled',false);
					}
				});
			</script>
		</div>
	</div>
</div>
<!-- announcement -->
<div class="modal fade" id="announcement" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<!-- <h5 class="modal-title" id="exampleModalLongTitle">An- und Absagen</h5> -->
				<nav>
					<ul class="nav nav-tabs" id="anAbsage" role="tablist">
						<li class="nav-item"><a class="nav-link active" id="ansage-tab" data-toggle="tab" href="#ansage" role="tab">Ansage</a></li>
						<li class="nav-item"><a class="nav-link {if sizeof($ansagen)==0}disabled{/if}" id="absage-tab" data-toggle="tab" href="#absage" role="tab">Absage</a></li>
					</ul>
				</nav>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="tab-content" id="tabContent">
				<div class="tab-pane fade show active" id="ansage" role="tabpanel" aria-labelledby="ansage-tab">
					<form action="index.php?page=round&ansage=1" class="" method="post">
						{$token}
						<div class="modal-body">
							<div class="form-group row">
								<label for="player" class="col-sm-3 col-form-label">Spieler:</label>
								<div class="col-sm-9">
									<select class="form-control" id="player" name="spieler" required>
										<option value="">bitte auswählen</option>{html_options options=$players_game}
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="announcement" class="col-sm-3 col-form-label">Ansage:</label>
								<div class="col-sm-9">
									<select class="form-control" id="announcement" name="ansage" required>
										<option value="">bitte auswählen</option>
										<option value="re">Re</option>
										<option value="kontra">Kontra</option>
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
							<button type="submit" class="btn btn-primary">Ansage speichern</button>
						</div>
					</form>
				</div>
				<div class="tab-pane fade" id="absage" role="tabpanel" aria-labelledby="absage-tab">
					<form action="index.php?page=round&absage=1" class="" method="post">
						{$token}
						<div class="modal-body">
							<div class="form-group row">
								<label for="player" class="col-sm-3 col-form-label">Spieler:</label>
								<div class="col-sm-9">
									<select class="form-control" id="player" name="spieler" required>
										<option value="">bitte auswählen</option>{html_options options=$players_game}
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="announcement" class="col-sm-3 col-form-label">Absage:</label>
								<div class="col-sm-9">
									<select class="form-control" id="announcement" name="absage" required>
										<option value="">bitte auswählen</option>
										<option value="keine 90">keine 90</option>
										<option value="keine 60">keine 60</option>
										<option value="keine 30">keine 30</option>
										<option value="schwarz">schwarz</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="partei" class="col-sm-3 col-form-label">Partei:</label>
								<div class="col-sm-9">
									<select class="form-control" id="partei" name="partei">
										<option value="">bitte auswählen</option>
										<option value="re">Re</option>
										<option value="kontra">Kontra</option>
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
							<button type="submit" class="btn btn-primary">Absage speichern</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- extraPoint -->
<div class="modal fade" id="extraPoint" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Sonderpunkte</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=round&extraPoint=1" class="" method="post">
				{$token}
				<div class="modal-body">
					<div class="form-group row">
						<label for="player" class="col-sm-3 col-form-label">Spieler:</label>
						<div class="col-sm-9">
							<select class="form-control" id="player" name="spieler" required>
								<option value="">bitte auswählen</option>{html_options options=$players_game}
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="sonderpunkt" class="col-sm-3 col-form-label">Sonderpunkt:</label>
						<div class="col-sm-9">
							<select class="form-control" id="sonderpunkt" name="sonderpunkt" required>
								<option value="">bitte auswählen</option>
								<option value="doppelkopf">Doppelkopf</option>
								<option value="fuchs_gefangen">Fuchs gefangen</option>
								<option value="karlchen_gewonnen">Karlchen gewonnen</option>
								<option value="karlchen_gefangen">Karlchen gefangen</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<label for="looser" class="col-sm-3 col-form-label">von:</label>
						<div class="col-sm-9">
							<select class="form-control" id="looser" name="looser" disabled>
								<option value="">bitte auswählen</option>{html_options options=$players_game}
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-primary">Sonderpunkt speichern</button>
				</div>
			</form>
			<script>
				$( '#sonderpunkt').change(function() {
					var select = $('#sonderpunkt').val();
					if (select == 'fuchs_gefangen' || select == 'karlchen_gefangen') {
						$('#looser').prop('required',true);
						$('#looser').prop('disabled',false);
					} else {
						$('#looser').prop('required',false);
						$('#looser').prop('disabled',true);
						$('#looser').val('');
					}
				});
			</script>
		</div>
	</div>
</div>
<!-- gameCalculate -->
<div class="modal fade" id="gameCalculate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Spielabrechnung</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=round&gameCalculate=1" class="" method="post">
				{$token}
				<div class="modal-body">
					{if $gameType != 'solo'}
					<div class="form-group row">
						<label for="re1" class="col-sm-3 col-form-label">Re-Partei:</label>
						<div class="col-sm-4">
							<select class="form-control" id="re1" name="reSpieler1" required>
								<option value="">bitte auswählen</option>{html_options options=$players_game selected=$rePartei.0}
							</select>
						</div>
						<div class="col-sm-4">
							<select class="form-control" id="re2" name="reSpieler2">
								<option value="">bitte auswählen</option>
								<option value="solo">Stille Hochzeit</option>{html_options options=$players_game selected=$rePartei.1}
							</select>
						</div>
					</div>
					{else} <input type="hidden" name="reSpieler1" value="{$rePartei.0}"><input type="hidden" name="reSpieler2" value="solo"> {/if}
					<div class="form-group row">
						<label for="reAugen" class="col-sm-6 col-form-label">Augen der Re-Partei:</label>
						<div class="col-sm-6">
							<input type="tel" autocomplete="off" id="reAugen" maxlength="3" name="reAugen" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="kontraAugen" class="col-sm-6 col-form-label">Augen der Kontra-Partei:</label>
						<div class="col-sm-6">
							<input type="tel" autocomplete="off" id="kontraAugen" maxlength="3" name="kontraAugen" class="form-control" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-primary">Speichern</button>
				</div>
			</form>
			<script>
				$( '#reAugen').change(function() {
					var reAugen = $('#reAugen').val();
					$('#kontraAugen').val(240-reAugen);
				});
				$( '#kontraAugen').change(function() {
					var kontraAugen = $('#kontraAugen').val();
					$('#reAugen').val(240-kontraAugen);
				});
			</script>
		</div>
	</div>
</div>
<!-- endOfRound -->
<div class="modal fade" id="endOfRound" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Runde beenden</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=round&endOfRound=1" class="" method="post">
				{$token}
				<div class="modal-body">
					<p>Soll die Doppelkopfrunde wirklich beendet werden? Das notieren von weiteren Spielergebnissen zu dieser Runde ist dann nicht mehr möglich.</p>
					<input type="hidden" name="endOfRound" value="1">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Platz</th>
									<th>Name</th>
									<th>Punkte</th>
									<th>Siege</th>
									<!-- <th>Summe</th> -->
								</tr>
							</thead>
							<tbody>
								{foreach $sieger as $player_id => $daten}
								<tr>
									<th class="text-right">{$daten@index +1}</th>
									<td>{$daten.name}</td>
									<td class="text-right">{$daten.plusminus}</td>
									<td class="text-right">{$daten.siege}</td>
									<!-- <td class="text-right">{$daten.summe}</td> -->
								</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger">Runde beenden</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
				</div>
			</form>
			<script>
				$( '#reAugen').change(function() {
					var reAugen = $('#reAugen').val();
					$('#kontraAugen').val(240-reAugen);
				});
				$( '#kontraAugen').change(function() {
					var kontraAugen = $('#kontraAugen').val();
					$('#reAugen').val(240-kontraAugen);
				});
			</script>
		</div>
	</div>
</div>
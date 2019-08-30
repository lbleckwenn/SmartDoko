<h2>Doppelkopfrunden</h2>
<div class="row">
	<div class="col-sm-12 col-md-3">
		<h3 class="mt-3">Neue Runde</h3>
		<script src="./javascript/bootstrap-datepicker.js"></script>
		<script src="./javascript/bootstrap-datepicker.de.js"></script>
		<link rel="stylesheet" href="./stylesheet/bootstrap-datepicker3.css">
		<form action="?page=round&newRound=1" id="sandbox" class="" method="post">
			{$token}
			<div class="form-group">
				<label for="inputDatum">Datum</label>
				<div class="input-group date">
					<input type="text" class="form-control" id="inputDatum" name="datum" required>
					<div class="input-group-append">
						<span class="input-group-text">&#128197;</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="inputOrt">Ort der Runde:</label> <input type="text" class="form-control" id="inputOrt" name="ort" required>
			</div>
			<div class="form-group">
				<label for="inputPlayer">Anzahl Spieler:</label> <input type="number" class="form-control" id="inputPlayer" value="4"  maxlength="1" name="anzahl" required>
			</div>
			<button type="submit" class="btn btn-primary float-right">Neue Runde anlegen</button>
		</form>
		<script>
			$('#sandbox .input-group.date').datepicker({
				endDate: "0d",
				todayBtn: "linked",
				language: "de",
				orientation: "bottom left",
				daysOfWeekHighlighted: "0,6",
				autoclose: true,
				todayHighlight: true    	
			});
		</script>
	</div>
	<div class="col-sm-12 col-md-9">
		<h3 class="mt-3">Bisherige Runden</h3>
		<div class="table-responsive">
			<table class="table table-bordered table-sm">
				<thead>
					<tr>
						<th>Ort</th>
						<th>Datum</th>
						<th>Spiele</th>
						<th>Mitspieler</th>
						<th>Punkte</th>
						<th>Siege</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					{foreach $runden as $runde}
					<tr>
						<td>{$runde.location}</td>
						<td>{$runde.date|date_format:"d.m.Y"}</td>
						<td>{$runde.games}</td>
						<td>{foreach $runde.player as $player_id => $punkte}{$players.$player_id}<br>{/foreach}
						</td>
						<td>{foreach $runde.player as $player_id => $punkte}<span class="float-right">{$punkte}&nbsp;</span><br>{/foreach}
						</td>
						<td>{foreach $runde.siege as $player_id => $anzahl}<span class="float-right">{$anzahl}&nbsp;</span><br>{/foreach}
						</td>
						<td>{if $runde.is_running}läuft{else}beendet{/if}</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
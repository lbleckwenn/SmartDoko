<h2>Neue Doppelkopfrunde</h2>
<script src="./javascript/bootstrap-datepicker.js"></script>
<script src="./javascript/bootstrap-datepicker.de.js"></script>
<link rel="stylesheet" href="./stylesheet/bootstrap-datepicker3.css">
<form action="?page=round&newRound=1" id="sandbox" class="mt-5" method="post">
	<div class="form-group row">
		<label for="inputDatum" class="col-sm-2 col-form-label">Datum:</label>
		<div class="input-group date col-sm-3">
			<input type="text" id="inputDatum" maxlength="250" name="datum" class="form-control" required>
			<div class="input-group-append">
				<span class="input-group-text">&#128197;</span>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="inputOrt" class="col-sm-2 col-form-label">Ort der Runde:</label>
		<div class="col-sm-6">
			<input type="text" id="inputOrt" maxlength="250" name="ort" class="form-control" required>
		</div>
	</div>
	<div class="form-group row">
		<label for="inputPlayer" class="col-sm-2 col-form-label">Anzahl Spieler:</label>
		<div class="col-sm-6">
			<input type="number" id="inputPlayer" maxlength="1" name="anzahl" class="form-control" value="4" required>
		</div>
	</div>
	<button type="submit" class="btn btn-primary">Runde speichern</button>
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

<h2>Mitspieler</h2>
<form action="?page=round&selectPlayer=1" id="sandbox" class="mt-3" method="post">
	{for $s1 = 0 to $anzahlSpieler-1}
	<div class="form-group row">
		<label for="inputSpieler{$s1}" class="col-sm-2 col-form-label">Platz {$s1 +1}:</label>
		<div class="col-sm-8">
			<select class="form-control" id="inputSpieler{$s1}" name="spieler[{$s1}]" required> {html_options options=$players}
			</select>
		</div>
	</div>
	{if $anzahlSpieler>4}
	<div class="form-group row">
		<div class="col-sm-2"></div>
		<div class="col-sm-10">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="aussetzen[{$s1}]" name="aussetzen[{$s1}]"> <label class="form-check-label"
					for="aussetzen[{$s1}]"> setzt im ersten Spiel aus</label>
			</div>
		</div>
	</div>
	{/if} {/for}
	<button type="submit" class="btn btn-secondary" name="submit" value="abort">Abbrechen</button>
	<button type="submit" class="btn btn-primary" name="submit" value="save">Runde speichern</button>
</form>

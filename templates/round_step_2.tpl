<h2>Mitspieler</h2>
<form action="?page=round&selectPlayer=1" id="sandbox" class="mt-3" method="post">{$token}
	{for $s1 = 0 to $anzahlSpieler-1}
	<div class="form-group row">
		<label for="inputSpieler{$s1}" class="col-sm-2 col-form-label">Platz {$s1 +1}:</label>
		<div class="col-sm-8">
			<select class="form-control" id="inputSpieler{$s1}" name="spieler[{$s1}]" required> {html_options options=$players}
			</select>
		</div>
	</div>
	{/for}
	<p>Mit dem Geben der Karten zum ersten Spiel beginnt der Mitspieler auf Platz 1. {if $anzahlSpieler == 5}Der Kartengeber erhält selbst keine Karten. {elseif $anzahlSpieler > 5}Die Spieler auf den
		Plätzen 1 bis {$anzahlSpieler-4} erhalten im ersten Spiel keine Karten. {/if}Der Spieler auf Platz {$anzahlSpieler} ist grundsätzlich der Listenführer.</p>
	<button type="submit" class="btn btn-secondary" name="submit" value="abort">Abbrechen</button>
	<button type="submit" class="btn btn-primary" name="submit" value="save">Runde speichern</button>
</form>

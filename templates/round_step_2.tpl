<h2>Mitspieler</h2>
<form action="?page=round&selectPlayer=1" id="sandbox" class="mt-3" method="post">{$token}
{for $s1 = 0 to $anzahlSpieler-1}
	<div class="form-row align-items-center">
		<div class="col-auto my-1 form-inline">
			<label class="mr-sm-2" for="inlineFormCustomSelect">Platz {$s1 +1}:</label>
			<select class="custom-select mr-sm-2" name="spieler[{$s1}]" id="inputSpieler{$s1}" required>
				{html_options options=$players}
			</select>
		</div>
		<div class="col-auto my-1">
			<div class="custom-control custom-checkbox mr-sm-2">
				<input type="radio" name="geber" class="custom-control-input" value="{$s1}" id="geber{$s1}"{if $s1 == 0}checked{/if}>
				<label class="custom-control-label" for="geber{$s1}">1. Kartengeber</label>
			</div>
		</div>
		<div class="col-auto my-1">
			<div class="custom-control custom-checkbox mr-sm-2">
				<input type="checkbox" name="aussetzer[{$s1}]" class="custom-control-input" value="{$s1}" id="aussetzer{$s1}">
				<label class="custom-control-label" for="aussetzer{$s1}">Setzt 1. Spiel aus</label>
			</div>
		</div>
	</div>
{/for}
	<p>Mit dem Geben der Karten zum ersten Spiel beginnt der Mitspieler auf Platz 1. {if $anzahlSpieler == 5}Der Kartengeber erhält selbst keine Karten. {elseif $anzahlSpieler > 5}Die Spieler auf den
		Plätzen 1 bis {$anzahlSpieler-4} erhalten im ersten Spiel keine Karten. {/if}Der Spieler auf Platz {$anzahlSpieler} ist grundsätzlich der Listenführer.</p>
	<button type="submit" class="btn btn-secondary" name="submit" value="abort">Abbrechen</button>
	<button type="submit" class="btn btn-primary" name="submit" value="save">Runde speichern</button>
</form>

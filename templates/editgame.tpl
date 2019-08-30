{function dropdown single=true selected1=0 selected2=0}
<div class="input-group mb-3">
	{if $single}
	<div class="input-group-prepend">
		<span class="input-group-text" {* style="width: 5.3em;"*}>{$inputGroupText}</span>
	</div>
	{/if} <select class="custom-select" name="{$selectName|replace:' ':''}">
		<option {if !$selected1} selected{/if} value='0'>Spieler auswählen...</option> {foreach $players as $playerId =>
		$player}
		<option {if $selected1==$playerId}selected{/if} value="{$playerId}">{$player.vorname}</option>{/foreach}
	</select> {if !$single}
	<div class="input-group-prepend input-group-append">
		<div class="input-group-text">{$inputGroupText}</div>
	</div>
	<select class="custom-select" name="{$selectName|replace:' ':''}2">
		<option {if !$selected2} selected{/if} value='0'>Spieler auswählen...</option> {foreach $players as $playerId =>
		$player}
		<option {if $selected2==$playerId}selected{/if} value="{$playerId}">{$player.vorname}</option>{/foreach}
	</select> {/if}
</div>
{/function}
<div class="container">
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Erfolg</h4>
		<p>
			{$success}</a>
		</p>
	</div>
	{elseif $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler!</h4>
		<p>{$error}</p>
	</div>
	{/if}
	<h2>Spiel berichtigen</h2>
	<form action="?page=editgame" method="post">
		<div class="row">
			<div class="col">
				<script src="./javascript/editgame_javascript.js"></script>
				<h3 class="mt-3">Die Alten</h3>
				<div class="row">
					{foreach $players as $playerId => $player}
					<div class="col-12 col-sm-6 mb-3">
						<span class="button-checkbox">
							<button type="button" class="btn btn-block" data-color="success">&nbsp;{$player.vorname}</button> <input
							type="checkbox" name="dieAlten[]" value="{$playerId}" style="display: none;" {if $player.partei== 're'}checked{/if}/>
						</span>
					</div>
					{/foreach}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h3 class="mt-3">Spieltyp</h3>
				<div class="row">
					{foreach $gameTypes as $gameTypId => $gameTyp} {if $gameTypId==2}
					<div class="col-12 col-lg-6">{dropdown inputGroupText="spielt eine Hochzeit mit" selectName="hochzeit"
						selected1="{if $gameData.gameType==2}{$gameData.playerId}{else}0{/if}" single=false selected2="{if
						$gameData.gameType==2}{$gameData.mateId}{else}0{/if}"}</div>
					{elseif $gameTypId==4}
					<div class="col-12 col-lg-6">{dropdown inputGroupText="gibt seite Trümpfe an" selectName="armut" selected1="{if
						$gameData.gameType==4}{$gameData.playerId}{else}0{/if}" single=false selected2="{if
						$gameData.gameType==4}{$gameData.mateId}{else}0{/if}"}</div>
					{else}
					<div class="col-12 col-sm-4 col-md-3 col-xl-2 mb-3">
						<span class="button-checkbox">
							<button type="button" class="btn btn-block" data-color="primary">&nbsp;{$gameTyp.text}</button> <input
							type="checkbox" name="spieltyp" value="{$gameTypId}" style="display: none;" {if $gameData.gameType== $gameTypId}checked{/if}/>
						</span>
					</div>
					{/if}{/foreach}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h3 class="mt-3">Augen</h3>
				<div class="form-group">
					<label for="formControlRange">Re <span id="eyesRe">{$game.re_augen}</span></label> <label for="formControlRange"
						class="float-right">Kontra <span id="eyesKontra">{240-$game.re_augen}</span>
					</label> <input type="range" id="eyesRange" value="{120-$game.re_augen}" min="-120" max="120"
						class="form-control-range" oninput="updateEyes(eyesRange.value)">
				</div>
				<script>
            		function updateEyes(eyes) {
                		eyesRe = 120-eyes
                		eyesKontra = parseInt(eyes) + 120
                		$('#eyesRe').html(eyesRe)
                		$('#eyesKontra').html(eyesKontra)
            		}
        		</script>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h3 class="my-3">An- und Absagen</h3>
				{$absagen = ["keine 90","keine 60","keine 30","schwarz"]}
				<div class="row">
					<div class="col-12 col-md-6">
						<h4>Re-Partei</h4>
						{dropdown inputGroupText="Re" selectName="re" selected1=$gameData.re.ansage} {foreach $absagen as $absage}
						{dropdown inputGroupText=$absage selectName="re"|cat:$absage selected1=$gameData.re.$absage} {/foreach}
					</div>
					<div class="col-12 col-md-6">
						<h4>Kontra-Partei</h4>
						{dropdown inputGroupText="Kontra" selectName="kontra" selected1=$gameData.kontra.ansage} {foreach $absagen as $absage}
						{dropdown inputGroupText=$absage selectName="kontra"|cat:$absage selected1=$gameData.kontra.$absage} {/foreach}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<h3 class="mt-3">Sonderpunkte</h3>
				<div class="row">
					<div class="col-12 col-lg-6">{dropdown inputGroupText="fängt Fuchs von" selectName="fuchs_gefangen1"
						selected1=$gameData.fuchs_gefangen[1].g single=false selected2=$gameData.fuchs_gefangen[1].v} {dropdown
						inputGroupText="fängt Fuchs von" selectName="fuchs_gefangen2" selected1=$gameData.fuchs_gefangen[2].g single=false
						selected2=$gameData.fuchs_gefangen[2].v} {dropdown inputGroupText="fängt Karlchen von"
						selectName="karlchen_gefangen1" selected1=$gameData.karlchen_gefangen[1].g single=false
						selected2=$gameData.karlchen_gefangen[1].v} {dropdown inputGroupText="fängt Karlchen von"
						selectName="karlchen_gefangen2" selected1=$gameData.karlchen_gefangen[2].g single=false
						selected2=$gameData.karlchen_gefangen[2].v}</div>
					<div class="col-12 col-lg-6">{dropdown inputGroupText="Karlchen letzter Stich" selectName="karlchen_gewonnen"
						selected1=$gameData.karlchen_gewonnen} {dropdown inputGroupText="Doppelkopf" selectName="doppelkopf1"
						selected1=$gameData.doppelkopf[1]} {dropdown inputGroupText="Doppelkopf" selectName="doppelkopf2"
						selected1=$gameData.doppelkopf[2]}</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col text-right">
				<input type="submit" class="btn btn-primary mb-5 col-2" value="Speichern">
			</div>
		</div>
	</form>
</div>

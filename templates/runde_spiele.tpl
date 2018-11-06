<script src="./javascript/runde_javascript.js"></script>

<script>
spielTyp = 1;
var menu = 'ms1';
var alleSpieler = {ldelim}{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}: '{$spieler.vorname}'{/foreach}{rdelim};

</script>

<h3>Spiel {$aktuellesSpiel} - Geber: {$geber}</h3>
<div class="row">
	<div class="col-sm-12 col-md-3 mt-3" id="menu">
		<span id="ms1"> {foreach $alleSpieler as $spielerId => $spieler}
			<button type="button" class="btn btn-primary btn-block" id="{$spielerId}">{$spieler.vorname}</button> {/foreach}
			<button type="button" class="btn btn-success btn-block">Spielabrechnung</button>
			<button type="button" class="btn btn-danger btn-block">Runde beenden</button>
		</span>
	</div>
</div>
<script>
$("#ms1 > button.btn").on("click", function(event){
	var spielerId = this.id;
	if(spielTyp) {
		$('#menu').html(createMenu('ms3',['re','kontra','keine90','keine60','keine30','schwarz','sonderpunkt','stillehochzeit','abbrechen']));
	} else {
		$('#menu').html(createMenu('ms2',['re','kontra','sonderpunkt','hochzeit','armut','solo','abbrechen']));
	}
});
</script>
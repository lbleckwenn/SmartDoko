<script>
var alleSpieler = {ldelim}{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}: '{$spieler.vorname}'{/foreach}{rdelim};
var alleSpielerIds = [{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}{/foreach}];
var alleSpielerNamen = [{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}'{$spieler.vorname}'{/foreach}];


</script>

<h3>Spiel {$aktuellesSpiel}</h3>
<p class="lead">Geber:<span class="float-right">{$geber}</span></p>
<div class="row">
	<div class="col-sm-12 col-md-3" id="menu">
	</div>
</div>

<script src="./javascript/runde_javascript.js"></script>

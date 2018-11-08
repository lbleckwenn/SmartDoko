<script>
var alleSpielerIds = [{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}{/foreach}];
var spielDaten = {
	alleSpieler : {
		5 : {
			vorname : 'Holger',
			punkte : 0,
			partei : null
		},
		20 : {
			vorname : 'Arne',
			punkte : 0,
			partei : null
		},
		22 : {
			vorname : 'Michael',
			punkte : 0,
			partei : null
		},
		1 : {
			vorname : 'Lars',
			punkte : 0,
			partei : null
		}
	},
	spielTyp : 0,
	ansagen : {
		re : 0,
		kontra : 0
	},
	absagen : {
		re : null,
		kontra : null
	}
};
</script>

<h3>Spiel {$aktuellesSpiel}</h3>
<p class="lead">
	Geber:<span class="float-right">{$geber}</span>
</p>
<div class="row">
	<div class="col-sm-12 col-md-3" id="menu"></div>
</div>

<script src="./javascript/runde_javascript.js"></script>

<script>
var alleSpielerIds = [{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}{/foreach}];
var spielDaten = {
	alleSpieler : {
		4 : {
			vorname : 'Heiko',
			punkte : 0,
			partei : null
		},
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
		1 : {
			vorname : 'Lars',
			punkte : 0,
			partei : null
		}
	},
	spielTyp : null,
	ansagen : {
		re : false,
		kontra : false
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

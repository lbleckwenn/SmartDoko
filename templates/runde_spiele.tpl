<script>
var spielDaten = {
	spieler: {$jsObjectSpieler},
	spielerIds: [{foreach $alleSpieler as $spielerId => $spieler}{if !$spieler@first}, {/if}{$spielerId}{/foreach}],
	spielTyp : {$jsObjectSpielTyp},
	anzahl : {$jsObjectAnzahl},
	ansagen : {$jsObjectAnsagen},
	absagen :{$jsObjectAbsagen}
};
</script>

<h3>Spiel {$aktuellesSpiel}</h3>
<p class="lead">
	Geber:<span class="float-right">{$geber}</span>
</p>
<div class="row">
	<form action="index.php?page=runde&spieldaten=1" class="col-12" id="formSpielDaten" method="post">
		{$token}
		<div class="col-12" id="menu"></div>
	</form>
</div>
<div class="invisible" id="rangliste">
	<p class="font-weight-bold">Zwischenstand nach {$aktuellesSpiel-1} Spielen:</p>
	<div class="table-responsive">
		<table class="table table-bordered table-sm">
			<thead>
				<tr>
					<th>Platz</th>
					<th>Name</th>
					<th>Punkte</th>
				</tr>
			</thead>
			<tbody>
				{foreach $alleSpieler as $spielerId => $spieler}
				<tr>
					<td class="col-1 text-right pr-2">{$spieler@iteration}</td>
					<td>{$spieler.vorname}</td>
					<td class="text-right pr-2">{$spieler.punkte+0}</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	<button type="button" class="btn btn-danger btn-block"
		onclick="$('#menu').html(createMenu('Auswahl:', players, [ 'zwischenstand', 'abrechnung', 'beenden' ]))">Zurück</button>
</div>
<script src="./javascript/runde_javascript.js"></script>
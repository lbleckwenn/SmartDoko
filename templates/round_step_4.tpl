<h2>
	<strong>{$gewinner}</strong> gewinnt Spiel {$aktuellesSpiel}
</h2>
<div class="row mt-3">
	<div class="col-sm-12">
		<div id="accordion">
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Punkte</button>
					</h5>
				</div>
				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body">
						{foreach $parteien as $id => $partei} {if $gameType=='solo' && $partei == 're'}{$multi = 3}{else}{$multi = 1}{/if}
						<p class="mb-1">{$players_game.$id} ({$partei|ucfirst}) erhält {$log.$partei * $multi} Punkte.</p>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header" id="headingTwo">
					<h5 class="mb-0">
						<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
							Wertung</button>
					</h5>
				</div>
				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
					<div class="card-body">
						<div class="table-responsive-sm">
							<table class="table table-sm">
								<thead>
									<tr>
										<th colspan="2">Detalierte Aufstellung</th>
										<th class="text-right">Punkte</th>
									</tr>
								</thead>
								<tbody>
									{foreach $log.log as $regel => $regelPunkte}
									<tr>
										<td colspan="3">Regel {$regel}</td>
									</tr>
									{foreach $regelPunkte as $punkt}
									<tr>
										<td>&nbsp;</td>
										<td>{$punkt.text}</td>
										<td class="text-right">{$punkt.punkte}</td>
									</tr>
									{/foreach} {/foreach}
								</tbody>
								<tfoot>
									<tr>
										<td><strong>Summe</strong></td>
										<td>&nbsp;</td>
										<td class="text-right"><strong>{$log.re|abs}</strong></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header" id="headingTwo">
					<h5 class="mb-0">
						<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">
							Zusammenfassung</button>
					</h5>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
					<div class="card-body">
						<p>Ansagen</p>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<form action="index.php?page=round&gameCalculate=1" class="" method="post">
				{if $gameType != 'solo'} <input type="hidden" name="save" value="1"><input type="hidden" name="reSpieler1" value="{$reSpieler1}"><input
					type="hidden" name="reSpieler2" value="{$reSpieler2}"> {else} <input type="hidden" name="reSpieler1" value="{$reSpieler1}"><input type="hidden"
					name="reSpieler2" value="solo"><input type="hidden" name="reAugen" value="{$reAugen}"><input type="hidden" name="kontraAugen"
					value="{$kontraAugen}"> {/if} <a class="btn btn-secondary" href="index.php?page=round" role="button">Abbrechen</a>
				<button type="submit" class="btn btn-primary">Spiel speichern</button>
			</form>
		</div>
	</div>
</div>
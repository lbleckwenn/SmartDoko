<div class="modal-header">
	<h5 class="modal-title" id="Spieldetails">
		<strong>{$gewinner}</strong> gewinnt
	</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<div id="accordion">
				<div class="card">
					<div class="card-header" id="headingOne">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">Zusammenfassung</button>
						</h5>
					</div>
					<div id="collapseThree" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
						<div class="card-body">
							<table class="table table-sm border-bottom">
								<tbody>
									<tr>
										<td><strong>Re</strong></td>
										<td>{foreach $partei.re.spieler as $id}{$players_game.$id}{if !$id@last}<br>{/if}{/foreach}
										</td>
										<td class="text-right pr-4">{$partei.re.punkte} Punkt{if abs($partei.re.punkte)!=1}e{/if}</td>
										<td>{$partei.re.augen} Augen</td>
									</tr>
									<tr>
										<td><strong>Kontra</strong></td>
										<td>{foreach $partei.kontra.spieler as $id}{$players_game.$id}{if !$id@last}<br>{/if}{/foreach}
										</td>
										<td class="text-right pr-4">{$partei.kontra.punkte} Punkt{if abs($partei.kontra.punkte)!=1}e{/if}</td>
										<td>{$partei.kontra.augen} Augen</td>
									</tr>
								</tbody>
							</table>
							{if $vorbehalt || sizeof($ansagen)+sizeof($absagen)+sizeof($sonderpunkte)>0} <strong class="mt-3">Spielverlauf</strong>
							<!-- Auflistung der An- und Absagen sowie Sonderpunkte -->
							{if $vorbehalt}
							<p class="mb-1">{$vorbehalt}</p>
							{/if} {if sizeof($ansagen)>0} {foreach $ansagen as $id => $ansage}
							<p class="mb-1">{$ansage}</p>
							{/foreach} {/if}{if sizeof($absagen)>0} {foreach $absagen as $id => $absage}
							<p class="mb-1">{$absage}</p>
							{/foreach} {/if} {if sizeof($sonderpunkte)>0} {foreach $sonderpunkte as $id => $sonderpunkt}
							<p class="mb-1">{$sonderpunkt}</p>
							{/foreach} {/if}{/if}
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingTwo">
						<h5 class="mb-0">
							<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Wertung</button>
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
										{foreach $log as $regel => $regelPunkte}
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
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
</div>

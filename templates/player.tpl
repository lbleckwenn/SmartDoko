<div class="container">
	<h2>
		Mitspieler<small class="float-right"><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newPlayer">Mitspieler
				hinzufügen</button> </small>
	</h2>
	{if $success}
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Erfolg</h4>
		<p>
			{$success}</a>
		</p>
	</div>
	{elseif $error}
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Fehler</h4>
		<p>{$error}</p>
	</div>
	{/if}
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>#</th>
				<th>Vorname</th>
				<th>Nachname</th>
				<th>Runden</th>
				<th>Benutzerkonto</th>
				<th>Optionen</th>
			</tr>
			{foreach $players as $player}
			<tr>
				<td>{$player.id}</td>
				<td>{$player.vorname}</td>
				<td>{$player.nachname}</td>
				<td class="text-center">0</td>
				<td class="text-center">{if $player.user_id}ja{/if}</td>
				<td class="text-nowrap">{if $user.id == $player.user_id} <a class="btn btn-primary btn-sm" href="index.php?page=settings" role="button">Einstellungen</a>
					{else}
					<button type="button" class="btn btn-warning btn-sm" {if $player.user_id}disabled{/if} data-toggle="modal" data-id="{$player.id}"
						data-vorname="{$player.vorname}" data-nachname="{$player.nachname}" data-email="{$player.email}" data-target="#editPlayer">Bearbeiten</button>
					<button type="button" class="btn btn-danger btn-sm" {if 1==0}disabled{/if} data-toggle="modal" data-id="{$player.id}"
						data-vorname="{$player.vorname}" data-nachname="{$player.nachname}" data-target="#deletePlayer">Löschen</button>
					<button type="button" class="btn btn-info btn-sm" {if $player.user_id}disabled{/if} data-toggle="modal" data-id="{$player.id}"
						data-vorname="{$player.vorname}" data-nachname="{$player.nachname}" data-target="#invitePlayer">Einladen</button> {/if}
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
	{if sizeof($players)>0}
	<p class="text-center">
		<small>Mitspieler können nur gelöscht werden, solange noch keine Doppelkopfrunde mit ihnen gespielt worden ist.</small>
	</p>
	{/if}
</div>

<!-- newPlayer -->
<div class="modal fade" id="newPlayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Mitspieler hinzufügen</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=player&newPlayer=1" class="" method="post">{$token}
				<div class="modal-body">
					<div class="form-group row">
						<label for="inputVorname" class="col-sm-3 col-form-label">Vorname:</label>
						<div class="col-sm-9">
							<input type="text" id="inputNewVorname" maxlength="250" name="vorname" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputNachname" class="col-sm-3 col-form-label">Nachname:</label>
						<div class="col-sm-9">
							<input type="text" id="inputNewNachname" maxlength="250" name="nachname" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputEmail" class="col-sm-3 col-form-label">E-Mail:</label>
						<div class="col-sm-9">
							<input type="email" id="inputNewEmail" maxlength="250" name="email" class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-primary">Spieler speichern</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- editPlayer -->
<div class="modal fade" id="editPlayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Mitspieler bearbeiten</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=player&editPlayer=1" class="" method="post">{$token}
				<div class="modal-body">
					<input type="hidden" id="inputEditId" name="player_id">
					<div class="form-group row">
						<label for="inputVorname" class="col-sm-3 col-form-label">Vorname:</label>
						<div class="col-sm-9">
							<input type="text" id="inputEditVorname" maxlength="250" name="vorname" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputNachname" class="col-sm-3 col-form-label">Nachname:</label>
						<div class="col-sm-9">
							<input type="text" id="inputEditNachname" maxlength="250" name="nachname" class="form-control" required>
						</div>
					</div>
					<div class="form-group row">
						<label for="inputEmail" class="col-sm-3 col-form-label">E-Mail:</label>
						<div class="col-sm-9">
							<input type="email" id="inputEditEmail" maxlength="250" name="email" class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-primary">Spieler speichern</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- deletePlayer -->
<div class="modal fade" id="deletePlayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Mitspieler löschen</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=player&deletePlayer=1" class="" method="post">{$token}
				<div class="modal-body">
					<input type="hidden" id="inputDeleteId" name="player_id">
					<p>
						Soll <span id="inputDeleteVorname"></span> <span id="inputDeleteNachname"></span> wirklich gelöscht werden?
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="submit" class="btn btn-danger">Spieler löschen</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- invitePlayer -->
<div class="modal fade" id="invitePlayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Mitspieler einladen</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="index.php?page=player&invitePlayer=1" class="" method="post">{$token}
				<div class="modal-body">
					<input type="hidden" id="inputDeleteId" name="player_id">
					<p>Du kannst deine Mitspieler einladen, ein eigenes Benutzerkonto für SmartDoko zu erstellen. Damit ist es ihnen möglich, die Ergebnisse eurer
						Doppelkopfrunden einzusehen oder selbst zu erfassen.</p>
					<div class="form-group">
						<label for="message">Persönliche Nachricht an <span id="inputInviteVorname"></span> <span id="inputInviteNachname"></span>:
						</label>
						<textarea class="form-control" id="message" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
					<button type="button" class="btn btn-primary">Einladeung senden</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$('#editPlayer').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) 
	  $('#inputEditId').val(button.data('id'))
	  $('#inputEditVorname').val(button.data('vorname'))
	  $('#inputEditNachname').val(button.data('nachname'))
	  $('#inputEditEmail').val(button.data('email'))
	})
$('#deletePlayer').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) 
	  $('#inputDeleteId').val(button.data('id'))
	  $('#inputDeleteVorname').text(button.data('vorname'))
	  $('#inputDeleteNachname').text(button.data('nachname'))
	})
$('#invitePlayer').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) 
	  $('#inputInviteVorname').text(button.data('vorname'))
	  $('#inputInviteNachname').text(button.data('nachname'))
	})
	</script>
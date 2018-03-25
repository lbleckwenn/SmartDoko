<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-24 22:10:17
  from 'E:\xampp\htdocs\SmartDoko\templates\register.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab6beb90534d5_23334636',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'df2631b9b3ec23ae152802a83eba86e1a685105a' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\register.tpl',
      1 => 1521821948,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab6beb90534d5_23334636 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container">
	<h2>Neues Benutzerkonto erstellen</h2>
	<?php if ($_smarty_tpl->tpl_vars['success']->value) {?>
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Benutzerkonto erstellt</h4>
		<p>
			Du wurdest erfolgreich registriert.
			<a href="index.php?page=login">Zur Anmeldung</a>
		</p>
	</div>
	<?php } else { ?> <?php if ($_smarty_tpl->tpl_vars['error']->value) {?>
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
	</div>
	<?php }?>
	<form action="?page=register&register=1" class="mt-5" method="post">
		<div class="form-group row">
			<label for="inputVorname" class="col-sm-2 offset-sm-2 col-form-label">Vorname:</label>
			<div class="col-sm-6">
				<input type="text" id="inputVorname" maxlength="250" name="vorname" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputNachname" class="col-sm-2 offset-sm-2 col-form-label">Nachname:</label>
			<div class="col-sm-6">
				<input type="text" id="inputNachname" maxlength="250" name="nachname" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 offset-sm-2 col-form-label">E-Mail:</label>
			<div class="col-sm-6">
				<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort" class="col-sm-2 offset-sm-2 col-form-label">Dein Passwort:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort" id="inputPasswort">
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort2" class="col-sm-2 offset-sm-2 col-form-label">Passwort wiederholen:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort2" id="inputPasswort2">
			</div>
		</div>
		<button type="submit" class="btn btn-primary col-sm-2">Registrieren</button>
	</form>
	<?php }?>
</div>
<?php }
}

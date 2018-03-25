<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-23 15:47:20
  from 'E:\xampp\htdocs\SmartDoko\templates\login.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab5137850dfe4_20236883',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f6d42dc7c01e936f3382d4be8a8741e45706d133' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\login.tpl',
      1 => 1521801448,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab5137850dfe4_20236883 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container">
	<h2>Anmeldung</h2>
	<?php if ($_smarty_tpl->tpl_vars['error']->value) {?>
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
	</div>
	<?php }?>
	<form action="index.php?page=login" class="mt-5" method="post">
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 offset-sm-2 col-form-label">E-Mail:</label>
			<div class="col-sm-6">
				<input type="email" id="inputEmail" maxlength="250" name="email" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['email_value']->value;?>
" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="inputPasswort" class="col-sm-2 offset-sm-2 col-form-label">Dein Passwort:</label>
			<div class="col-sm-6">
				<input type="password" class="form-control" name="passwort" id="inputPasswort">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-2 offset-sm-2"></div>
			<div class="col-sm-6">
				<div class="form-check">
					<input type="checkbox" class="form-check-input" value="remember-me" name="angemeldet_bleiben" value="1" checked>
					<label class="form-check-label" for="gridCheck1"> Angemeldet bleiben </label>
				</div>
			</div>
		</div>
		<a href="index.php?page=passwortvergessen" class="btn btn-secondary col-sm-2 offset-sm-6" role="button">Passwort vergessen</a>
		<button class="btn btn-primary col-sm-2" type="submit">Login</button>
	</form>
</div><?php }
}

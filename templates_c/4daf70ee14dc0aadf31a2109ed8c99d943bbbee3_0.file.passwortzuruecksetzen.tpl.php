<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-23 15:33:18
  from 'E:\xampp\htdocs\SmartDoko\templates\passwortzuruecksetzen.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab5102ed09215_33613187',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4daf70ee14dc0aadf31a2109ed8c99d943bbbee3' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\passwortzuruecksetzen.tpl',
      1 => 1521815596,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab5102ed09215_33613187 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container">
	<h1>Neues Passwort vergeben</h1>
	<?php if ($_smarty_tpl->tpl_vars['success']->value) {?>
	<div class="alert alert-success" role="alert">
		<h4 class="alert-heading">Benutzerkonto erstellt</h4>
		<p>
			Dein Passwort wurde erfolgreich geändert. <a href="index.php?page=login">Zur Anmeldung</a>
		</p>
	</div>
	<?php } elseif ($_smarty_tpl->tpl_vars['error']->value) {?>
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
	</div>
	<?php } else { ?> <?php if ($_smarty_tpl->tpl_vars['msg']->value) {?>
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
</p>
	</div>
	<?php }?>
	<form action="?send=1&amp;userid=<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
&amp;code=<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
" method="post">
		<label for="passwort">Bitte gib ein neues Passwort ein:</label><br> <input type="password" id="passwort" name="passwort" class="form-control"
			required><br> <label for="passwort2">Passwort erneut eingeben:</label><br> <input type="password" id="passwort2" name="passwort2"
			class="form-control" required><br> <input type="submit" value="Passwort speichern" class="btn btn-lg btn-primary btn-block">
	</form>
	<?php }?>
</div><?php }
}

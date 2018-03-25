<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-24 16:33:11
  from 'E:\xampp\htdocs\SmartDoko\templates\overview.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab66fb721afb5_79107460',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'deebf13fe21547b1d1983dfcf7d0b9510be13faa' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\overview.tpl',
      1 => 1521905584,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab66fb721afb5_79107460 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container">
	<h2>Übersicht</h2>
	<?php if (isset($_smarty_tpl->tpl_vars['error']->value)) {?>
	<div class="alert alert-danger" role="alert">
		<h4 class="alert-heading">Es ist ein Fehler aufgetreten!</h4>
		<p><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</p>
	</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>#</th>
				<th>Vorname</th>
				<th>Nachname</th>
				<th>E-Mail</th>
			</tr>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['users']->value, 'user');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
?>
			<tr>
				<td><?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['user']->value['vorname'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['user']->value['nachname'];?>
</td>
				<td><a href="mailto:<?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
</a></td>
			</tr>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</table>
	</div>
	<?php }?>
</div><?php }
}

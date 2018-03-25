<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-24 20:35:11
  from 'E:\xampp\htdocs\SmartDoko\templates\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab6a86fc69c52_01224150',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b3d75f0506e9fb5294c00939e116efaba0e8d467' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\index.tpl',
      1 => 1521920110,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:navbar.tpl' => 1,
  ),
),false)) {
function content_5ab6a86fc69c52_01224150 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="stylesheet/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet/extra.css">
<link rel="apple-touch-icon" sizes="57x57" href="./images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="./images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="./images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="./images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="./images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="./images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="./images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="./images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="./images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="./images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="./images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="./images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="./images/favicon-16x16.png">
<link rel="manifest" href="./images/manifest.json">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="./images/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">
<?php echo '<script'; ?>
 src="./javascript/jquery-3.2.1.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="./javascript/popper.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="./javascript/bootstrap.min.js"><?php echo '</script'; ?>
>
<title>SmartDoko</title>
</head>
<body>
	<?php $_smarty_tpl->_subTemplateRender('file:navbar.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_assignInScope('fullPathToTemplate', "./templates/".((string)$_smarty_tpl->tpl_vars['page']->value).".tpl");?> <?php if (file_exists($_smarty_tpl->tpl_vars['fullPathToTemplate']->value)) {?> <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['page']->value).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?> <?php } else { ?>
	<div class="container">
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">Fehler beim Seitenaufruf!</h4>
			<p>Die gewählte Seite "<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
" ist nicht vorhanden.</p>
			<hr>
			<p class="mb-0"><?php echo $_smarty_tpl->tpl_vars['fullPathToTemplate']->value;?>
</p>
		</div>
	</div>
	<?php }?>
	<footer class="footer">
		<div class="container">
			<span class="text-muted">&copy; 2018 Lars Bleckwenn<span class="float-right"><a href="https://github.com/lbleckwenn/SmartDoko" target="_blank">SmartDoko</a></span>
			</span>
		</div>
	</footer>
</body>
</html><?php }
}

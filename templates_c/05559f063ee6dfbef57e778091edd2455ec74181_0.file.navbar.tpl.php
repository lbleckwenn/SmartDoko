<?php
/* Smarty version 3.1.32-dev-38, created on 2018-03-24 21:43:10
  from 'E:\xampp\htdocs\SmartDoko\templates\navbar.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-38',
  'unifunc' => 'content_5ab6b85ee690e2_14026087',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '05559f063ee6dfbef57e778091edd2455ec74181' => 
    array (
      0 => 'E:\\xampp\\htdocs\\SmartDoko\\templates\\navbar.tpl',
      1 => 1521924189,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ab6b85ee690e2_14026087 (Smarty_Internal_Template $_smarty_tpl) {
?><header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
		<div class="container">
			<a class="navbar-brand" href="index.php">
				<img src="./images/logo_small.png" width="30" height="30" class="d-inline-block align-top" alt=""> SmartDoko
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<?php if ($_smarty_tpl->tpl_vars['login']->value) {?>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item <?php if ($_smarty_tpl->tpl_vars['page']->value == "newRound") {?>active<?php }?>"><a class="nav-link" href="index.php?page=newRound">Neue Runde</a></li>
					<li class="nav-item <?php if ($_smarty_tpl->tpl_vars['page']->value == "player") {?>active<?php }?>"><a class="nav-link" href="index.php?page=player">Mitspieler</a></li>
					<li class="nav-item <?php if ($_smarty_tpl->tpl_vars['page']->value == "statistics") {?>active<?php }?>"><a class="nav-link" href="index.php?page=statistics">Statistiken</a></li>
				</ul>
				<ul class="navbar-nav my-2 my-lg-0">
					<li class="nav-item dropdown <?php if ($_smarty_tpl->tpl_vars['page']->value == "settings") {?>active<?php }?>"><a class="nav-link dropdown-toggle" href="index.php?page=config" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Profil</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" href="index.php?page=settings">Einstellungen</a>
							<a class="dropdown-item" href="index.php?page=logout">Abmelden</a>
						</div></li>
				</ul>
				<?php } else { ?>
				<form class="form-inline" action="index.php?page=login" method="post">
					<input class="form-control col-sm-3 mr-sm-2" placeholder="E-Mail" name="email" type="email" required> <input class="form-control col-sm-3 mr-sm-2" placeholder="Passwort" name="passwort" type="password" value=""
						required>
					</td>
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Anmelden</button>
				</form>
			</div>
			<?php }?>
		</div>
	</nav>
</header>
<?php }
}

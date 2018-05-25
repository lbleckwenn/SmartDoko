<?php
/**
 * This file is part of the "SmartDoko" package.
 * Copyright (C) 2018 Lars Bleckwenn <lars.bleckwenn@web.de>
 *
 * "SmartDoko" is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "SmartDoko" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
session_start ();
require_once ('include/config.inc.php');
require_once ('include/functions.inc.php');
require_once ('include/dbFunctions.inc.php');
require_once ('include/login.inc.php');
require_once ('include/Smarty.class.php');
$mitNachnamen = getConfig ( 'mitNachnamen' );
$f = new formreload ();

$success = $error = false;

$smarty = new Smarty ();
$smarty->caching = false;
$smarty->debugging = false;
$page = GetParam ( 'page', 'G', 'splashscreen' );
$pages = array (
		'splashscreen',
		'register',
		'login',
		'passwortvergessen',
		'passwortzuruecksetzen',
		'logout',
		'statistics',
		'settings',
		'player',
		'round',
		'user' 
);
if (! in_array ( $page, $pages )) {
	$page = 'splashscreen';
}
if (is_checked_in () && $page == 'splashscreen') {
	$page = 'overview';
}
if ($page != 'splashscreen') {
	include ("./include/$page.php");
}
$smarty->assign ( 'login', is_checked_in () );
$smarty->assign ( 'page', $page );
$smarty->assign ( 'success', $success );
$smarty->assign ( 'error', $error );
$smarty->assign ( 'token', $f->get_formtoken () );
$smarty->display ( 'index.tpl' );

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

/**
 * This  part of the program code is originally from php-einfach.de
 * and was modified for SmartDoko by me.
 */

$success = $error = false;
if (isset ( $_GET ['send'] )) {
	if (! isset ( $_POST ['email'] ) || empty ( $_POST ['email'] )) {
		$error = "Bitte eine E-Mail-Adresse eintragen.";
	} else {
		$statement = $pdo->prepare ( "SELECT * FROM users WHERE email = :email" );
		$result = $statement->execute ( array (
				'email' => $_POST ['email'] 
		) );
		$user = $statement->fetch ();
		
		if ($user === false) {
			$error = "Kein Benutzer gefunden.";
		} else {
			
			$passwortcode = random_string ();
			$statement = $pdo->prepare ( "UPDATE users SET passwortcode = :passwortcode, passwortcode_time = NOW() WHERE id = :userid" );
			$result = $statement->execute ( array (
					'passwortcode' => sha1 ( $passwortcode ),
					'userid' => $user ['id'] 
			) );
			
			$empfaenger = $user ['email'];
			$betreff = "Neues Passwort für dein SmartDoko-Benutzerkonto";
			$adminFullName = getConfig('adminFullName');
			$adminEmail = getConfig('adminEmail');
			$adminName = getConfig('adminName');
			$from = "From: $adminFullName <$adminEmail>";
			$url_passwortcode = getSiteURL () . 'index.php?page=passwortzuruecksetzen&userid=' . $user ['id'] . '&code=' . $passwortcode;
			$text = 'Hallo ' . $user ['vorname'] . ',
für dein SmartDoko Benutzerkonto wurde ein neues Passwort angefordert. Um ein neues Passwort zu vergeben, rufe innerhalb der nächsten 24 Stunden die folgende Website auf:
' . $url_passwortcode . '
 
Sollte dir dein Passwort wieder eingefallen sein oder hast du dies nicht angefordert, so bitte ignoriere diese E-Mail.
 
Viele Grüße,
'.$adminName;
			// echo $text;
			mail ( $empfaenger, $betreff, $text, $from );
			$success = true;
		}
	}
}
$smarty->assign ( 'success', $success );
$email_value = "";
if (isset ( $_POST ['email'] )) {
	$email_value = htmlentities ( $_POST ['email'] );
}
$smarty->assign ( 'error', $error );
$smarty->assign ( 'email_value', $email_value );

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
 * This part of the program code is originally from php-einfach.de
 * and was modified for SmartDoko by me.
 */
$error = false;
if (isset ( $_POST ['email'] ) && isset ( $_POST ['passwort'] )) {
	if ($f->easycheck ()) {
		$email = $_POST ['email'];
		$passwort = $_POST ['passwort'];
		
		$statement = $pdo->prepare ( "SELECT * FROM users WHERE email = :email" );
		$result = $statement->execute ( array (
				'email' => $email
		) );
		$user = $statement->fetch ();
		
		// Überprüfung des Passworts
		if ($user !== false && password_verify ( $passwort, $user ['passwort'] )) {
			$_SESSION ['userid'] = $user ['id'];
			
			// Möchte der Nutzer angemeldet beleiben?
			if (isset ( $_POST ['angemeldet_bleiben'] )) {
				$identifier = random_string ();
				$securitytoken = random_string ();
				
				$insert = $pdo->prepare ( "INSERT INTO securitytokens (user_id, identifier, securitytoken) VALUES (:user_id, :identifier, :securitytoken)" );
				$insert->execute ( array (
						'user_id' => $user ['id'],
						'identifier' => $identifier,
						'securitytoken' => sha1 ( $securitytoken )
				) );
				setcookie ( "identifier", $identifier, time () + (3600 * 24 * 365) ); // Valid for 1 year
				setcookie ( "securitytoken", $securitytoken, time () + (3600 * 24 * 365) ); // Valid for 1 year
			}
			
			header ( "location: index.php?page=overview" );
			exit ();
		} else {
			$error = "E-Mail oder Passwort war ungültig<br><br>";
		}
	} else {
		$error = 'Bitte nicht die "Reload"-Funktion des Browsers benutzen';
	}
}

$email_value = "";
if (isset ( $_POST ['email'] )) {
	$email_value = htmlentities ( $_POST ['email'] );
}
$smarty->assign ( 'error', $error );
$smarty->assign ( 'email_value', $email_value );

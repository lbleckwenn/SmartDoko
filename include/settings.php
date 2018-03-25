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

// Überprüfe, dass der User eingeloggt ist
// Der Aufruf von check_user() muss in alle internen Seiten eingebaut sein
$user = check_user ();
if (! $user) {
	$smarty->assign ( 'error', 'Bitte zuerst <a href="login.php">einloggen</a>' );
	exit ();
}

if (isset ( $_GET ['save'] )) {
	$save = $_GET ['save'];

	if ($save == 'personal_data') {
		$vorname = trim ( $_POST ['vorname'] );
		$nachname = trim ( $_POST ['nachname'] );

		if ($vorname == "" || $nachname == "") {
			$error = "Bitte Vor- und Nachname ausfüllen.";
		} else {
			$statement = $pdo->prepare ( "UPDATE users SET vorname = :vorname, nachname = :nachname, updated_at=NOW() WHERE id = :userid" );
			$result = $statement->execute ( array (
					'vorname' => $vorname,
					'nachname' => $nachname,
					'userid' => $user ['id']
			) );

			$success = "Daten erfolgreich gespeichert.";
		}
	} else if ($save == 'email') {
		$passwort = $_POST ['passwort'];
		$email = trim ( $_POST ['email'] );
		$email2 = trim ( $_POST ['email2'] );

		if ($email != $email2) {
			$error = "Die eingegebenen E-Mail-Adressen stimmten nicht überein.";
		} else if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
			$error = "Bitte eine gültige E-Mail-Adresse eingeben.";
		} else if (! password_verify ( $passwort, $user ['passwort'] )) {
			$error = "Bitte korrektes Passwort eingeben.";
		} else {
			$statement = $pdo->prepare ( "UPDATE users SET email = :email WHERE id = :userid" );
			$result = $statement->execute ( array (
					'email' => $email,
					'userid' => $user ['id']
			) );

			$success = "E-Mail-Adresse erfolgreich gespeichert.";
		}
	} else if ($save == 'passwort') {
		$passwortAlt = $_POST ['passwortAlt'];
		$passwortNeu = trim ( $_POST ['passwortNeu'] );
		$passwortNeu2 = trim ( $_POST ['passwortNeu2'] );

		if ($passwortNeu != $passwortNeu2) {
			$error = "Die eingegebenen Passwörter stimmten nicht überein.";
		} else if ($passwortNeu == "") {
			$error = "Das Passwort darf nicht leer sein.";
		} else if (! password_verify ( $passwortAlt, $user ['passwort'] )) {
			$error = "Bitte korrektes Passwort eingeben.";
		} else {
			$passwort_hash = password_hash ( $passwortNeu, PASSWORD_DEFAULT );

			$statement = $pdo->prepare ( "UPDATE users SET passwort = :passwort WHERE id = :userid" );
			$result = $statement->execute ( array (
					'passwort' => $passwort_hash,
					'userid' => $user ['id']
			) );

			$success = "Passwort erfolgreich gespeichert.";
		}
	}
}

$smarty->assign ( 'user', check_user () );
$smarty->assign ( 'success', $success );
$smarty->assign ( 'error', $error );


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
$error = $success = $msg = false;
if (! isset($_GET['userid']) || ! isset($_GET['code'])) {
    $error = "Leider wurde beim Aufruf dieser Website kein Code zum Zurücksetzen deines Passworts übermittelt";
}
$userid = $_GET['userid'];
$code = $_GET['code'];

// Abfrage des Nutzers
$statement = $pdo->prepare("SELECT * FROM users WHERE id = :userid");
$result = $statement->execute(array(
    'userid' => $userid
));
$user = $statement->fetch();

// Überprüfe dass ein Nutzer gefunden wurde und dieser auch ein Passwortcode hat
if ($user === null || $user['passwortcode'] === null) {
    $error = "Der Benutzer wurde nicht gefunden oder hat kein neues Passwort angefordert.";
}

if ($user['passwortcode_time'] === null || strtotime($user['passwortcode_time']) < (time() - 24 * 3600)) {
    $error = "Dein Code ist leider abgelaufen. Bitte benutze die Passwort vergessen Funktion erneut.";
}

// Überprüfe den Passwortcode
if (sha1($code) != $user['passwortcode']) {
    $error = "Der übergebene Code war ungültig. Stell sicher, dass du den genauen Link in der URL aufgerufen hast. Solltest du mehrmals die Passwort-vergessen Funktion genutzt haben, so ruf den Link in der neuesten E-Mail auf.";
}

// Der Code war korrekt, der Nutzer darf ein neues Passwort eingeben

if (isset($_GET['send'])) {
    $passwort = $_POST['passwort'];
    $passwort2 = $_POST['passwort2'];

    if ($passwort != $passwort2) {
        $msg = "Bitte identische Passwörter eingeben";
    } else { // Speichere neues Passwort und lösche den Code
        $passworthash = password_hash($passwort, PASSWORD_DEFAULT);
        $statement = $pdo->prepare("UPDATE users SET passwort = :passworthash, passwortcode = NULL, passwortcode_time = NULL WHERE id = :userid");
        $result = $statement->execute(array(
            'passworthash' => $passworthash,
            'userid' => $userid
        ));

        if ($result) {
            $success = true;
        }
    }
}
$smarty->assign('success', $success);
$smarty->assign('error', $error);
$smarty->assign('msg', $msg);
$smarty->assign('userid', htmlentities($userid));
$smarty->assign('code', htmlentities($code));
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
$success = $error = false;

if (isset($_GET['register'])) {
    $error = false;
    $vorname = trim($_POST['vorname']);
    $nachname = trim($_POST['nachname']);
    $email = trim($_POST['email']);
    $passwort = $_POST['passwort'];
    $passwort2 = $_POST['passwort2'];

    if (empty($vorname) || empty($nachname) || empty($email)) {
        $error .= 'Bitte alle Felder ausfüllen<br>';
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
    }
    if (strlen($passwort) == 0) {
        $error .= 'Bitte ein Passwort angeben<br>';
    }
    if ($passwort != $passwort2) {
        $error .= 'Die Passwörter müssen übereinstimmen<br>';
    }

    // Überprüfe, dass die E-Mail-Adresse noch nicht registriert wurde
    if (! $error) {
        $statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array(
            'email' => $email
        ));
        $user = $statement->fetch();

        if ($user !== false) {
            $error .= 'Unter der eingegebenen E-Mail-Adresse ist bereits ein Benutzerkonto registriert worden.<br>';
        }
    }

    // Keine Fehler, wir können den Nutzer registrieren
    if (! $error) {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO users (email, passwort, vorname, nachname) VALUES (:email, :passwort, :vorname, :nachname)");
        $result1 = $statement->execute(array(
            'email' => $email,
            'passwort' => $passwort_hash,
            'vorname' => $vorname,
            'nachname' => $nachname
        ));
        $user_id = $pdo->lastInsertId();
        $statement = $pdo->prepare("INSERT INTO players (vorname, nachname, user_id) VALUES (:vorname, :nachname, :user_id)");
        $result2 = $statement->execute(array(
            'vorname' => $vorname,
            'nachname' => $nachname,
            'user_id' => $user_id
        ));
        $player_id = $pdo->lastInsertId();
        $statement = $pdo->prepare("INSERT INTO user_player (user_id, player_id) VALUES (:user_id, :player_id)");
        $result3 = $statement->execute(array(
            'user_id' => $user_id,
            'player_id' => $player_id
        ));
        if ($result1 && $result2 && $result3) {
            $success = true;
        } else {
            $error .= 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
        }
    }
}
$smarty->assign('success', $success);
$smarty->assign('error', $error);

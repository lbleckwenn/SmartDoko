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
$success = $error = false;
$user = check_user();
if (! $user) {
    $smarty->assign('error', 'Bitte zuerst <a href="login.php">einloggen</a>');
    exit();
}

if (isset($_GET['newPlayer'])) {
    $vorname = trim(GetParam('vorname'));
    $nachname = trim(GetParam('nachname'));
    $email = trim(GetParam('email'));
    if (empty($vorname) || empty($nachname)) {
        $error = 'Bitte alle Felder ausfüllen.';
    }
    if (! empty($email) && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
    }
    if (! $error) {
        $statement = $pdo->prepare("INSERT INTO players (vorname, nachname, email) VALUES (:vorname, :nachname, :email)");
        $result2 = $statement->execute(array(
            'vorname' => $vorname,
            'nachname' => $nachname,
            'email' => $email
        ));
        $player_id = $pdo->lastInsertId();
        $statement = $pdo->prepare("INSERT INTO user_player (user_id, player_id) VALUES (:user_id, :player_id)");
        $result3 = $statement->execute(array(
            'user_id' => $user['id'],
            'player_id' => $player_id
        ));
        if ($result2 && $result3) {
            $success = "$vorname $nachname wurde erfolgreich hinzugefügt.";
        } else {
            $error = "$vorname $nachname konnte nicht hinzugefügt werden.";
        }
    }
}

if (isset($_GET['editPlayer'])) {
    $player_id = trim(GetParam('player_id'));
    $vorname = trim(GetParam('vorname'));
    $nachname = trim(GetParam('nachname'));
    $email = trim(GetParam('email'));
    if (empty($vorname) || empty($nachname)) {
        $error = 'Bitte alle Felder ausfüllen.';
    }
    if (! empty($email) && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error .= 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
    }
    if (! $error) {
        $statement = $pdo->prepare("SELECT * FROM players WHERE id = ?");
        $result = $statement->execute(array(
            $player_id
        ));
        $playerAlt = $statement->fetch();
        $statement = $pdo->prepare("SELECT * FROM user_player WHERE user_id = ? AND player_id = ?");
        $statement->execute(array(
            $user['id'],
            $player_id
        ));
        if ($statement->rowCount() > 0) {
            $statement = $pdo->prepare("UPDATE players SET vorname = :vorname_neu, nachname = :nachname_neu, email = :email_neu WHERE id = :player_id");
            $result = $statement->execute(array(
                'player_id' => $player_id,
                'vorname_neu' => $vorname,
                'nachname_neu' => $nachname,
                'email_neu' => $email
            ));
            if ($result) {
                $success = "$vorname $nachname wurde erfolgreich geändert.";
            } else {
                extract($playerAlt);
                $error = "$vorname $nachname konnte nicht geändert werden.";
            }
        } else {
            extract($playerAlt);
            $error = "$vorname $nachname konnte nicht geändert werden.";
        }
    }
}

if (isset($_GET['deletePlayer'])) {
    $player_id = trim($_POST['player_id']);
    $statement = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $result = $statement->execute(array(
        $player_id
    ));
    if ($result) {
        extract($statement->fetch());
        $statement = $pdo->prepare("DELETE FROM user_player WHERE user_id = ? AND player_id = ?");
        $statement->execute(array(
            $user['id'],
            $player_id
        ));
        if ($statement->rowCount() > 0) {
            $statement = $pdo->prepare("DELETE FROM players WHERE id = ?");
            $result = $statement->execute(array(
                $player_id
            ));
            if ($result) {
                $success = "$vorname $nachname wurde gelöscht.";
            }
        } else {
            $error = "$vorname $nachname konnte nicht gelöscht werden.";
        }
    }
}

// $statement = $pdo->prepare("SELECT players.* FROM user_player, players WHERE user_player.user_id = ? AND user_player.player_id = players.id");
$statement = $pdo->prepare("SELECT DISTINCT players.*, (SELECT count(*) FROM round_player WHERE round_player.player_id = players.id) AS games FROM user_player, players WHERE user_player.user_id = ? AND user_player.player_id = players.id");
$result = $statement->execute(array(
    $user['id']
)); // SELECT COUNT(*) AS games FROM round_player WHERE player_id = 1
$players = $statement->fetchall(PDO::FETCH_ASSOC);
$smarty->assign('players', $players);

$smarty->assign('user', $user);
$smarty->assign('success', $success);
$smarty->assign('error', $error);

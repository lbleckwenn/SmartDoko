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
$user = check_user();
if (! $user) {
    $error = 'Bitte zuerst <a href="login.php">einloggen</a>';
    return;
}

/*
 * ********************************************************************************
 * Spieler laden
 */
$statement = $pdo->prepare("SELECT *, CONCAT(vorname, ' ', nachname) as komplett, (SELECT count(*) FROM round_player WHERE round_player.player_id = players.id) AS runden FROM `players` ORDER BY nachname ASC, vorname ASC");
$statement->execute();
$alleSpieler = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
foreach ($alleSpieler as $spielerId => $spieler) {
    if ($spieler['runden'] == 0) {
        // Spieler ohne Beteiligung nicht berücksichtigen
        unset($alleSpieler[$spielerId]);
        continue;
    }
    $alleSpieler[$spielerId]['spiele'] = 0;
    $alleSpieler[$spielerId]['ansagen'] = 0;
    $alleSpieler[$spielerId]['ansagen_spiel'] = 0;
    $alleSpieler[$spielerId]['re'] = 0;
    $alleSpieler[$spielerId]['re_gew_abs'] = 0;
    $alleSpieler[$spielerId]['re_gew_proz'] = 0;
    $alleSpieler[$spielerId]['kontra'] = 0;
    $alleSpieler[$spielerId]['kontra_gew_abs'] = 0;
    $alleSpieler[$spielerId]['kontra_gew_proz'] = 0;

    $sortName[] = $spieler['vorname'];
}
/*
 * ********************************************************************************
 * Spiele laden
 */
$statement = $pdo->prepare("SELECT * FROM games");
$statement->execute();
$spiele = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

/*
 * ********************************************************************************
 * Spieldaten laden
 */
$statement = $pdo->prepare("SELECT * FROM game_data");
$statement->execute();
$spieleSpielerParteiPunkte = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

/*
 * ********************************************************************************
 * Spiel- und Spielerdaten zusammenführen
 */
foreach ($spieleSpielerParteiPunkte as $spieleSpielerParteiPunkte) {
    $spielId = $spieleSpielerParteiPunkte['game_id'];
    if ($spiele[$spielId]['isRunning']) {
        // laufende Spiele nicht mit einbeziehen
        continue;
    }
    $spielerId = $spieleSpielerParteiPunkte['player_id'];
    $alleSpieler[$spielerId]['spiele'] ++;
}

/*
 * ********************************************************************************
 * Erspielte Sonderpunkte
 *
 */

$extraPoints = array();
$statement = $pdo->prepare("SELECT player_data.player_id, games.game_typ, player_data.ansage, games.gewinner FROM games, player_data, user_player WHERE player_data.ansage IS NOT NULL AND games.id = player_data.game_id AND user_player.player_id = player_data.player_id AND user_player.user_id = ?");
$result = $statement->execute(array(
    $_SESSION['userid']
));
if ($statement->rowCount() > 0) {
    while ($row = $statement->fetch()) {
        if ($row['game_typ'] == 1) {
            $spielerId = $row['player_id'];
            $ansage = $row['ansage'];
            $gewinner = $row['gewinner'];
            $alleSpieler[$spielerId]['ansagen'] ++;
            $alleSpieler[$spielerId]['ansagen_spiel'] = $alleSpieler[$spielerId]['ansagen'] / $alleSpieler[$spielerId]['spiele'] * 100;
            $alleSpieler[$spielerId][$ansage] ++;
            if ($ansage == $gewinner) {
                $alleSpieler[$spielerId][$ansage . "_gew_abs"] ++;
            }
            $alleSpieler[$spielerId][$ansage . "_gew_proz"] = $alleSpieler[$spielerId][$ansage . "_gew_abs"] / $alleSpieler[$spielerId][$ansage] * 100;
        }
    }
}
/*
 * ********************************************************************************
 * Daten an Template übergeben
 */
array_multisort($sortName, SORT_ASC, $alleSpieler);
$smarty->assign('ansagen', $alleSpieler);

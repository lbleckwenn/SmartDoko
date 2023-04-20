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
    $alleSpieler[$spielerId]['fuchs_gefangen'] = 0;
    $alleSpieler[$spielerId]['fuchs_verloren'] = 0;
    $alleSpieler[$spielerId]['karlchen_gewonnen'] = 0;
    $alleSpieler[$spielerId]['karlchen_gefangen'] = 0;
    $alleSpieler[$spielerId]['karlchen_verloren'] = 0;
    $alleSpieler[$spielerId]['doppelkopf'] = 0;

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
$statement = $pdo->prepare("SELECT player_data.* FROM player_data, user_player WHERE (fuchs_gefangen OR karlchen_gewonnen OR karlchen_gefangen OR doppelkopf) AND user_player.player_id = player_data.player_id AND user_player.user_id = ?");
$result = $statement->execute(array(
    $_SESSION['userid']
));
if ($statement->rowCount() > 0) {
    error_reporting(E_ALL & ~ E_NOTICE);
    while ($row = $statement->fetch()) {
        if ($row['fuchs_gefangen']) {
            $alleSpieler[$row['player_id']]['fuchs_gefangen'] ++;
            $alleSpieler[$row['fuchs_gefangen']]['fuchs_verloren'] ++;
        }
        if ($row['karlchen_gewonnen']) {
            $alleSpieler[$row['player_id']]['karlchen_gewonnen'] ++;
        }
        if ($row['karlchen_gefangen']) {
            $alleSpieler[$row['player_id']]['karlchen_gefangen'] ++;
            $alleSpieler[$row['karlchen_gefangen']]['karlchen_verloren'] ++;
        }
        if ($row['doppelkopf']) {
            $alleSpieler[$row['player_id']]['doppelkopf'] ++;
        }
    }
    error_reporting(E_ALL);
}

foreach ($alleSpieler as $spielerId => $spieler) {
    $spiele = max($alleSpieler[$spielerId]['spiele'], 1);
    $alleSpieler[$spielerId]['fuchs_gefangen'] = $alleSpieler[$spielerId]['fuchs_gefangen'] / $spiele * 100;
    $alleSpieler[$spielerId]['fuchs_verloren'] = $alleSpieler[$spielerId]['fuchs_verloren'] / $spiele * 100;
    $alleSpieler[$spielerId]['karlchen_gewonnen'] = $alleSpieler[$spielerId]['karlchen_gewonnen'] / $spiele * 100;
    $alleSpieler[$spielerId]['karlchen_gefangen'] = $alleSpieler[$spielerId]['karlchen_gefangen'] / $spiele * 100;
    $alleSpieler[$spielerId]['karlchen_verloren'] = $alleSpieler[$spielerId]['karlchen_verloren'] / $spiele * 100;
    $alleSpieler[$spielerId]['doppelkopf'] = $alleSpieler[$spielerId]['doppelkopf'] / $spiele * 100;
}

/*
 * ********************************************************************************
 * Daten an Template übergeben
 */
array_multisort($sortName, SORT_ASC, $alleSpieler);
$smarty->assign('sonderpunkte', $alleSpieler);

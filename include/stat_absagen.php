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
    $alleSpieler[$spielerId]['absagen'] = 0;
    $alleSpieler[$spielerId]['absagenp'] = 0;
    $alleSpieler[$spielerId]['keine90'] = 0;
    $alleSpieler[$spielerId]['keine90p'] = 0;
    $alleSpieler[$spielerId]['keine90e'] = 0;
    $alleSpieler[$spielerId]['keine90ep'] = 0;
    $alleSpieler[$spielerId]['keine60'] = 0;
    $alleSpieler[$spielerId]['keine60p'] = 0;
    $alleSpieler[$spielerId]['keine60e'] = 0;
    $alleSpieler[$spielerId]['keine60ep'] = 0;
    $alleSpieler[$spielerId]['keine30'] = 0;
    $alleSpieler[$spielerId]['keine30p'] = 0;
    $alleSpieler[$spielerId]['keine30e'] = 0;
    $alleSpieler[$spielerId]['keine30ep'] = 0;
    $alleSpieler[$spielerId]['schwarz'] = 0;
    $alleSpieler[$spielerId]['schwarzp'] = 0;
    $alleSpieler[$spielerId]['schwarze'] = 0;
    $alleSpieler[$spielerId]['schwarzep'] = 0;

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
$statement = $pdo->prepare("SELECT pd.round_id, pd.game_id, pd.player_id, gd.partei, pd.absage, g.gewinner FROM player_data pd LEFT JOIN games g ON g.id = pd.game_id LEFT JOIN game_data gd ON gd.player_id = pd.player_id AND gd.game_id = pd.game_id WHERE pd.absage IS NOT NULL");
$result = $statement->execute(array(
    //$_SESSION['userid']
));
if ($statement->rowCount() > 0) {
    while ($row = $statement->fetch()) {
        $spielerId = $row['player_id'];
        $absage = str_replace(' ', '', $row['absage']);
        $partei = $row['partei'];
        $gewinner = $row['gewinner'];
        $alleSpieler[$spielerId]['absagen'] ++;
        $alleSpieler[$spielerId]['absagenp'] = $alleSpieler[$spielerId]['absagen'] / $alleSpieler[$spielerId]['spiele'] * 100;
        $alleSpieler[$spielerId][$absage] ++;
        $alleSpieler[$spielerId][$absage . "p"] = $alleSpieler[$spielerId][$absage] / $alleSpieler[$spielerId]['spiele'] * 100;
        if ($partei == $gewinner) {
            $alleSpieler[$spielerId][$absage . "e"] ++;
        }
        $alleSpieler[$spielerId][$absage . "ep"] = $alleSpieler[$spielerId][$absage . "e"] / $alleSpieler[$spielerId][$absage] * 100;
    }
}
/*
 * ********************************************************************************
 * Daten an Template übergeben
 */
array_multisort($sortName, SORT_ASC, $alleSpieler);
$smarty->assign('ansagen', $alleSpieler);

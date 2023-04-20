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
$statement = $pdo->prepare("SELECT *, CONCAT(vorname, ' ', nachname) as komplett, (SELECT rounds.date FROM rounds WHERE players.vorname = rounds.location ORDER BY rounds.date DESC LIMIT 1) AS lausrichtung, (SELECT rounds.date FROM rounds, round_player WHERE round_player.player_id = players.id AND rounds.id = round_player.round_id ORDER BY rounds.date DESC LIMIT 1) AS lteilnahme, (SELECT count(*) FROM round_player WHERE round_player.player_id = players.id) AS teilnahmen, (SELECT count(*) FROM rounds WHERE rounds.location = players.vorname) AS ausrichter FROM `players` ORDER BY nachname ASC, vorname ASC");
$statement->execute();
$alleSpieler = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
foreach ($alleSpieler as $spielerId => $spieler) {
    if ($spieler['teilnahmen'] == 0) {
        // Spieler ohne Beteiligung nicht berücksichtigen
        unset($alleSpieler[$spielerId]);
        continue;
    }
    $alleSpieler[$spielerId]['ausrichter_teilnahmen'] = $spieler['ausrichter'] / $spieler['teilnahmen'] * 100;

    $sortName[] = $spieler['vorname'];
    $sortAusrichter[] = $alleSpieler[$spielerId]['ausrichter_teilnahmen'];
}

$statement = $pdo->prepare("SELECT (SELECT date FROM rounds ORDER BY date ASC LIMIT 1) AS ersteRunde, (SELECT location FROM rounds ORDER BY date ASC LIMIT 1) AS ersterAusrichter, (SELECT date FROM rounds ORDER BY date DESC LIMIT 1) AS letzteRunde, (SELECT location FROM rounds ORDER BY date DESC LIMIT 1) AS letzterAusrichter, COUNT(*) AS runden FROM rounds LIMIT 1");
$statement->execute();
$statistik = $statement->fetchAll(PDO::FETCH_ASSOC);
$statistik = $statistik[0];
$origin = date_create($statistik['ersteRunde']);
$target = date_create($statistik['letzteRunde']);
$statistik['interval'] = date_diff($origin, $target);
$statistik['aufzeichnung'] = $statistik['interval']->format("%y Jahren und %m Monaten");
$statistik['tage'] = $statistik['interval']->format('%a') + 0;
$statistik['wochen'] = intval($statistik['tage'] / 7 / $statistik['runden']);
/*
 * ********************************************************************************
 * Daten an Template übergeben
 */
array_multisort($sortAusrichter, SORT_DESC, $alleSpieler);
$smarty->assign('ausrichter', $alleSpieler);
$smarty->assign('statistik', $statistik);

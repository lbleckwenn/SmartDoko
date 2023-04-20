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
    $alleSpieler[$spielerId]['punktePlusMinus'] = 0;
    $alleSpieler[$spielerId]['punkteSumme'] = 0;
    $alleSpieler[$spielerId]['siege'] = 0;
    $alleSpieler[$spielerId]['spiele'] = 0;
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
    $spielerPunkte = $spieleSpielerParteiPunkte['punkte'];
    $alleSpieler[$spielerId]['punktePlusMinus'] += $spielerPunkte;
    $alleSpieler[$spielerId]['punkteSumme'] += ($spielerPunkte > 0 ? $spielerPunkte : 0);
    $alleSpieler[$spielerId]['siege'] += ($spieleSpielerParteiPunkte['partei'] == $spiele[$spielId]['gewinner'] ? 1 : 0);
    $alleSpieler[$spielerId]['spiele'] ++;
}

/*
 * ********************************************************************************
 * Durchnittliche Punkte ermittlen und Spieler nach Punkten sortieren
 */
$sortPunkte = array();
$sortFeld = ($summenPunkteSystem ? 'schnittSpielePunkteSumme' : 'schnittSpielePunktePlusMinus');
foreach ($alleSpieler as $spielerId => $spieler) {
    $alleSpieler[$spielerId]['schnittSpielePunktePlusMinus'] = $alleSpieler[$spielerId]['punktePlusMinus'] / max($alleSpieler[$spielerId]['spiele'], 1);
    $alleSpieler[$spielerId]['schnittSpielePunkteSumme'] = $alleSpieler[$spielerId]['punkteSumme'] / max($alleSpieler[$spielerId]['spiele'], 1);
    $alleSpieler[$spielerId]['schnittSiegeSpiele'] = $alleSpieler[$spielerId]['siege'] / max($alleSpieler[$spielerId]['spiele'], 1) * 100;
    $sortPunkte[] = $alleSpieler[$spielerId][$sortFeld];
}
array_multisort($sortPunkte, SORT_DESC, $alleSpieler);

/*
 * ********************************************************************************
 * Daten an Template übergeben
 */

$smarty->assign('rangliste', $alleSpieler);

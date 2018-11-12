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
$user = check_user ();
if (! $user) {
	$error = 'Bitte zuerst <a href="login.php">einloggen</a>';
	return;
}

/*
 * ********************************************************************************
 * Runden laden
 */
$statement = $pdo->prepare ( "SELECT * FROM rounds" );
$statement->execute ();
$runden = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );
$rundeInit = array (
		'punktePlusMinus' => 0,
		'punkteSumme' => 0,
		'siege' => 0,
		'spiele' => 0 
);

/*
 * ********************************************************************************
 * Spieler der Runden laden
 */
$statement = $pdo->prepare ( "SELECT * FROM round_player" );
$statement->execute ();
$spielerRunden = array ();
while ( $row = $statement->fetch ( PDO::FETCH_ASSOC ) ) {
	$spielerRunden [$row ['round_id']] [] = $row ['player_id'];
}

/*
 * ********************************************************************************
 * Spieler laden
 */
$statement = $pdo->prepare ( "SELECT *, CONCAT(vorname, ' ', nachname) as komplett, (SELECT count(*) FROM round_player WHERE round_player.player_id = players.id) AS runden FROM `players` ORDER BY nachname ASC, vorname ASC" );
$statement->execute ();
$alleSpieler = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );
$besteRunde = array ();
foreach ( $alleSpieler as $spielerId => $spieler ) {
	if ($spieler ['runden'] == 0) {
		// Spieler ohne Beteiligung nicht berücksichtigen
		unset ( $alleSpieler [$spielerId] );
		continue;
	}
	foreach ( $spielerRunden as $rundeId => $spielerRunde ) {
		if (in_array ( $spielerId, $spielerRunde )) {
			$besteRunde [$spielerId . $rundeId] = $spieler;
			$besteRunde [$spielerId . $rundeId] ['datum'] = strtotime ( $runden [$rundeId] ['date'] );
			$besteRunde [$spielerId . $rundeId] += $rundeInit;
		}
	}
}

/*
 * ********************************************************************************
 * Spiele laden
 */
$statement = $pdo->prepare ( "SELECT * FROM games" );
$statement->execute ();
$spiele = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );

/*
 * ********************************************************************************
 * Spieldaten laden
 */
$statement = $pdo->prepare ( "SELECT * FROM game_data" );
$statement->execute ();
$spieleSpielerParteiPunkte = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );

/*
 * ********************************************************************************
 * Spiel- und Spielerdaten zusammenführen
 */
foreach ( $spieleSpielerParteiPunkte as $spieleSpielerParteiPunkte ) {
	$rundeId = $spieleSpielerParteiPunkte ['round_id'];
	$spielId = $spieleSpielerParteiPunkte ['game_id'];
	if ($spiele [$spielId] ['isRunning']) {
		// laufende Spiele nicht mit einbeziehen
		continue;
	}
	$spielerId = $spieleSpielerParteiPunkte ['player_id'];
	$spielerPunkte = $spieleSpielerParteiPunkte ['punkte'];
	$besteRunde [$spielerId . $rundeId] ['punktePlusMinus'] += $spielerPunkte;
	$besteRunde [$spielerId . $rundeId] ['punkteSumme'] += ($spielerPunkte > 0 ? $spielerPunkte : 0);
	$besteRunde [$spielerId . $rundeId] ['siege'] += ($spieleSpielerParteiPunkte ['partei'] == $spiele [$spielId] ['gewinner'] ? 1 : 0);
	$besteRunde [$spielerId . $rundeId] ['spiele'] ++;
}

/*
 * ********************************************************************************
 * Durchnittliche Punkte ermittlen und Spieler nach Punkten sortieren
 */
$sortPunkte = array ();
$sortFeld = ($summenPunkteSystem ? 'schnittSpielePunkteSumme' : 'schnittSpielePunktePlusMinus');
foreach ( $besteRunde as $spielerRundeId => $rundenDaten ) {
	$besteRunde [$spielerRundeId] ['schnittSpielePunktePlusMinus'] = $besteRunde [$spielerRundeId] ['punktePlusMinus'] / $besteRunde [$spielerRundeId] ['spiele'];
	$besteRunde [$spielerRundeId] ['schnittSpielePunkteSumme'] = $besteRunde [$spielerRundeId] ['punkteSumme'] / $besteRunde [$spielerRundeId] ['spiele'];
	$besteRunde [$spielerRundeId] ['schnittSiegeSpiele'] = $besteRunde [$spielerRundeId] ['siege'] / $besteRunde [$spielerRundeId] ['spiele'] * 100;
	$sortPunkte [] = $besteRunde [$spielerRundeId] [$sortFeld];
}
array_multisort ( $sortPunkte, SORT_DESC, $besteRunde );

/*
 * ********************************************************************************
 * Daten an Template übergeben
 */
$smarty->assign ( 'rangliste', $besteRunde );

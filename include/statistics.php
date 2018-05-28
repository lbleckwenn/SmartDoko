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

$summenPunkteSystem = true;

/*
 * ********************************************************************************
 * Spieler laden
 */
$statement = $pdo->prepare ( "SELECT players.* FROM players, user_player WHERE user_player.player_id = players.id AND user_player.user_id = ?" );
$result = $statement->execute ( array (
		$_SESSION ['userid'] 
) );
$players = array ();
while ( $row = $statement->fetch () ) {
	$players [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
}
$smarty->assign ( 'players', $players );

/*
 * ********************************************************************************
 * Durchschnittliche Punkte
 * SELECT rounds.* FROM rounds, round_player WHERE round_player.player_id = 1 AND rounds.id = round_player.round_id
 */
if (0) {
	// Nur Spiele mit eigener Beteiligung
	$statement = $pdo->prepare ( "" );
	$result = $statement->execute ( array (
			getUserPlayerID () 
	) );
} else {
	$statement = $pdo->prepare ( "SELECT game_data.* FROM game_data, players, user_player WHERE user_player.player_id = game_data.player_id AND user_player.user_id = ? AND game_data.player_id = players.id" );
	$result = $statement->execute ( array (
			$_SESSION ['userid'] 
	) );
}
$averagePoints = $gamesOverall = array ();
if ($statement->rowCount () > 0) {
	while ( $row = $statement->fetch () ) {
		$gamesOverall [$row ['game_id']] = true;
		$playerId = $row ['player_id'];
		$playerName = $players [$playerId];
		$gamePoints = $row ['punkte'];
		if ($summenPunkteSystem && $gamePoints < 0) {
			$gamePoints = 0;
		}
		if (isset ( $averagePoints [$playerId] )) {
			$averagePoints [$playerId] ['games'] ++;
			$averagePoints [$playerId] ['points'] += $gamePoints;
			$averagePoints [$playerId] ['average'] = $averagePoints [$playerId] ['points'] / $averagePoints [$playerId] ['games'];
		} else {
			$averagePoints [$playerId] = array (
					'games' => 1,
					'playerName' => $playerName,
					'points' => $gamePoints,
					'average' => $gamePoints 
			);
			$sortName [] = $playerName;
		}
	}
	array_multisort ( $sortName, SORT_ASC, $averagePoints );
}
$smarty->assign ( 'averagePoints', $averagePoints );
$smarty->assign ( 'gamesOverall', sizeof ( $gamesOverall ) );

/*
 * ********************************************************************************
 * Häufigkeit der angesagten Spiele
 *
 */

/*
 * ********************************************************************************
 * Erspielte Sonderpunkte
 *
 */

$extraPoints = array ();
$statement = $pdo->prepare ( "SELECT player_data.* FROM player_data, user_player WHERE (fuchs_gefangen OR karlchen_gewonnen OR karlchen_gefangen OR doppelkopf) AND user_player.player_id = player_data.player_id AND user_player.user_id = ?" );
$result = $statement->execute ( array (
		$_SESSION ['userid'] 
) );
if ($statement->rowCount () > 0) {
	error_reporting ( E_ALL & ~ E_NOTICE );
	while ( $row = $statement->fetch () ) {
		if ($row ['fuchs_gefangen']) {
			$extraPoints [$row ['player_id']] ['fuchs_gefangen'] ++;
			$extraPoints [$row ['fuchs_gefangen']] ['fuchs_verloren'] ++;
		}
		if ($row ['karlchen_gewonnen']) {
			$extraPoints [$row ['player_id']] ['karlchen_gewonnen'] ++;
		}
		if ($row ['karlchen_gefangen']) {
			$extraPoints [$row ['player_id']] ['karlchen_gefangen'] ++;
			$extraPoints [$row ['karlchen_gefangen']] ['karlchen_verloren'] ++;
		}
		if ($row ['doppelkopf']) {
			$extraPoints [$row ['player_id']] ['doppelkopf'] ++;
		}
		$nameSort[] = $players [$row ['player_id']];
	}
	error_reporting ( E_ALL );
	
}
$smarty->assign ( 'extraPoints', $extraPoints );
/*
 * ********************************************************************************
 * Historie über alle Doppelkopfrunden
 *
 */

$statement = $pdo->prepare ( "SELECT * FROM rounds WHERE user_id = ? ORDER BY date DESC" );
$statement->execute ( array (
		$user ['id'] 
) );
$runden = array ();
while ( $row = $statement->fetch () ) {
	$runden [$row ['id']] = array (
			'date' => $row ['date'],
			'location' => $row ['location'],
			'is_running' => $row ['is_running'],
			'player' => array () 
	);
}

$statement1 = $pdo->prepare ( "SELECT * FROM round_player WHERE round_id = ?" );
$statement2 = $pdo->prepare ( "SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0" );
foreach ( $runden as $runde_id => $runde ) {
	$statement1->execute ( array (
			$runde_id 
	) );
	$players = $statement1->fetchAll ( PDO::FETCH_ASSOC );
	foreach ( $players as $player ) {
		$statement2->execute ( array (
				$runde_id,
				$player ['player_id'] 
		) );
		$row = $statement2->fetch ();
		$runden [$runde_id] ['player'] [$player ['player_id']] = $row ['sum(punkte)'];
		;
	}
}
$smarty->assign ( 'runden', $runden );

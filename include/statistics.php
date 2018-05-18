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

$statement = $pdo->prepare ( "SELECT * FROM players " );
$result = $statement->execute ();
$players = array ();
while ( $row = $statement->fetch () ) {
	$players [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
}
$smarty->assign ( 'players', $players );
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

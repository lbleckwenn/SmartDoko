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
	$smarty->assign ( 'error', 'Bitte zuerst <a href="login.php">einloggen</a>' );
	$page = 'splashscreen';
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
			'date' => strtotime ( $row ['date'] ),
			'games' => $row ['games'],
			'location' => $row ['location'],
			'is_running' => $row ['is_running'],
			'player' => array ()
	);
}

$statement1 = $pdo->prepare ( "SELECT * FROM round_player WHERE round_id = ?" );
$statement21 = $pdo->prepare ( "SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0" );
$statement22 = $pdo->prepare ( "SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ?" );
$statement3 = $pdo->prepare ( "SELECT count(*) from games, game_data WHERE games.round_id = ? and game_data.player_id = ? and game_data.partei = games.gewinner and games.id = game_data.game_id" );
foreach ( $runden as $runde_id => $runde ) {
	$statement1->execute ( array (
			$runde_id
	) );
	$players = $statement1->fetchAll ( PDO::FETCH_ASSOC );
	$sort = array ();
	foreach ( $players as $player ) {
		$statement21->execute ( array (
				$runde_id,
				$player ['player_id']
		) );
		$row = $statement21->fetch ();
		$punkte_se = $row ['sum(punkte)'];
		$statement22->execute ( array (
				$runde_id,
				$player ['player_id']
		) );
		$row = $statement22->fetch ();
		$punkte_pm = $row ['sum(punkte)'];
		$sort [] = $punkte_se;
		$statement3->execute ( array (
				$runde_id,
				$player ['player_id']
		) );
		$row = $statement3->fetch ();
		$siege = $row ['count(*)'];
		$runden [$runde_id] ['player'] [] = array (
				'player_id' => $player ['player_id'],
				'punkte_se' => $punkte_se,
				'punkte_pm' => $punkte_pm,
				'anz_siege' => $siege
		);
	}
	array_multisort ( $sort, SORT_DESC, $runden [$runde_id] ['player'] );
}
$smarty->assign ( 'runden', $runden );

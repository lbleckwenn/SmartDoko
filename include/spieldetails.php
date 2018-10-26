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

/*
 * @todo Script abhärten. Die per GET übergebene Variable wird noch nicht überprüft
 */
$game_id = GetParam ( 'spielid', 'G' );

// Spieltyp ermitteln
$statement = $pdo->prepare ( "SELECT game_types.* FROM game_types, player_data WHERE game_types.id = player_data.game_typ AND player_data.game_id = ?" );
$statement->execute ( array (
		$game_id
) );
$isSolo = $statement->fetch () ['isSolo'];

$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.game_id = ? AND game_data.player_id = players.id" );
$result = $statement->execute ( array (
		$game_id
) );
$players_game = $statement->fetchall (); // PDO::FETCH_ASSOC
$parteien = array ();
$re = 0;
foreach ( $players_game as $player ) {
	$parteien [$player ['id']] = $player ['partei'];
}

// Augen von Re ermitteln
$statement = $pdo->prepare ( "SELECT * FROM games WHERE id = ?" );
$statement->execute ( array (
		$game_id
) );
$reAugen = $statement->fetch () ['re_augen'];

include ('gewinner.php');
// Ansagen ermitteln
$ansagen = array (
		're' => false,
		'kontra' => false
);
$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE game_id = ? AND ansage = ?" );
foreach ( $ansagen as $ansage => $value ) {
	$statement->execute ( array (
			$game_id,
			$ansage
	) );
	if ($statement->rowCount () > 0) {
		$ansagen [$ansage] = true;
	}
}
// Absagen ermitteln
$absagen = array (/*
're' => null,
'kontra' => null */
);
// AND game_data.partei = ?
$statement = $pdo->prepare ( "SELECT player_data.* FROM player_data, game_data WHERE game_data.game_id = ? AND player_data.game_id = ? AND game_data.player_id = player_data.player_id AND player_data.absage != ''" );
$statement->execute ( array (
		$game_id,
		$game_id
) );
if ($statement->rowCount () > 0) {
	while ( $row = $statement->fetch () ) {
		$absageWert = getWertAbsage ( $row ['absage'] );
		if (! isset ( $absagen [$parteien [$row ['player_id']]] ) || $absageWert < $absagen [$parteien [$row ['player_id']]]) {
			$absagen [$parteien [$row ['player_id']]] = $absageWert;
		}
	}
}
// Sonderpunkte
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
// AND game_data.partei = ?
$statement = $pdo->prepare ( "SELECT player_data.* FROM player_data, game_data WHERE game_data.game_id = ? AND player_data.game_id = game_data.game_id AND game_data.player_id = player_data.player_id AND (player_data.fuchs_gefangen > 0 OR player_data.karlchen_gewonnen > 0 OR player_data.karlchen_gefangen > 0 OR player_data.doppelkopf > 0)" );
$statement->execute ( array (
		$game_id
) );
if ($statement->rowCount () > 0) {
	while ( $row = $statement->fetch () ) {
		if ($row ['fuchs_gefangen'] > 0) {
			$sonderpunkte [$parteien [$row ['player_id']]] [] = "Fuchs gefangen";
		}
		if ($row ['karlchen_gefangen'] > 0) {
			$sonderpunkte [$parteien [$row ['player_id']]] [] = "Karlchen gefangen";
		}
		if ($row ['karlchen_gewonnen'] > 0) {
			$sonderpunkte [$parteien [$row ['player_id']]] [] = "Karlchen gewonnen";
		}
		if ($row ['doppelkopf'] > 0) {
			$sonderpunkte [$parteien [$row ['player_id']]] [] = "Doppelkopf";
		}
	}
}
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );

$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, (($isSolo == true) ? 'solo' : 'normal') );
$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.game_id = ? AND game_data.player_id = players.id" );
$result = $statement->execute ( array (
		$game_id
) );
$players_game = array ();
while ( $row = $statement->fetch () ) {
	$players_game [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
}
$smarty->assign ( 'gewinner', ucfirst ( $gewinner ) );
$smarty->assign ( 'parteien', $parteien );
$smarty->assign ( 'log', $punkte );
$smarty->assign ( 'gameType', (($isSolo == true) ? 'solo' : 'normal') );
$smarty->assign ( 'players_game', $players_game );

$smarty->display ( 'spieldetails.tpl' );
// var_dump ( $gewinner, $punkte );
exit ();

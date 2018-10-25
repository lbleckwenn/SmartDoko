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
$round_id = GetParam ( 'round_id', 'G' );

$statement = $pdo->prepare ( "SELECT * FROM rounds WHERE id = ? " );
$result = $statement->execute ( array (
		$round_id
) );
$aktuellesSpiel = $statement->fetch () ['games'];
$smarty->assign ( 'aktuellesSpiel', $aktuellesSpiel );
$statement = $pdo->prepare ( "SELECT players.*, round_player.platz, round_player.spielt, round_player.gibt FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id " );
$result = $statement->execute ( array (
		$round_id
) );
$players_round = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );
foreach ( $players_round as $player_id => $player ) {
	$players_round [$player_id] ['anzeige'] = $player ['vorname'] . (($mitNachnamen) ? ' ' . $player ['nachname'] : '');
}
$smarty->assign ( 'players_round', $players_round );
$punkteliste = $sieger = $sortSieger = array ();
foreach ( $players_round as $player_id => $player ) {
	$punkteliste [0] [$player_id] = array (
			'plusminus' => 0,
			'summe' => 0,
			'siege' => 0
	);
}
$statement = $pdo->prepare ( "SELECT game_data.*, games.re_augen, games.gewinner FROM games, game_data WHERE games.round_id = ? AND games.game_number = ? AND games.id = game_data.game_id " );
for($i = 1; $i <= $aktuellesSpiel; $i ++) {
	$punkteliste [$i] ['re'] = $punkteliste [$i] ['kontra'] = '';
	foreach ( $players_round as $player_id => $player ) {
		$punkteliste [$i] [$player_id] ['plusminus'] = $punkteliste [$i - 1] [$player_id] ['plusminus'];
		$punkteliste [$i] [$player_id] ['summe'] = $punkteliste [$i - 1] [$player_id] ['summe'];
		$punkteliste [$i] [$player_id] ['spielte'] = false;
	}
	$statement->execute ( array (
			$round_id,
			$i
	) );
	while ( $row = $statement->fetch () ) { // PDO::FETCH_ASSOC
		$punkteSpiel = $row ['punkte'];
		$punkteliste [$i] ['sieger'] = $row ['gewinner'];
		$punkteliste [$i] ['augen'] = $row ['re_augen'] . ':' . (240 - $row ['re_augen']);
		if ($punkteSpiel >= 0) {
			$punkteliste [$i] [$row ['player_id']] ['summe'] += $punkteSpiel;
		}
		$punkteliste [$i] [$row ['player_id']] ['plusminus'] += $punkteSpiel;
		$punkteliste [$i] [$row ['player_id']] ['punkte_spiel'] = $punkteSpiel;
		$punkteliste [$i] [$row ['player_id']] ['spielte'] = true;
		if ($row ['gewinner'] == $row ['partei'] && $row ['gewinner'] != '') {
			$punkteliste [$i] [$row ['player_id']] ['sieger'] = true;
		}
	}
	$punkteliste [$i] ['spiel'] = abs ( $punkteSpiel );
	$statement2 = $pdo->prepare ( "SELECT player_data.* FROM player_data, games WHERE games.round_id = ? AND games.game_number = ? AND games.id = player_data.game_id AND player_data.game_typ != ''" );
	$statement2->execute ( array (
			$round_id,
			$i
	) );
	if ($statement2->rowCount () > 0) {
		$row = $statement2->fetch ();
		switch ($row ['game_typ']) {
			case 4 :
				$punkteliste [$i] ['re'] = 'A';
				break;
			case 2 :
				$punkteliste [$i] ['re'] = 'H';
				break;
			default :
				$punkteliste [$i] ['re'] = 'S';
				break;
		}
	}
	$statement3 = $pdo->prepare ( "SELECT player_data.* FROM player_data, games WHERE games.round_id = ? AND games.game_number = ? AND games.id = player_data.game_id AND player_data.ansage != ''" );
	$statement3->execute ( array (
			$round_id,
			$i
	) );
	if ($statement3->rowCount () > 0) {
		while ( $row = $statement3->fetch () ) {
			$punkteliste [$i] [$row ['ansage']] .= substr ( ucfirst ( $row ['ansage'] ), 0, 1 );
		}
	}
	$statement4 = $pdo->prepare ( "SELECT player_data.*, game_data.partei FROM player_data, games, game_data WHERE games.round_id = ? AND games.game_number = ? AND games.id = player_data.game_id AND games.id = game_data.game_id AND player_data.absage != '' AND player_data.player_id = game_data.player_id " );
	$statement4->execute ( array (
			$round_id,
			$i
	) );
	if ($statement4->rowCount () > 0) {
		while ( $row = $statement4->fetch () ) {
			$kurzText = getKurzText ( $row ['absage'] );
			$punkteliste [$i] [$row ['partei']] .= ", $kurzText";
		}
	}
	$statement5 = $pdo->prepare ( "SELECT player_data.*, game_data.partei FROM player_data, games, game_data WHERE games.round_id = ? AND games.game_number = ? AND games.id = player_data.game_id AND games.id = game_data.game_id AND (player_data.fuchs_gefangen > 0 OR player_data.karlchen_gewonnen > 0 OR player_data.karlchen_gefangen > 0 OR player_data.doppelkopf > 0) AND player_data.player_id = game_data.player_id " );
	$statement5->execute ( array (
			$round_id,
			$i
	) );
	if ($statement5->rowCount () > 0) {
		while ( $row = $statement5->fetch () ) {
			if ($row ['partei'] == 're' || $row ['partei'] == 'kontra') {
				if ($row ['fuchs_gefangen'] > 0) {
					$punkteliste [$i] [$row ['partei']] .= ", Fgf";
				}
				if ($row ['karlchen_gefangen'] > 0) {
					$punkteliste [$i] [$row ['partei']] .= ", Kgf";
				}
				if ($row ['karlchen_gewonnen'] > 0) {
					$punkteliste [$i] [$row ['partei']] .= ", Kgw";
				}
				if ($row ['doppelkopf'] > 0) {
					$punkteliste [$i] [$row ['partei']] .= ", Dk";
				}
			}
		}
	}
	$punkteliste [$i] ['re'] = ltrim ( $punkteliste [$i] ['re'], ", " );
	$punkteliste [$i] ['kontra'] = ltrim ( $punkteliste [$i] ['kontra'], ', ' );
}

$smarty->assign ( 'punkteliste', $punkteliste );

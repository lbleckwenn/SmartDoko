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

if (! $f->easycheck ()) {
	// *****************************************************************************
	// *** Reloadcheck
	// *****************************************************************************
	$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
} else {
	// *****************************************************************************
	// *** nach laufender Runde des Benutzers suchen
	// *****************************************************************************
	$statement = $pdo->prepare ( "SELECT * FROM rounds WHERE user_id = ? AND is_running = 1" );
	$statement->execute ( array (
			$user ['id']
	) );
	if ($statement->rowCount () == 0) {
		// *****************************************************************************
		// *** Noch keine offene Runde in der Datenbank gefunden
		// *****************************************************************************
		$step = 1;
		if (isset ( $_GET ['newRound'] )) {
			// *****************************************************************************
			// *** Aber es wurden Daten für eine neue Runde übermittelt
			// *****************************************************************************
			$datum = trim ( GetParam ( 'datum' ) );
			$datum = strtotime ( $datum );
			$ort = trim ( GetParam ( 'ort' ) );
			$anzahlSpieler = trim ( GetParam ( 'anzahl' ) );
			if ($anzahlSpieler < 4) {
				$error = 'Eine Doppelkopfrunde besteht aus mindestens vier Spielern.';
			} else {
				$statement = $pdo->prepare ( "INSERT INTO rounds (user_id, date, location, player, games, is_running) VALUES (?, ?, ?, ?, ?, ?)" );
				$result = $statement->execute ( array (
						$user ['id'],
						date ( 'Y-m-d', $datum ),
						$ort,
						$anzahlSpieler,
						1,
						1
				) );
				$round_id = $pdo->lastInsertId ();
				$statement = $pdo->prepare ( "INSERT INTO games (round_id, game_number, isRunning) VALUES (?, ?, ?)" );
				$result = $statement->execute ( array (
						$round_id,
						1,
						1
				) );
				if ($result) {
					$success = 'Die neue Doppelkopfrunde wurde angelegt.';
					$step = 2;
				} else {
					$error = 'Die neue Doppelkopfrunde konte nicht angelegt werden.';
				}
			}
		} else {
			// *****************************************************************************
			// *** Anzeige der bisherigen Runden mit Ergebnissen
			// *****************************************************************************
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
			$statement2 = $pdo->prepare ( "SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0" );
			$statement3 = $pdo->prepare ( "SELECT count(*) from games, game_data WHERE games.round_id = ? and game_data.player_id = ? and game_data.partei = games.gewinner and games.id = game_data.game_id" );
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
					$statement3->execute ( array (
							$runde_id,
							$player ['player_id']
					) );
					$row = $statement3->fetch ();
					$runden [$runde_id] ['siege'] [$player ['player_id']] = $row ['count(*)'];
				}
			}
			$smarty->assign ( 'runden', $runden );
		}
	} else {
		// *****************************************************************************
		// *** offene Runde gefunden, Daten der Runde laden
		// *****************************************************************************
		$step = 2;
		$round = $statement->fetch ();
		$round_id = $round ['id'];
		$aktuellesSpiel = $round ['games'];
		// laudende Runde löschen
		if (isset ( $_GET ['abbrechen'] )) {
			$statement = $pdo->prepare ( "DELETE FROM games WHERE round_id = ?" );
			$statement->execute ( array (
					$round_id
			) );
			$statement = $pdo->prepare ( "DELETE FROM game_data WHERE round_id = ?" );
			$statement->execute ( array (
					$round_id
			) );
			$statement = $pdo->prepare ( "DELETE FROM player_data WHERE round_id = ?" );
			$statement->execute ( array (
					$round_id
			) );
			$statement = $pdo->prepare ( "DELETE FROM round_player WHERE round_id = ?" );
			$statement->execute ( array (
					$round_id
			) );
			$statement = $pdo->prepare ( "DELETE FROM rounds WHERE id = ?" );
			$statement->execute ( array (
					$round_id
			) );
		}
		// *****************************************************************************
		$anzahlSpieler = $round ['player'];
		$statement = $pdo->prepare ( "SELECT * FROM games WHERE round_id = ? AND game_number = ?" );
		$statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$game = $statement->fetch ();
		$game_id = $game ['id'];
		// *****************************************************************************
		// *** schauen, ob für die Runde schon Spieler festgelegt wurden
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT players.* FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id
		) );
		if ($statement->rowCount () != $anzahlSpieler) {
			// *****************************************************************************
			// *** noch keine Spieler für die Runde festgelegt
			// *****************************************************************************
			if (isset ( $_GET ['selectPlayer'] )) {
				if (GetParam ( 'submit' ) == 'save') {
					$spieler = GetParam ( 'spieler' );
					if (checkIfTwoElementsEqual ( $spieler )) {
						$error = "Die Mitspielerauswahl ist fehlerhaft.";
					}
					if (! $error) {
						$statement1 = $pdo->prepare ( "INSERT INTO round_player (round_id, player_id, platz, spielt, gibt) VALUES (?, ?, ?, ?, ?)" );
						$statement2 = $pdo->prepare ( "INSERT INTO game_data (round_id, game_id, player_id) VALUES (?, ?, ?)" );
						for($i = 0; $i < $round ['player']; $i ++) {
							$mitspieler = array (
									$round_id,
									$spieler [$i],
									$i + 1,
									(($anzahlSpieler - $i) > 4 ? 0 : 1), // Bei mehr als 4 Spielern spielen nur die letzten 4 im ersten Spiel
									($i == 0 ? 1 : 0) // Spieler auf Platz 1 ist der erste Geber
							);
							$statement1->execute ( $mitspieler );
							if ($anzahlSpieler - $i < 5) {
								$mitspieler = array (
										$round_id,
										$game_id,
										$spieler [$i]
								);
								$statement2->execute ( $mitspieler );
							}
						}
						$success = "Mitspielerauswahl gespeichert.";
						$step = 3;
					}
				} else {
					$statement = $pdo->prepare ( "DELETE FROM games WHERE round_id = ?" );
					$statement->execute ( array (
							$round_id
					) );
					$statement = $pdo->prepare ( "DELETE FROM game_data WHERE round_id = ?" );
					$statement->execute ( array (
							$round_id
					) );
					$statement = $pdo->prepare ( "DELETE FROM round_player WHERE round_id = ?" );
					$statement->execute ( array (
							$round_id
					) );
					$statement = $pdo->prepare ( "DELETE FROM rounds WHERE round_id = ?" );
					$statement->execute ( array (
							$round_id
					) );
					$step = 1;
				}
			}
		} else {
			// *****************************************************************************
			// *** Spieler für Runde wurden bereits festgelegt
			// *****************************************************************************
			$step = 3;
			$smarty->assign ( 'aktuellesSpiel', $aktuellesSpiel );
			// *****************************************************************************
			// *** Spieler des Spiels ermitteln
			// *****************************************************************************
			$statement = $pdo->prepare ( "SELECT players.*, CONCAT(players.vorname, ' ', players.nachname) as komplett, round_player.spielt, round_player.gibt FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id " );
			$result = $statement->execute ( array (
					$round_id
			) );
			$alleSpieler = $statement->fetchAll ( PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC );
			foreach ( $alleSpieler as $spielerId => $spieler ) {
				if ($spieler ['gibt']) {
					$geber = ($mitNachnamen ? $spieler ['komplett'] : $spieler ['vorname']);
				}
				if (! $spieler ['spielt']) {
					unset ( $alleSpieler [$spielerId] );
				}
			}
			$smarty->assign ( 'alleSpieler', $alleSpieler );
			$smarty->assign ( 'geber', $geber );
		}
	}

	$smarty->assign ( 'step', $step );
}

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

// *****************************************************************************
// *** nach laufender Runde des Benutzers suchen
// *****************************************************************************

$statement = $pdo->prepare ( "SELECT * FROM rounds WHERE user_id = ? AND is_running = 1" );
$statement->execute ( array (
		$user ['id']
) );
if ($statement->rowCount () == 0) {

	// *****************************************************************************
	// *** keine offene Runde gefunden
	// *****************************************************************************

	$step = 1;
	if (isset ( $_GET ['newRound'] )) {
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
			$statement = $pdo->prepare ( "INSERT INTO games (round_id, game_number) VALUES (?, ?)" );
			$result = $statement->execute ( array (
					$round_id,
					1
			) );
			if ($result) {
				$success = 'Die neue Doppelkopfrunde wurde angelegt.';
				$step = 2;
			} else {
				$error = 'Die neue Doppelkopfrunde konte nicht angelegt werden.';
			}
		}
	}
} else {

	// *****************************************************************************
	// *** offene Runde gefunden, Daten der Runde laden
	// *****************************************************************************

	$step = 2;
	$round = $statement->fetch ();
	$round_id = $round ['id'];
	$aktuellesSpiel = $round ['games'];
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
				$aussetzer = GetParam ( 'aussetzen', 'P', array () );
				if (checkIfTwoElementsEqual ( $spieler )) {
					$error = "Die Mitspielerauswahl ist fehlerhaft.";
				}
				if ($anzahlSpieler - sizeof ( $aussetzer ) != 4) {
					$error = (($anzahlSpieler == 5) ? "Es muss ein Spieler im ersten Spiel aussetzen." : sprintf ( "Es müssen %d Spieler im ersten Spiel aussetzen.", $anzahlSpieler - 4 ));
				}
				if (! $error) {
					$statement1 = $pdo->prepare ( "INSERT INTO round_player (round_id, player_id, 1st_game) VALUES (?, ?, ?)" );
					$statement2 = $pdo->prepare ( "INSERT INTO game_data (round_id, game_id, game_number, player_id) VALUES (?, ?, ?, ?)" );
					for($i = 0; $i < $round ['player']; $i ++) {
						$mitspieler = array (
								$round_id,
								$spieler [$i],
								(isset ( $aussetzer [$i] ) ? 0 : 1)
						);
						$statement1->execute ( $mitspieler );
						if (! isset ( $aussetzer [$i] )) {
							$mitspieler = array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler [$i]
							);
							$statement2->execute ( $mitspieler );
						}
					}
					$success = "Mitspielerauswahl gespeichert.";
					$step = 3;
				}
			} else {
				/**
				 * Nicht vergessen auch die Spielerdaten zu löschen wenn die Tabellenstruktur dafür ausgearbeitet wurde
				 */
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

		$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.round_id = ? AND game_data.game_id = ? AND game_data.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id,
				$game_id
		) );
		$players_game = $statement->fetchall (); // PDO::FETCH_ASSOC
		if (isset ( $_GET ['reservation'] )) {

			// *****************************************************************************
			// *** Vorbehalte speichern
			// *****************************************************************************

			if (! isset ( $_GET ['delete'] )) {
				$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND (vorbehalt != '' OR ansage != '')" );
				$statement->execute ( array (
						$round_id,
						$aktuellesSpiel
				) );
				if ($statement->rowCount () > 0) {
					$error = "Es kann nur ein Vorbehalt pro Spiel ausgewählt werden. Der Vorbehalt muss vor einer Ansage angemeldet werden.";
				} else {
					$spieler = GetParam ( 'spieler', 'P', null );
					$vorbehalt = GetParam ( 'vorbehalt', 'P', null );
					$partner = GetParam ( 'partner', 'P', null );
					if ($spieler == null || $vorbehalt == null) {
						$error = "Es wurden keine Daten übermittelt.";
					} else {
						if ($vorbehalt != 'solo') {
							if ($spieler == $partner) {
								$error = "Bei einer Hochzeit oder Trumpfabgabe müssen zwei verschiedene Spieler ausgewählt werden.";
							} else {
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, vorbehalt) VALUES (?, ?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
										$aktuellesSpiel,
										$spieler,
										$vorbehalt
								) );
								$statement->execute ( array (
										$round_id,
										$game_id,
										$aktuellesSpiel,
										$partner,
										$vorbehalt
								) );
							}
						} else {
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, vorbehalt) VALUES (?, ?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler,
									$vorbehalt
							) );
						}
						foreach ( $players_game as $player_game ) {
							if ($player_game ['id'] == $spieler || $player_game ['id'] == $partner) {
								$partei = 're';
							} else {
								$partei = 'kontra';
							}
							$statement = $pdo->prepare ( "UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?" );
							$statement->execute ( array (
									$partei,
									$game_id,
									$player_game ['id']
							) );
						}
					}
				}
			} else {
				// Spielerdaten löschen
				$statement = $pdo->prepare ( "DELETE FROM player_data WHERE round_id = ? AND game_number = ? AND vorbehalt != ''" );
				$statement->execute ( array (
						$round_id,
						$aktuellesSpiel
				) );
				// Spielpartei löschen
				$statement = $pdo->prepare ( "UPDATE game_data SET partei = '' WHERE game_id = ? AND round_id = ?" );
				$statement->execute ( array (
						$game_id,
						$round_id
				) );
			}
		}

		if (isset ( $_GET ['ansage'] )) {

			// *****************************************************************************
			// *** Ansagen speichern
			// *****************************************************************************

			if (! isset ( $_GET ['delete'] )) {
				$spieler = GetParam ( 'spieler', 'P', null );
				$ansage = GetParam ( 'ansage', 'P', null );
				if ($spieler == null || $ansage == null) {
					// Überprüfung der Variabeln
					$error = "Es wurden keine Daten übermittelt.";
				} else {
					// Prüfen ob Re oder Kontra bereits angesagt wurden
					$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND ansage = ?" );
					$statement->execute ( array (
							$round_id,
							$aktuellesSpiel,
							$ansage
					) );
					if ($statement->rowCount () > 0) {
						$error = "Re und Kontra können jeweils nur einmal angesagt werden.";
					}
					// Prüfen ob die Spielpartei zur Ansage passt
					$statement = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_number = ? AND player_id = ?" );
					$result = $statement->execute ( array (
							$round_id,
							$aktuellesSpiel,
							$spieler
					) );
					if ($result) {
						$row = $statement->fetch ();
						if ($row ['partei'] != '' && $row ['partei'] != $ansage) {
							$error = sprintf ( 'Ein Spieler der "%s"-Partei kann nicht "%s" ansagen', ucfirst ( $row ['partei'] ), ucfirst ( $ansage ) );
						}
					} else {
						$error = "Fehler beim Speichern der Ansage.";
					}

					if (! $error) {
						$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, ansage) VALUES (?, ?, ?, ?, ?)" );
						$statement->execute ( array (
								$round_id,
								$game_id,
								$aktuellesSpiel,
								$spieler,
								$ansage
						) );
						$statement = $pdo->prepare ( "UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?" );
						$statement->execute ( array (
								$ansage,
								$game_id,
								$spieler
						) );
					}
				}
			} else {
				$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE id = ?" );
				$statement->execute ( array (
						$_GET ['delete']
				) );
				$row = $statement->fetch ();
				$statement = $pdo->prepare ( "DELETE FROM player_data WHERE id = ?" );
				$statement->execute ( array (
						$_GET ['delete']
				) );
				$statement = $pdo->prepare ( "UPDATE game_data SET partei = '' WHERE game_id = ? AND player_id = ?" );
				$statement->execute ( array (
						$game_id,
						$row ['player_id']
				) );
			}
		}
		if (isset ( $_GET ['absage'] )) {

			// *****************************************************************************
			// *** Absagen speichern
			// *****************************************************************************

			if (! isset ( $_GET ['delete'] )) {
				$spieler = GetParam ( 'spieler', 'P', null );
				$absage = GetParam ( 'absage', 'P', null );
				// Überprüfung der Variabeln
				if ($spieler == null || $absage == null) {
					$error = "Es wurden keine Daten übermittelt.";
				} else {
					// Anzahl der Ansagen abfragen
					$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND ansage != ''" );
					$statement->execute ( array (
							$round_id,
							$aktuellesSpiel
					) );
					if ($statement->rowCount () == 0) {
						// keine Ansage
						$error = 'Vor einer Absage muss zunächst "Re" oder "Kontra" angesagt werden';
					}
					if ($statement->rowCount () == 1) {
						// Re oder Kontra angesagt; Absage der gleichen Partei
						$row = $statement->fetch ();
						$ansage = $row ['ansage'];
						// Partei des absagenden Spielers setzen
						$statement = $pdo->prepare ( "UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?" );
						$statement->execute ( array (
								$ansage,
								$game_id,
								$spieler
						) );
						// Absage speichern
						$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, absage) VALUES (?, ?, ?, ?, ?)" );
						$statement->execute ( array (
								$round_id,
								$game_id,
								$aktuellesSpiel,
								$spieler,
								$absage
						) );
					}
					if ($statement->rowCount () == 2) {
						// Re und Kontra wurden angesagt
						// Partei des absagenden Spielers laden
						$statement = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_id = ? AND player_id = ?" );
						$statement->execute ( array (
								$round_id,
								$game_id,
								$spieler
						) );
						$row = $statement->fetch ();
						$partei = GetParam ( 'partei', 'P', null );
						if (($row ['partei'] != 're' && $row ['partei'] != 'kontra') && $partei == null) {
							// noch keine Partei, dann Fehlermeldung
							$error = 'Die "Re" oder "Kontra" Zugehörigkeit des Spielers ist noch nicht geklärt. Bitte bei Absage mit auswählen.';
						} else {
							if ($row ['partei'] == '') {
								// Partei des absagenden Spielers setzen
								$statement = $pdo->prepare ( "UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?" );
								$statement->execute ( array (
										$partei,
										$game_id,
										$spieler
								) );
							}
							// Absage speichern
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, absage) VALUES (?, ?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler,
									$absage
							) );
						}
					}
				}
			} else {
				// Absagen löschen
				$statement = $pdo->prepare ( "DELETE FROM player_data WHERE id = ?" );
				$statement->execute ( array (
						$_GET ['delete']
				) );
			}
		}
		if (isset ( $_GET ['extraPoint'] )) {

			// *****************************************************************************
			// *** Sonderpunkte speichern
			// *****************************************************************************

			if (! isset ( $_GET ['delete'] )) {
				$spieler = GetParam ( 'spieler' );
				$sonderpunkt = GetParam ( 'sonderpunkt' );
				$verlierer = GetParam ( 'looser', 'P', null );
				switch ($sonderpunkt) {
					case 'doppelkopf' :
						$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, doppelkopf) VALUES (?, ?, ?, ?, ?)" );
						$statement->execute ( array (
								$round_id,
								$game_id,
								$aktuellesSpiel,
								$spieler,
								1
						) );
						break;
					case 'fuchs_gefangen' :
						$statement = $pdo->prepare ( sprintf ( "SELECT * FROM player_data WHERE round_id = %d AND game_number = %d AND fuchs_gefangen != ''", $round_id, $aktuellesSpiel ) );
						$statement->execute ();
						if ($statement->rowCount () == 2) {
							$error = "Mehr als zwei Füchse können nicht gefangen werden.";
						} else {
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, fuchs_gefangen) VALUES (?, ?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler,
									$verlierer
							) );
						}
						break;
					case 'karlchen_gefangen' :
						$statement = $pdo->prepare ( sprintf ( "SELECT * FROM player_data WHERE round_id = %d AND game_number = %d AND karlchen_gefangen != ''", $round_id, $aktuellesSpiel ) );
						$statement->execute ();
						if ($statement->rowCount () == 2) {
							$error = "Mehr als zwei Karlchen können nicht gefangen werden.";
						} else {
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, karlchen_gefangen) VALUES (?, ?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler,
									$verlierer
							) );
						}
						break;
					case 'karlchen_gewonnen' :
						$statement = $pdo->prepare ( sprintf ( "SELECT * FROM player_data WHERE round_id = %d AND game_number = %d AND karlchen_gewonnen != ''", $round_id, $aktuellesSpiel ) );
						$statement->execute ();
						if ($statement->rowCount () == 1) {
							$error = "Nur ein Karlchen kann gewinnen.";
						} else {
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, game_number, player_id, karlchen_gewonnen) VALUES (?, ?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$aktuellesSpiel,
									$spieler,
									1
							) );
						}
						break;
				}
			} else {
				$statement = $pdo->prepare ( "DELETE FROM player_data WHERE id = ?" );
				$statement->execute ( array (
						$_GET ['delete']
				) );
			}
		}

		if (isset ( $_GET ['gameCalculate'] )) {
			// *****************************************************************************
			// *** Spielabrechnung
			// *****************************************************************************
			$reSpieler1 = GetParam ( 'reSpieler1', 'P', '' );
			$reSpieler2 = GetParam ( 'reSpieler2', 'P', '' );
			$reAugen = GetParam ( 'reAugen', 'P', '' );
			$kontraAugen = GetParam ( 'kontraAugen', 'P', '' );
			// Variabeln überprüfen
			if ($reAugen == '' || $kontraAugen == '') {
				$error = "Die Augenzahl der Parteien muss eingegeben werden.";
			}
			// Spieltyp ermitteln
			$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND vorbehalt = 'solo'" );
			$statement->execute ( array (
					$round_id,
					$game_id
			) );
			if ($statement->rowCount () > 0 || $reSpieler2 == 'solo') {
				$gameType = 'solo';
			} else {
				$gameType = 'normal';
			}
			// Variabeln überprüfen
			if ($gameType == 'normal' && ($reSpieler1 == '' || $reSpieler2 == '')) {
				$error = 'Beide Spieler der "Re"-Partei müssen angegeben werden.';
			}
			$parteien = array ();
			$re = 0;
			foreach ( $players_game as $player ) {
				$parteien [$player ['id']] = $player ['partei'];
				if ($player ['partei'] == 're') {
					$re ++;
				}
			}
			if ($gameType == 'solo') {
				if ($re > 1) {
					$error = 'Durch An- oder Absagen gehören 2 Spieler der "Re"-Partei an. Dies kann also kein Solo-Spiel sein.';
				} else {
					foreach ( $parteien as $id => $partei ) {
						if ($reSpieler1 == $id) {
							$parteien [$id] = 're';
						} else {
							$parteien [$id] = 'kontra';
						}
					}
				}
			} elseif ($gameType == 'normal') {
				if (! $error && ($parteien [$reSpieler1] != 're' || $parteien [$reSpieler2] != 're')) {
					$error = 'Die An- oder Absagen der Spieler passen nicht zur "Re"-Partei.';
				} else {
					foreach ( $parteien as $id => $partei ) {
						if ($reSpieler1 == $id || $reSpieler2 == $id) {
							$parteien [$id] = 're';
						} else {
							$parteien [$id] = 'kontra';
						}
					}
				}
			}
			// Gefangene Füchse und Karlchen überprüfen
			$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE game_id = ? AND fuchs_gefangen > 0 OR karlchen_gefangen > 0" );
			$statement->execute ( array (
					$game_id
			) );
			if ($statement->rowCount () > 0) {
				while ( $row = $statement->fetch () ) {
					if ($row ['fuchs_gefangen'] != null && $parteien [$row ['player_id']] == $parteien [$row ['fuchs_gefangen']]) {
						$error = 'Ein Fuchs der eigenen Partei kann nicht gefangen werden.';
					}
					if ($row ['karlchen_gefangen'] != null && $parteien [$row ['player_id']] == $parteien [$row ['karlchen_gefangen']]) {
						$error = 'Ein Karlchen der eigenen Partei kann nicht gefangen werden.';
					}
				}
			}
			// Keine Fehler bei Vorbehalten, Spielerzuordnungen und Sonderpunkten gefunden
			if (! $error) {
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
				$absagen = array (
						're' => null,
						'kontra' => null
				);
				$statement = $pdo->prepare ( "SELECT player_data.* FROM player_data, game_data WHERE game_data.game_id = ? AND game_data.partei = ? AND game_data.player_id = player_data.player_id AND player_data.absage != ''" );
				foreach ( $absagen as $absage => $value ) {
					$statement->execute ( array (
							$game_id,
							$absage
					) );
					if ($statement->rowCount () > 0) {
						$absageWert = getWertAbsage ( $statement->fetch () ['absage'] );
						if ($absageWert < $value || $value == null) {
							$absagen [$absage] = $absageWert;
						}
					}
				}
				// Sonderpunkte
				$sonderpunkte = array (
						're' => array (),
						'kontra' => array ()
				);
				$statement = $pdo->prepare ( "SELECT player_data.* FROM player_data, game_data WHERE game_data.game_id = ? AND game_data.partei = ? AND game_data.player_id = player_data.player_id AND (player_data.fuchs_gefangen > 0 OR player_data.karlchen_gewonnen > 0 OR player_data.karlchen_gefangen > 0 OR player_data.doppelkopf > 0 )" );
				foreach ( $sonderpunkte as $partei => $array ) {
					$statement->execute ( array (
							$game_id,
							$partei
					) );
					if ($statement->rowCount () > 0) {
						while ( $row = $statement->fetch () ) {
							if ($row ['fuchs_gefangen'] > 0) {
								$sonderpunkte [$partei] [] = "Fuchs gefangen";
							}
							if ($row ['karlchen_gefangen'] > 0) {
								$sonderpunkte [$partei] [] = "Karlchen gefangen";
							}
							if ($row ['karlchen_gewonnen'] > 0) {
								$sonderpunkte [$partei] [] = "Karlchen gewonnen";
							}
							if ($row ['doppelkopf'] > 0) {
								$sonderpunkte [$partei] [] = "Doppelkopf";
							}
						}
					}
				}
				$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
				$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $gameType );
				$step = 4;
			}
		}
	}
}
switch ($step) {
	case 1 :
		break;
	case 2 :
		$statement = $pdo->prepare ( "SELECT players.* FROM user_player, players WHERE user_player.user_id = ? AND user_player.player_id = players.id" );
		$result = $statement->execute ( array (
				$user ['id']
		) );
		$players = array ();
		while ( $row = $statement->fetch () ) {
			$players [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
		}
		$smarty->assign ( 'anzahlSpieler', $anzahlSpieler );
		break;
	case 3 :
		$smarty->assign ( 'aktuellesSpiel', $aktuellesSpiel );
		$anAbSagen = array (
				're' => '',
				'kontra' => ''
		);
		// *****************************************************************************
		// *** Spieler der Runde ermitteln
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT players.* FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id
		) );
		$players_round = array ();
		while ( $row = $statement->fetch () ) {
			$players_round [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
		}
		$smarty->assign ( 'players_round', $players_round );

		// *****************************************************************************
		// *** Spieler des Spiels ermitteln
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.round_id = ? AND game_data.game_number = ? AND game_data.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$players_game = $parteien = array ();
		while ( $row = $statement->fetch () ) {
			$players_game [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
			if ($row ['partei'] == '')
				$row ['partei'] = 'unklar';
			$parteien [$row ['partei']] [] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
		}
		$smarty->assign ( 'players_game', $players_game );
		$smarty->assign ( 'parteien', $parteien );
		// *****************************************************************************
		// *** Vorbehalt
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND vorbehalt != ''" );
		$statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$vorbehaltText = '';
		$gameType = 'normal';
		if ($statement->rowCount () > 0) {
			while ( $row = $statement->fetch () ) {
				$vorbehalt = $row ['vorbehalt'];
				if ($vorbehalt == 'solo') {
					$anAbSagen ['re'] .= 'S';
					$vorbehaltText = $players_game [$row ['player_id']] . ' spielt ein Solo.';
				} else {
					$vorbehaltText .= $players_game [$row ['player_id']] . ' und ';
				}
			}
			if ($vorbehalt != 'solo') {
				$anAbSagen ['re'] .= (($vorbehalt == 'armut') ? 'T' : 'H');
				$vorbehaltText = substr ( $vorbehaltText, 0, - 4 ) . 'spielen eine ' . (($vorbehalt == 'armut') ? 'Trumpfabgabe.' : 'Hochzeit.');
			} else {
				$gameType = 'solo';
			}
		}
		$smarty->assign ( 'vorbehalt', $vorbehaltText );
		$smarty->assign ( 'gameType', $gameType );
		// *****************************************************************************
		// *** Ansagen
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND ansage != ''" );
		$statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$ansagen = array ();
		if ($statement->rowCount () > 0) {
			while ( $row = $statement->fetch () ) {
				$anAbSagen [$row ['ansage']] .= substr ( ucfirst ( $row ['ansage'] ), 0, 1 );
				$ansagen [$row ['id']] = sprintf ( '%s hat "%s" gesagt.', $players_game [$row ['player_id']], ucfirst ( $row ['ansage'] ) );
			}
		}
		$smarty->assign ( 'ansagen', $ansagen );
		// *****************************************************************************
		// *** Absagen
		// *****************************************************************************
		$statement1 = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND absage != ''" );
		$statement1->execute ( array (
				$round_id,
				$game_id
		) );
		$absagen = array ();
		if ($statement1->rowCount () > 0) {
			while ( $row = $statement1->fetch () ) {
				$absagen [$row ['id']] = sprintf ( '%s hat "%s" gesagt.', $players_game [$row ['player_id']], $row ['absage'] );
				$kurzText = getKurzText ( $row ['absage'] );
				$statement2 = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_id = ? AND player_id = ?" );
				$statement2->execute ( array (
						$round_id,
						$game_id,
						$row ['player_id']
				) );
				$player = $statement2->fetch ();
				$anAbSagen [$player ['partei']] .= ", $kurzText";
			}
		}
		$smarty->assign ( 'absagen', $absagen );
		// *****************************************************************************
		// *** Sonderpunkte
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_number = ? AND (fuchs_gefangen != '' OR karlchen_gewonnen != '' OR karlchen_gefangen != '' OR doppelkopf != '')" );
		$statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$sonderpunkte = array ();
		if ($statement->rowCount () > 0) {
			while ( $row = $statement->fetch () ) {
				if ($row ['fuchs_gefangen']) {
					$sonderpunkte [$row ['id']] = sprintf ( "%s hat einen Fuchs von %s gefangen.", $players_game [$row ['player_id']], $players_game [$row ['fuchs_gefangen']] );
				}
				if ($row ['karlchen_gefangen']) {
					$sonderpunkte [$row ['id']] = sprintf ( "%s hat ein Karlchen von %s gefangen.", $players_game [$row ['player_id']], $players_game [$row ['karlchen_gefangen']] );
				}
				if ($row ['karlchen_gewonnen']) {
					$sonderpunkte [$row ['id']] = sprintf ( "Karlchen von %s macht den letzten Stich.", $players_game [$row ['player_id']] );
				}
				if ($row ['doppelkopf']) {
					$sonderpunkte [$row ['id']] = sprintf ( "%s hat einen Doppelkopf.", $players_game [$row ['player_id']] );
				}
			}
		}
		$smarty->assign ( 'sonderpunkte', $sonderpunkte );
		// *****************************************************************************
		// *** Re-Partei für Abrechnung
		// *****************************************************************************
		$rePartei = array ();
		$statement = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_id = ? AND partei = 're'" );
		$statement->execute ( array (
				$round_id,
				$game_id
		) );
		if ($statement->rowCount () > 0) {
			while ( $row = $statement->fetch () ) {
				// $rePartei [$row ['player_id']] = $player_game [$row ['vorname']] . (($mitNachnamen) ? ' ' . $player_game [$row ['nachname']] : '');
				$rePartei [] = $row ['player_id'];
			}
		}
		$smarty->assign ( 'rePartei', $rePartei );
		$smarty->assign ( 'anAbSagen', $anAbSagen );
		break;
	case 4 :
		$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.round_id = ? AND game_data.game_number = ? AND game_data.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id,
				$aktuellesSpiel
		) );
		$players_game = array ();
		while ( $row = $statement->fetch () ) {
			$players_game [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
		}
		$smarty->assign ( 'players_game', $players_game );
		$smarty->assign ( 'reSpieler1', $reSpieler1 );
		$smarty->assign ( 'reSpieler2', $reSpieler2 );
		$smarty->assign ( 'parteien', $parteien );
		$smarty->assign ( 'aktuellesSpiel', $aktuellesSpiel );
		$smarty->assign ( 'gewinner', ucfirst ( $gewinner ) );
		$smarty->assign ( 'reAugen', $reAugen );
		$smarty->assign ( 'players_game', $players_game );
		$smarty->assign ( 'kontraAugen', $kontraAugen );
		$smarty->assign ( 'parteien', $parteien );
		$smarty->assign ( 'log', $punkte );
		$smarty->assign ( 'gameType', $gameType );
		break;
}
$smarty->assign ( 'step', $step );
function checkIfTwoElementsEqual($ar) {
	for($i = 0; $i < count ( $ar ); $i ++) {
		for($j = $i + 1; $j < count ( $ar ); $j ++) {
			if ($ar [$i] == $ar [$j]) {
				return true;
			}
		}
	}
	return false;
}
function getKurzText($text) {
	switch ($text) {
		case 'keine 90' :
			return 'k90';
		case 'keine 60' :
			return 'k60';
		case 'keine 30' :
			return 'k30';
		case 'schwarz' :
			return 'sw';
	}
}
function getWertAbsage($text) {
	switch ($text) {
		case 'keine 90' :
			return '90';
		case 'keine 60' :
			return '60';
		case 'keine 30' :
			return '30';
		case 'schwarz' :
			return '0';
	}
}
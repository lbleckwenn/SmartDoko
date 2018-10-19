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
		if ($f->easycheck ()) {
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
		} else {
			$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
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
			if ($f->easycheck ()) {
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
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
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
			if ($f->easycheck ()) {
				
				// *****************************************************************************
				// *** Vorbehalte speichern
				// *****************************************************************************
				
				if (! isset ( $_GET ['delete'] )) {
					$vorbehalt = GetParam ( 'vorbehalt', 'P', null );
					$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE game_id = ? AND (game_typ != '' OR ansage != '')" );
					$statement->execute ( array (
							$game_id 
					) );
					if ($statement->rowCount () > 0 && $vorbehalt != 3) {
						$error = "Es kann nur ein Vorbehalt pro Spiel ausgewählt werden. Der Vorbehalt muss vor einer Ansage angemeldet werden (außer bei einer stillen Hochzeit).";
					} else {
						$spieler = GetParam ( 'spieler', 'P', null );
						$partner = GetParam ( 'partner', 'P', null );
						if ($spieler == null || $vorbehalt == null) {
							$error = "Es wurden keine Daten übermittelt.";
						} else {
							if ($vorbehalt == 2 || $vorbehalt == 4) {
								if ($spieler == $partner) {
									$error = "Bei einer Hochzeit oder Trumpfabgabe müssen zwei verschiedene Spieler ausgewählt werden.";
								} else {
									$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, game_typ, mate_id) VALUES (?, ?, ?, ?, ?)" );
									$statement->execute ( array (
											$round_id,
											$game_id,
											$spieler,
											$vorbehalt,
											$partner 
									) );
								}
							} else {
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, game_typ) VALUES (?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
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
					$statement = $pdo->prepare ( "DELETE FROM player_data WHERE round_id = ? AND game_id = ? AND game_typ != ''" );
					$statement->execute ( array (
							$round_id,
							$game_id 
					) );
					// Spielpartei löschen
					$statement = $pdo->prepare ( "UPDATE game_data SET partei = '' WHERE game_id = ? AND round_id = ?" );
					$statement->execute ( array (
							$game_id,
							$round_id 
					) );
				}
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
		
		if (isset ( $_GET ['ansage'] )) {
			if ($f->easycheck ()) {
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
						$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND ansage = ?" );
						$statement->execute ( array (
								$round_id,
								$game_id,
								$ansage 
						) );
						if ($statement->rowCount () > 0) {
							$error = "Re und Kontra können jeweils nur einmal angesagt werden.";
						}
						// Prüfen ob die Spielpartei zur Ansage passt
						$statement = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_id = ? AND player_id = ?" );
						$result = $statement->execute ( array (
								$round_id,
								$game_id,
								$spieler 
						) );
						$row = $statement->fetch ();
						if ($row ['partei'] != '' && $row ['partei'] != $ansage) {
							// $error = sprintf ( 'Ein Spieler der "%s"-Partei kann nicht "%s" ansagen', ucfirst ( $row ['partei'] ), ucfirst ( $ansage ) );
						}
						if (! $error) {
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, ansage) VALUES (?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
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
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
		if (isset ( $_GET ['absage'] )) {
			if ($f->easycheck ()) {
				
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
						$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND ansage != ''" );
						$statement->execute ( array (
								$round_id,
								$game_id 
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
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, absage) VALUES (?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
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
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, absage) VALUES (?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
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
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
		if (isset ( $_GET ['extraPoint'] )) {
			if ($f->easycheck ()) {
				// *****************************************************************************
				// *** Sonderpunkte speichern
				// *****************************************************************************
				
				if (! isset ( $_GET ['delete'] )) {
					$spieler = GetParam ( 'spieler' );
					$sonderpunkt = GetParam ( 'sonderpunkt' );
					$verlierer = GetParam ( 'looser', 'P', null );
					switch ($sonderpunkt) {
						case 'doppelkopf' :
							$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, doppelkopf) VALUES (?, ?, ?, ?)" );
							$statement->execute ( array (
									$round_id,
									$game_id,
									$spieler,
									1 
							) );
							break;
						case 'fuchs_gefangen' :
							$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND fuchs_gefangen != ''" );
							$statement->execute ( array (
									$round_id,
									$game_id 
							) );
							if ($statement->rowCount () == 2) {
								$error = "Mehr als zwei Füchse können nicht gefangen werden.";
							} else {
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, fuchs_gefangen) VALUES (?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
										$spieler,
										$verlierer 
								) );
							}
							break;
						case 'karlchen_gefangen' :
							$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND karlchen_gefangen != ''" );
							$statement->execute ( array (
									$round_id,
									$game_id 
							) );
							if ($statement->rowCount () == 2) {
								$error = "Mehr als zwei Karlchen können nicht gefangen werden.";
							} else {
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, karlchen_gefangen) VALUES (?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
										$spieler,
										$verlierer 
								) );
							}
							break;
						case 'karlchen_gewonnen' :
							$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND karlchen_gewonnen != ''" );
							$statement->execute ( array (
									$round_id,
									$game_id 
							) );
							if ($statement->rowCount () == 1) {
								$error = "Nur ein Karlchen kann gewinnen.";
							} else {
								$statement = $pdo->prepare ( "INSERT INTO player_data (round_id, game_id, player_id, karlchen_gewonnen) VALUES (?, ?, ?, ?)" );
								$statement->execute ( array (
										$round_id,
										$game_id,
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
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
		
		if (isset ( $_GET ['gameCalculate'] )) {
			if ($f->easycheck ()) {
				// *****************************************************************************
				// *** Spielabrechnung
				// *****************************************************************************
				$reSpieler1 = GetParam ( 'reSpieler1', 'P', '' );
				$reSpieler2 = GetParam ( 'reSpieler2', 'P', '' );
				$reAugen = GetParam ( 'reAugen', 'P', '' );
				$kontraAugen = GetParam ( 'kontraAugen', 'P', '' );
				// Augen überprüfen
				if ($reAugen == '' || $kontraAugen == '') {
					$error = "Die Augenzahl der Parteien muss eingegeben werden.";
				}
				// Spieltyp ermitteln
				// $statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND vorbehalt = 'solo'" );
				$statement = $pdo->prepare ( "SELECT game_types.* FROM game_types, player_data WHERE game_types.id = player_data.game_typ AND player_data.game_id = ?" );
				$statement->execute ( array (
						$game_id 
				) );
				// Solo wenn in DB gespeichert oder bei Abrechnung angegeben.
				$gameData = $statement->fetch ();
				if ($gameData ['isSolo'] || $reSpieler2 == 'solo') { // $statement->rowCount () > 0
					$isSolo = true;
				} else {
					$isSolo = false;
				}
				$gameType = ($gameData ['id'] == null) ? 1 : $gameData ['id'];
				// Beide Spieler der Re-Partei bei Normalspiel vorhanden?
				if ($isSolo == false && ($reSpieler1 == '' || $reSpieler2 == '')) {
					$error = 'Beide Spieler der "Re"-Partei müssen angegeben werden.';
				}
				// Parteien aus Datenbank laden; Re zählen
				$parteien = array ();
				$re = 0;
				foreach ( $players_game as $player ) {
					$parteien [$player ['id']] = $player ['partei'];
					if ($player ['partei'] == 're') {
						$re ++;
					}
				}
				if ($isSolo == true) {
					if ($re > 1) {
						$error = 'Durch An- oder Absagen gehören 2 Spieler der "Re"-Partei an. Dies kann also kein Solo-Spiel sein.';
					} else {
						// Alle Spieler Parteien zuweisen
						foreach ( $parteien as $id => $partei ) {
							if ($reSpieler1 == $id) {
								$parteien [$id] = 're';
							} else {
								$parteien [$id] = 'kontra';
							}
						}
					}
				} elseif ($isSolo == false) {
					// Prüfen ob Re-Spieler lt. Abrechnung durch Kontra An-/Absage aufgefallen sind.
					if (! $error && ($parteien [$reSpieler1] == 'kontra' || $parteien [$reSpieler2] == 'kontra')) {
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
				$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE game_id = ? AND (fuchs_gefangen > 0 OR karlchen_gefangen > 0)" );
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
					
					$save = GetParam ( 'save', 'P', 0 );
					
					// Wenn Abrechnung genemigt wurde Daten speichern und nächstes Spiel
					if ($save) {
						// Spielpunkte speichern
						$statement = $pdo->prepare ( "UPDATE games SET game_typ = ?, gewinner = ?, re_augen = ?, spiel_punkte = ? WHERE id = ?" );
						$statement->execute ( array (
								$gameType,
								$gewinner,
								$reAugen,
								abs ( $punkte ['re'] ),
								$game_id 
						) );
						$statement = $pdo->prepare ( "UPDATE game_data set partei = ?, punkte = ? WHERE game_id = ? AND player_id = ?" );
						foreach ( $parteien as $player_id => $partei ) {
							$statement->execute ( array (
									$partei,
									$punkte [$partei] * (($isSolo == true && $partei == 're') ? 3 : 1),
									$game_id,
									$player_id 
							) );
						}
						// Spieler der Runde für nächstes Spiel laden
						$statement = $pdo->prepare ( "SELECT * FROM round_player WHERE round_id = ?" );
						$statement->execute ( array (
								$round_id 
						) );
						$players_round = $spielt = $gibt = array ();
						while ( $row = $statement->fetch () ) {
							$players_round [] = array (
									'player_id' => $row ['player_id'],
									'spielt' => $row ['spielt'],
									'gibt' => $row ['gibt'] 
							);
							$spielt [] = $row ['spielt'];
							$gibt [] = $row ['gibt'];
						}
						if ($isSolo == false) {
							// Aussetzende Spieler und Geber für nächstes Spiel ermitteln
							// Bei Solo wird in der gleichen Konstellation erneut gespielt
							array_unshift ( $spielt, array_pop ( $spielt ) );
							array_unshift ( $gibt, array_pop ( $gibt ) );
							$statement = $pdo->prepare ( "UPDATE round_player SET spielt = ?, gibt = ? WHERE round_id = ? AND player_id = ?" );
							foreach ( $players_round as $key => $player ) {
								$statement->execute ( array (
										$spielt [$key],
										$gibt [$key],
										$round_id,
										$player ['player_id'] 
								) );
							}
						}
						$aktuellesSpiel ++;
						$statement = $pdo->prepare ( "UPDATE rounds SET games = ? WHERE id = ? " );
						$statement->execute ( array (
								$aktuellesSpiel,
								$round_id 
						) );
						
						$statement = $pdo->prepare ( "INSERT INTO games (round_id, game_number) VALUES (?, ?)" );
						$result = $statement->execute ( array (
								$round_id,
								$aktuellesSpiel 
						) );
						$game_id = $pdo->lastInsertId ();
						
						$statement = $pdo->prepare ( "INSERT INTO game_data (round_id, game_id, player_id) VALUES (?, ?, ?)" );
						foreach ( $players_round as $key => $player ) {
							if ($spielt [$key]) {
								$statement->execute ( array (
										$round_id,
										$game_id,
										$player ['player_id'] 
								) );
							}
						}
						$step = 3;
					} else {
						$step = 4;
					}
				}
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
		// *****************************************************************************
		// *** Doppenkopfrunde beenden
		// *****************************************************************************
		if (isset ( $_GET ['endOfRound'] )) {
			if ($f->easycheck ()) {
				$endOfRound = GetParam ( 'endOfRound', 'P', 0 );
				if ($endOfRound) {
					$statement = $pdo->prepare ( "DELETE FROM game_data WHERE game_id = ?" );
					$statement->execute ( array (
							$game_id 
					) );
					$statement = $pdo->prepare ( "DELETE FROM games WHERE id = ?" );
					$statement->execute ( array (
							$game_id 
					) );
					$statement = $pdo->prepare ( "UPDATE rounds SET games = games -1, is_running = 0 WHERE id = ?" );
					$statement->execute ( array (
							$round_id 
					) );
					header ( "location: index.php?page=statistics" );
					exit ();
				}
			} else {
				$error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
			}
		}
	}
}
$statement = $pdo->prepare ( "SELECT * FROM players " );
$result = $statement->execute ();
$players = array ();
while ( $row = $statement->fetch () ) {
	$players [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
}
$smarty->assign ( 'players', $players );
switch ($step) {
	case 1 :
		$statement = $pdo->prepare ( "SELECT * FROM rounds WHERE user_id = ? ORDER BY date DESC" );
		$statement->execute ( array (
				$user ['id'] 
		) );
		$runden = array ();
		while ( $row = $statement->fetch () ) {
			$runden [$row ['id']] = array (
					'date' => strtotime($row ['date']),
					'games' => $row ['games'],
 					'location' => $row ['location'],
					'is_running' => $row ['is_running'],
					'player' => array () 
			);
		}
		
		$statement1 = $pdo->prepare ( "SELECT * FROM round_player WHERE round_id = ?" );
		$statement2 = $pdo->prepare ( "SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0" );
		$statement3 = $pdo->prepare ( "SELECT count(*) from games, game_data WHERE games.round_id = ? and game_data.player_id = ? and game_data.partei = games.gewinner and games.id = game_data.game_id");
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
		$smarty->assign ( 'players', $players );
		$smarty->assign ( 'anzahlSpieler', $anzahlSpieler );
		break;
	case 3 :
		$smarty->assign ( 'aktuellesSpiel', $aktuellesSpiel );
		// *****************************************************************************
		// *** Spieler der Runde ermitteln
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT players.*, round_player.spielt, round_player.gibt FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id " );
		$result = $statement->execute ( array (
				$round_id 
		) );
		$players_round = $aussetzer = array ();
		while ( $row = $statement->fetch () ) {
			if (! $row ['spielt']) {
				$aussetzer [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
			}
			if ($row ['gibt']) {
				$geber = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
			}
			$players_round [$row ['id']] = $row ['vorname'] . (($mitNachnamen) ? ' ' . $row ['nachname'] : '');
		}
		$smarty->assign ( 'players_round', $players_round );
		$smarty->assign ( 'aussetzer', $aussetzer );
		$smarty->assign ( 'geber', $geber );
		
		// *****************************************************************************
		// *** Spieltypen übergeben
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM `game_types` ORDER BY `game_types`.`id` ASC" );
		$result = $statement->execute ( array () );
		$gameTypes = array ();
		while ( $row = $statement->fetch () ) {
			$id = $row ['id'];
			if ($row ['id'] == 1) {
				$gameTypes [''] = 'bitte auswählen';
			} else {
				$gameTypes [$id] = $row ['text'];
			}
		}
		$smarty->assign ( 'gameTypes', $gameTypes );
		
		// *****************************************************************************
		// *** Spieler des Spiels ermitteln
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.round_id = ? AND game_data.game_id = ? AND game_data.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id,
				$game_id 
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
		// *** Punkteliste
		// *****************************************************************************
		$punkteliste = $sieger = $sortSieger = array ();
		
		foreach ( $players_round as $player_id => $player ) {
			$punkteliste [0] [$player_id] = array (
					'plusminus' => 0,
					'summe' => 0,
					'siege' => 0 
			);
		}
		$statement = $pdo->prepare ( "SELECT game_data.*, games.gewinner FROM games, game_data WHERE games.round_id = ? AND games.game_number = ? AND games.id = game_data.game_id " );
		for($i = 1; $i <= $aktuellesSpiel; $i ++) {
			$punkteliste [$i] ['re'] = $punkteliste [$i] ['kontra'] = '';
			foreach ( $players_round as $player_id => $player ) {
				$punkteliste [$i] [$player_id] ['plusminus'] = $punkteliste [$i - 1] [$player_id] ['plusminus'];
				$punkteliste [$i] [$player_id] ['summe'] = $punkteliste [$i - 1] [$player_id] ['summe'];
				$punkteliste [$i] [$player_id] ['siege'] = $punkteliste [$i - 1] [$player_id] ['siege'];
				$punkteliste [$i] [$player_id] ['spielte'] = false;
			}
			$statement->execute ( array (
					$round_id,
					$i 
			) );
			while ( $row = $statement->fetch () ) { // PDO::FETCH_ASSOC
				$punkteSpiel = $row ['punkte'];
				if ($punkteSpiel >= 0) {
					$punkteliste [$i] [$row ['player_id']] ['summe'] += $punkteSpiel;
				}
				$punkteliste [$i] [$row ['player_id']] ['plusminus'] += $punkteSpiel;
				$punkteliste [$i] [$row ['player_id']] ['punkte_spiel'] = $punkteSpiel;
				$punkteliste [$i] [$row ['player_id']] ['spielte'] = true;
				if ($row ['gewinner'] == $row ['partei'] && $row ['gewinner'] != '') {
					$punkteliste [$i] [$row ['player_id']] ['siege'] ++;
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
		foreach ( $players_round as $player_id => $player ) {
			$sieger [$player_id] = array (
					'name' => $players_round [$player_id],
					'plusminus' => $punkteliste [$aktuellesSpiel] [$player_id] ['plusminus'],
					'summe' => $punkteliste [$aktuellesSpiel] [$player_id] ['summe'],
					'siege' => $punkteliste [$aktuellesSpiel] [$player_id] ['siege'] 
			);
			$sortSieger [] = $punkteliste [$aktuellesSpiel] [$player_id] ['plusminus'];
		}
		array_multisort ( $sortSieger, SORT_DESC, $sieger );
		$smarty->assign ( 'punkteliste', $punkteliste );
		$smarty->assign ( 'sieger', $sieger );
		
		// *****************************************************************************
		// *** Vorbehalt
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND game_typ != ''" );
		$statement->execute ( array (
				$round_id,
				$game_id 
		) );
		$vorbehaltText = '';
		$gameType = 'normal';
		if ($statement->rowCount () > 0) {
			$row = $statement->fetch ();
			$statement = $pdo->prepare ( "SELECT game_types.isSolo FROM game_types, player_data WHERE game_types.id = player_data.game_typ AND player_data.game_id = ?" );
			$statement->execute ( array (
					$game_id 
			) );
			$isSolo = $statement->fetch () ['isSolo'];
			if ($isSolo) {
				$vorbehaltText = $players_game [$row ['player_id']] . ' spielt ein Solo.';
				$gameType = 'solo';
			} else {
				$vorbehaltText .= $players_game [$row ['player_id']] . ' und ' . $players_game [$row ['mate_id']] . ' spielen eine ' . (($row ['game_typ'] == 4) ? 'Trumpfabgabe.' : 'Hochzeit.');
			}
		}
		$smarty->assign ( 'vorbehalt', $vorbehaltText );
		$smarty->assign ( 'gameType', $gameType );
		// *****************************************************************************
		// *** Ansagen
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND ansage != ''" );
		$statement->execute ( array (
				$round_id,
				$game_id 
		) );
		$ansagen = array ();
		if ($statement->rowCount () > 0) {
			while ( $row = $statement->fetch () ) {
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
			}
		}
		$smarty->assign ( 'absagen', $absagen );
		// *****************************************************************************
		// *** Sonderpunkte
		// *****************************************************************************
		$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND (fuchs_gefangen != '' OR karlchen_gewonnen != '' OR karlchen_gefangen != '' OR doppelkopf != '')" );
		$statement->execute ( array (
				$round_id,
				$game_id 
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
		break;
	case 4 :
		$statement = $pdo->prepare ( "SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.round_id = ? AND game_data.game_id = ? AND game_data.player_id = players.id" );
		$result = $statement->execute ( array (
				$round_id,
				$game_id 
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
		$smarty->assign ( 'gameType', (($isSolo == true) ? 'solo' : 'normal') );
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
			return 'schw.';
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
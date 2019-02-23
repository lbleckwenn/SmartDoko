<?php
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

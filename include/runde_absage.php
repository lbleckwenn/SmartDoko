<?php
// Überprüfung der Variabeln
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
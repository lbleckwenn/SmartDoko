<?php
/*
 * Eine "Ansage" wird auch übertragen, wenn eine Absage getätigt wird.
 * Aber sie soll natürlich nur einmal gespeichert werden.
 *
 * Zunächst prüfen ob Re oder Kontra bereits angesagt wurden
 */
$statement = $pdo->prepare ( "SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND ansage = ?" );
$statement->execute ( array (
		$round_id,
		$game_id,
		$ansage 
) );
if ($statement->rowCount () == 0) {
	/*
	 * Re oder Kontra wurden noch nicht angesagt. -> Prüfen ob, die Spielpartei zur Ansage passt.
	 */
	$statement = $pdo->prepare ( "SELECT * FROM game_data WHERE round_id = ? AND game_id = ? AND player_id = ?" );
	$result = $statement->execute ( array (
			$round_id,
			$game_id,
			$spieler 
	) );
	$row = $statement->fetch ();
	if ($row ['partei'] != '' && $row ['partei'] != $ansage) {
		$error = sprintf ( 'Ein Spieler der "%s"-Partei kann nicht "%s" ansagen', ucfirst ( $row ['partei'] ), ucfirst ( $ansage ) );
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

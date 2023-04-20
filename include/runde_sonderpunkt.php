<?php
switch ($sonderpunkt) {
    case 'doppelkopf':
        $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, doppelkopf) VALUES (?, ?, ?, ?)");
        $statement->execute(array(
            $round_id,
            $game_id,
            $spieler,
            1
        ));
        break;
    case 'fuchsgefangen':
        $statement = $pdo->prepare("SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND fuchs_gefangen != ''");
        $statement->execute(array(
            $round_id,
            $game_id
        ));
        if ($statement->rowCount() == 2) {
            $error = "Mehr als zwei Füchse können nicht gefangen werden.";
        } else {
            $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, fuchs_gefangen) VALUES (?, ?, ?, ?)");
            $statement->execute(array(
                $round_id,
                $game_id,
                $spieler,
                $gegner
            ));
        }
        break;
    case 'karlchengefangen':
        $statement = $pdo->prepare("SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND karlchen_gefangen != ''");
        $statement->execute(array(
            $round_id,
            $game_id
        ));
        if ($statement->rowCount() == 2) {
            $error = "Mehr als zwei Karlchen können nicht gefangen werden.";
        } else {
            $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, karlchen_gefangen) VALUES (?, ?, ?, ?)");
            $statement->execute(array(
                $round_id,
                $game_id,
                $spieler,
                $gegner
            ));
        }
        break;
    case 'karlchen':
        $statement = $pdo->prepare("SELECT * FROM player_data WHERE round_id = ? AND game_id = ? AND karlchen_gewonnen != ''");
        $statement->execute(array(
            $round_id,
            $game_id
        ));
        if ($statement->rowCount() == 1) {
            $error = "Nur ein Karlchen kann gewinnen.";
        } else {
            $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, karlchen_gewonnen) VALUES (?, ?, ?, ?)");
            $statement->execute(array(
                $round_id,
                $game_id,
                $spieler,
                1
            ));
        }
        break;
}
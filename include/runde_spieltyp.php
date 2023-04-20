<?php
$statement = $pdo->prepare("SELECT * FROM player_data WHERE game_id = ? AND (game_typ != '' OR ansage != '')");
$statement->execute(array(
    $game_id
));
if ($statement->rowCount() > 0 && $spielTyp != 3) {
    if ($spielTyp != $statement->fetch()['gameType']) {
        $error = "Es kann nur ein Vorbehalt pro Spiel ausgewählt werden. Der Vorbehalt muss vor einer Ansage angemeldet werden (außer bei einer stillen Hochzeit).";
    }
} else {
    $spieler = GetParam('spieler', 'P', null);
    $partner = GetParam('partner', 'P', null);
    if ($spieler == null || $spielTyp == null) {
        $error = "Es wurden keine Daten übermittelt.";
    } else {
        if ($spielTyp == 2 || $spielTyp == 4) {
            if ($spieler == $partner) {
                $error = "Bei einer Hochzeit oder Trumpfabgabe müssen zwei verschiedene Spieler ausgewählt werden.";
            } else {
                $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, game_typ, mate_id) VALUES (?, ?, ?, ?, ?)");
                $statement->execute(array(
                    $round_id,
                    $game_id,
                    $spieler,
                    $spielTyp,
                    $partner
                ));
            }
        } elseif ($spielTyp != 1) {

            $statement = $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, game_typ) VALUES (?, ?, ?, ?)");
            $statement->execute(array(
                $round_id,
                $game_id,
                $spieler,
                $spielTyp
            ));
        }
        $statement = $pdo->prepare("SELECT players.* FROM round_player, players WHERE round_player.round_id = :roundId AND round_player.player_id = players.id AND round_player.spielt = 1");
        $result = $statement->execute(array(
            'roundId' => $round_id
        ));
        $spielerSpiel = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        foreach ($spielerSpiel as $spielerId => $player_game) {
            if ($spielerId == $spieler || $spielerId == $partner) {
                $partei = 're';
            } else {
                $partei = 'kontra';
            }
            $statement = $pdo->prepare("UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?");
            $statement->execute(array(
                $partei,
                $game_id,
                $spielerId
            ));
        }
    }
}

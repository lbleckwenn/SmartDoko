<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST);
} else {
    $gameIdArray = array(
        GetParam('gameId', 'G', 27)
    );
    $statement = $pdo->prepare("SELECT * FROM game_types");
    $result = $statement->execute();
    $game_types = $statement->fetchall(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
    $smarty->assign('gameTypes', $game_types);

    $statement = $pdo->prepare("SELECT players.*, game_data.partei FROM game_data, players WHERE game_data.game_id = ? AND game_data.player_id = players.id  ORDER BY game_data.id");
    $result = $statement->execute($gameIdArray);
    $players_game = $statement->fetchall(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
    $smarty->assign('players', $players_game);

    $statement = $pdo->prepare("SELECT * FROM games WHERE id = ?");
    $result = $statement->execute($gameIdArray);
    $game = $statement->fetchall(PDO::FETCH_ASSOC)[0];
    $smarty->assign('game', $game);

    $statement = $pdo->prepare("SELECT * FROM player_data WHERE game_id = ?");
    $result = $statement->execute($gameIdArray);
    $fgf = $kgf = $dk = 1;
    $gameData = array(
        'gameType' => 1,
        'playerId' => 0,
        'mateId' => 0,
        're' => array(
            'ansage' => 0,
            'keine 90' => 0,
            'keine 60' => 0,
            'keine 30' => 0,
            'schwarz' => 0
        ),
        'kontra' => array(
            'ansage' => 0,
            'keine 90' => 0,
            'keine 60' => 0,
            'keine 30' => 0,
            'schwarz' => 0
        ),
        'fuchs_gefangen' => array(
            1 => array(
                'g' => 0,
                'v' => 0
            ),
            2 => array(
                'g' => 0,
                'v' => 0
            )
        ),
        'karlchen_gefangen' => array(
            1 => array(
                'g' => 0,
                'v' => 0
            ),
            2 => array(
                'g' => 0,
                'v' => 0
            )
        ),
        'karlchen_gewonnen' => 0,
        'doppelkopf' => array(
            0,
            0,
            0,
            0
        )
    );
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if ($game_typ) {
            $gameData['gameType'] = $game_typ;
            $gameData['playerId'] = $player_id;
            if ($game_typ == 2 || $game_typ == 4) {
                $gameData['mateId'] = $mate_id;
            }
        }
        if ($ansage) {
            $gameData[$ansage]['ansage'] = $player_id;
        }
        if ($absage) {
            $gameData[$players_game[$player_id]['partei']][$absage] = $player_id;
        }
        if ($fuchs_gefangen) {
            $gameData['fuchs_gefangen'][$fgf]['g'] = $player_id;
            $gameData['fuchs_gefangen'][$fgf]['v'] = $fuchs_gefangen;
            $fgf ++;
        }
        if ($karlchen_gefangen) {
            $gameData['karlchen_gefangen'][$kgf]['g'] = $player_id;
            $gameData['karlchen_gefangen'][$kgf]['v'] = $karlchen_gefangen;
            $kgf ++;
        }
        if ($karlchen_gewonnen) {
            $gameData['karlchen_gewonnen'] = $player_id;
        }
        if ($doppelkopf) {
            $gameData['doppelkopf'][$dk] = $player_id;
            $dk ++;
        }
    }
    $smarty->assign('gameData', $gameData);
}
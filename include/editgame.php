<?php
$user = check_user();
if (! $user) {
    $smarty->assign('error', 'Bitte zuerst <a href="login.php">einloggen</a>');
    $page = 'splashscreen';
    return;
}
$userId = is_array($user) ? (int) $user ['id'] : 0;

$gameId = 0;
$reAugen = 0;
$round_id = 0;

if ($_SERVER ['REQUEST_METHOD'] === 'POST') {
    if (! $f->easycheck()) {
        $error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
    } else {
        $gameId = (int) GetParam('gameId', 'P', 0);

        if (! $gameId) {
            $error = 'Ungültige Spiel-ID.';
        } else {
            // Spiel laden und Berechtigung prüfen
            $statement = $pdo->prepare("SELECT games.*, rounds.user_id, rounds.id as round_id FROM games JOIN rounds ON rounds.id = games.round_id WHERE games.id = ?");
            $statement->execute(array($gameId));
            $gameRow = $statement->fetch(PDO::FETCH_ASSOC);

            if (! $gameRow || $gameRow ['user_id'] != $userId) {
                $error = 'Spiel nicht gefunden oder keine Berechtigung.';
            } else {
                $round_id = $gameRow ['round_id'];
                $reAugen = (int) GetParam('re_augen', 'P', 0);
                if ($reAugen < 0 || $reAugen > 240) {
                    $error = 'Die Augenzahl muss zwischen 0 und 240 liegen.';
                }
            }
        }

        if (! $error) {
            include ('gewinner.php');

            // Alte Spieldaten löschen
            $pdo->prepare("DELETE FROM player_data WHERE game_id = ?")->execute(array($gameId));

            // --- Spieltyp ermitteln ---
            $isSolo = false;
            $gameType = 1;
            $soloPlayer = 0;

            $hochzeit = (int) GetParam('hochzeit', 'P', 0);
            $hochzeit2 = (int) GetParam('hochzeit2', 'P', 0);
            $armut = (int) GetParam('armut', 'P', 0);
            $armut2 = (int) GetParam('armut2', 'P', 0);
            $spieltyp = (int) GetParam('spieltyp', 'P', 1);

            if ($hochzeit > 0 && $hochzeit2 > 0 && $hochzeit != $hochzeit2) {
                $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, game_typ, mate_id) VALUES (?, ?, ?, 2, ?)")
                    ->execute(array($round_id, $gameId, $hochzeit, $hochzeit2));
                $gameType = 2;
            } elseif ($armut > 0 && $armut2 > 0 && $armut != $armut2) {
                $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, game_typ, mate_id) VALUES (?, ?, ?, 4, ?)")
                    ->execute(array($round_id, $gameId, $armut, $armut2));
                $gameType = 4;
            } elseif ($spieltyp > 1) {
                $stmt = $pdo->prepare("SELECT isSolo FROM game_types WHERE id = ?");
                $stmt->execute(array($spieltyp));
                $gtRow = $stmt->fetch(PDO::FETCH_ASSOC);
                $isSolo = $gtRow && $gtRow ['isSolo'];
                $dieAlten = (array) GetParam('dieAlten', 'P', array());
                $soloPlayer = ! empty($dieAlten) ? (int) $dieAlten [0] : 0;
                if ($soloPlayer > 0) {
                    $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, game_typ) VALUES (?, ?, ?, ?)")
                        ->execute(array($round_id, $gameId, $soloPlayer, $spieltyp));
                }
                $gameType = $spieltyp;
            }

            // --- Parteien der Spieler ermitteln ---
            $dieAlten = (array) GetParam('dieAlten', 'P', array());
            $statement = $pdo->prepare("SELECT player_id FROM game_data WHERE game_id = ?");
            $statement->execute(array($gameId));
            $parteien = array();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $pid = (int) $row ['player_id'];
                if ($isSolo) {
                    $parteien [$pid] = ($pid === $soloPlayer) ? 're' : 'kontra';
                } else {
                    $parteien [$pid] = in_array((string) $pid, $dieAlten) ? 're' : 'kontra';
                }
            }

            // --- Ansagen ---
            $ansagenArr = array('re' => false, 'kontra' => false);
            $rePlayer = (int) GetParam('re', 'P', 0);
            if ($rePlayer > 0) {
                $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, ansage) VALUES (?, ?, ?, 're')")
                    ->execute(array($round_id, $gameId, $rePlayer));
                $ansagenArr ['re'] = true;
            }
            $kontraPlayer = (int) GetParam('kontra', 'P', 0);
            if ($kontraPlayer > 0) {
                $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, ansage) VALUES (?, ?, ?, 'kontra')")
                    ->execute(array($round_id, $gameId, $kontraPlayer));
                $ansagenArr ['kontra'] = true;
            }

            // --- Absagen ---
            $absagenArr = array();
            $absageMap = array('keine 90' => 90, 'keine 60' => 60, 'keine 30' => 30, 'schwarz' => 0);
            foreach (array('re', 'kontra') as $partei) {
                foreach ($absageMap as $absageText => $wert) {
                    $fieldName = $partei . str_replace(' ', '', $absageText);
                    $spielerId = (int) GetParam($fieldName, 'P', 0);
                    if ($spielerId > 0) {
                        $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, absage) VALUES (?, ?, ?, ?)")
                            ->execute(array($round_id, $gameId, $spielerId, $absageText));
                        if (! isset($absagenArr [$partei]) || $wert < $absagenArr [$partei]) {
                            $absagenArr [$partei] = $wert;
                        }
                    }
                }
            }

            // --- Sonderpunkte ---
            $sonderpunkteArr = array('re' => array(), 'kontra' => array());

            for ($i = 1; $i <= 2; $i ++) {
                $g = (int) GetParam("fuchs_gefangen{$i}", 'P', 0);
                $v = (int) GetParam("fuchs_gefangen{$i}2", 'P', 0);
                if ($g > 0 && $v > 0) {
                    $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, fuchs_gefangen) VALUES (?, ?, ?, ?)")
                        ->execute(array($round_id, $gameId, $g, $v));
                    if (isset($parteien [$g])) {
                        $sonderpunkteArr [$parteien [$g]] [] = 'Fuchs gefangen';
                    }
                }
            }
            for ($i = 1; $i <= 2; $i ++) {
                $g = (int) GetParam("karlchen_gefangen{$i}", 'P', 0);
                $v = (int) GetParam("karlchen_gefangen{$i}2", 'P', 0);
                if ($g > 0 && $v > 0) {
                    $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, karlchen_gefangen) VALUES (?, ?, ?, ?)")
                        ->execute(array($round_id, $gameId, $g, $v));
                    if (isset($parteien [$g])) {
                        $sonderpunkteArr [$parteien [$g]] [] = 'Karlchen gefangen';
                    }
                }
            }
            $kg = (int) GetParam('karlchen_gewonnen', 'P', 0);
            if ($kg > 0) {
                $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, karlchen_gewonnen) VALUES (?, ?, ?, 1)")
                    ->execute(array($round_id, $gameId, $kg));
                if (isset($parteien [$kg])) {
                    $sonderpunkteArr [$parteien [$kg]] [] = 'Karlchen gewonnen';
                }
            }
            for ($i = 1; $i <= 2; $i ++) {
                $dk = (int) GetParam("doppelkopf{$i}", 'P', 0);
                if ($dk > 0) {
                    $pdo->prepare("INSERT INTO player_data (round_id, game_id, player_id, doppelkopf) VALUES (?, ?, ?, 1)")
                        ->execute(array($round_id, $gameId, $dk));
                    if (isset($parteien [$dk])) {
                        $sonderpunkteArr [$parteien [$dk]] [] = 'Doppelkopf';
                    }
                }
            }

            // --- game_data Parteien aktualisieren ---
            $stmt = $pdo->prepare("UPDATE game_data SET partei = ? WHERE game_id = ? AND player_id = ?");
            foreach ($parteien as $playerId => $partei) {
                $stmt->execute(array($partei, $gameId, $playerId));
            }

            // --- Gewinner und Punkte berechnen ---
            $gewinner = ermitteleGewinner($reAugen, $ansagenArr, $absagenArr);
            $punkte = zaehlePunkte($reAugen, $ansagenArr, $absagenArr, $sonderpunkteArr, $gewinner, $isSolo ? 'solo' : 'normal');

            // --- games Tabelle aktualisieren ---
            $pdo->prepare("UPDATE games SET game_typ = ?, gewinner = ?, re_augen = ?, spiel_punkte = ? WHERE id = ?")
                ->execute(array($gameType, $gewinner, $reAugen, abs($punkte ['re']), $gameId));

            // --- game_data Punkte aktualisieren ---
            $stmt = $pdo->prepare("UPDATE game_data SET punkte = ? WHERE game_id = ? AND player_id = ?");
            foreach ($parteien as $playerId => $partei) {
                $stmt->execute(array(
                    $punkte [$partei] * (($isSolo && $partei === 're') ? 3 : 1),
                    $gameId,
                    $playerId
                ));
            }

            header('location: index.php?page=round');
            exit();
        }
    }
    // Bei Fehler: gameId aus POST für die Formular-Anzeige
    if (! $gameId) {
        $gameId = (int) GetParam('gameId', 'P', 0);
    }
} else {
    $gameId = (int) GetParam('gameId', 'G', 0);
}

// --- Formular anzeigen (GET oder POST mit Fehler) ---
$gameIdArray = array($gameId);

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
        $gameData ['gameType'] = $game_typ;
        $gameData ['playerId'] = $player_id;
        if ($game_typ == 2 || $game_typ == 4) {
            $gameData ['mateId'] = $mate_id;
        }
    }
    if ($ansage) {
        $gameData [$ansage] ['ansage'] = $player_id;
    }
    if ($absage) {
        $gameData [$players_game [$player_id] ['partei']] [$absage] = $player_id;
    }
    if ($fuchs_gefangen) {
        $gameData ['fuchs_gefangen'] [$fgf] ['g'] = $player_id;
        $gameData ['fuchs_gefangen'] [$fgf] ['v'] = $fuchs_gefangen;
        $fgf ++;
    }
    if ($karlchen_gefangen) {
        $gameData ['karlchen_gefangen'] [$kgf] ['g'] = $player_id;
        $gameData ['karlchen_gefangen'] [$kgf] ['v'] = $karlchen_gefangen;
        $kgf ++;
    }
    if ($karlchen_gewonnen) {
        $gameData ['karlchen_gewonnen'] = $player_id;
    }
    if ($doppelkopf) {
        $gameData ['doppelkopf'] [$dk] = $player_id;
        $dk ++;
    }
}
$smarty->assign('gameData', $gameData);

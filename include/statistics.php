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
$user = check_user();
if (! $user) {
    $error = 'Bitte zuerst <a href="login.php">einloggen</a>';
    return;
}

/*
 * ********************************************************************************
 * Spieler laden
 */
$statement = $pdo->prepare("SELECT *, CONCAT(vorname, ' ', nachname) as komplett, (SELECT count(*) FROM round_player WHERE round_player.player_id = players.id) AS runden FROM `players` ORDER BY nachname ASC, vorname ASC");
$statement->execute();
$alleSpieler = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
foreach ($alleSpieler as $spielerId => $spieler) {
    if ($spieler['runden'] == 0) {
        // Spieler ohne Beteiligung nicht berücksichtigen
        unset($alleSpieler[$spielerId]);
        continue;
    }
    $alleSpieler[$spielerId]['punktePlusMinus'] = 0;
    $alleSpieler[$spielerId]['punkteSumme'] = 0;
    $alleSpieler[$spielerId]['siege'] = 0;
    $alleSpieler[$spielerId]['spiele'] = 0;
}

/*
 * ********************************************************************************
 * Spiele laden
 */

$statement = $pdo->prepare("SELECT * FROM games");
$statement->execute();
$spiele = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

/*
 * ********************************************************************************
 * Spieldaten laden
 */

$statement = $pdo->prepare("SELECT * FROM game_data");
$statement->execute();
$spieleSpielerParteiPunkte = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

/*
 * ********************************************************************************
 * Spiel- und Spielerdaten zusammenführen
 */

foreach ($spieleSpielerParteiPunkte as $spieleSpielerParteiPunkte) {
    $spielId = $spieleSpielerParteiPunkte['game_id'];
    if ($spiele[$spielId]['isRunning']) {
        // laufende Spiele nicht mit einbeziehen
        continue;
    }
    $spielerId = $spieleSpielerParteiPunkte['player_id'];
    $spielerPunkte = $spieleSpielerParteiPunkte['punkte'];
    $alleSpieler[$spielerId]['punktePlusMinus'] += $spielerPunkte;
    $alleSpieler[$spielerId]['punkteSumme'] += ($spielerPunkte > 0 ? $spielerPunkte : 0);
    $alleSpieler[$spielerId]['siege'] += ($spieleSpielerParteiPunkte['partei'] == $spiele[$spielId]['gewinner'] ? 1 : 0);
    $alleSpieler[$spielerId]['spiele'] ++;
}

/*
 * ********************************************************************************
 * Durchnittliche Punkte ermittlen und Spieler nach Punkten sortieren
 */
$sortPunkte = array();
$sortFeld = ($summenPunkteSystem ? 'schnittSpielePunkteSumme' : 'schnittSpielePunktePlusMinus');
foreach ($alleSpieler as $spielerId => $spieler) {
    $alleSpieler[$spielerId]['schnittSpielePunktePlusMinus'] = $alleSpieler[$spielerId]['punktePlusMinus'] / max($alleSpieler[$spielerId]['spiele'], 1);
    $alleSpieler[$spielerId]['schnittSpielePunkteSumme'] = $alleSpieler[$spielerId]['punkteSumme'] / max($alleSpieler[$spielerId]['spiele'], 1);
    $alleSpieler[$spielerId]['schnittSiegeSpiele'] = $alleSpieler[$spielerId]['siege'] / max($alleSpieler[$spielerId]['spiele'], 1) * 100;
    $sortPunkte[] = $alleSpieler[$spielerId][$sortFeld];
}
array_multisort($sortPunkte, SORT_DESC, $alleSpieler);
$smarty->assign('rangliste', $alleSpieler);

/*
 * ********************************************************************************
 * Alter Programmcode
 */

if ($nurRundenMitBeteiligung) {
    $statement = $pdo->prepare("SELECT players.* FROM players, user_player WHERE user_player.player_id = players.id AND user_player.user_id = :userId ORDER BY players.vorname ASC");
} else {
    $statement = $pdo->prepare("SELECT players.* FROM players, user_friend, user_player WHERE user_player.player_id = players.id AND (user_player.user_id = :userId OR (user_player.user_id = user_friend.userId1 AND user_friend.userId2 = :userId)) GROUP BY id ORDER BY players.vorname ASC ");
}

$result = $statement->execute(array(
    'userId' => $_SESSION['userid']
));
$players = array();
while ($row = $statement->fetch()) {
    $players[$row['id']] = $row['vorname'] . (($mitNachnamen) ? ' ' . $row['nachname'] : '');
}
$smarty->assign('players', $players);

/*
 * ********************************************************************************
 * Durchschnittliche Punkte
 * SELECT rounds.* FROM rounds, round_player WHERE round_player.player_id = 1 AND rounds.id = round_player.round_id
 *
 * Eigene Runden und die von Freunden:
 * SELECT rounds.* FROM rounds, user_friend WHERE user_friend.userId2 = 2 AND rounds.user_id = user_friend.userId1 OR rounds.user_id = 2
 *
 * Vorheriger SQL-String
 * SELECT game_data.* FROM game_data, players, user_player WHERE user_player.player_id = game_data.player_id AND user_player.user_id = 1 AND game_data.player_id = players.id
 */
if ($nurRundenMitBeteiligung) {
    // Nur Spiele mit eigener Beteiligung
    $statement = $pdo->prepare("SELECT game_data.*, games.game_typ FROM games, game_data, rounds, round_player WHERE game_data.round_id = rounds.id AND games.id = game_data.game_id AND rounds.id = round_player.round_id AND round_player.player_id = ? ORDER BY date DESC, id ASC ");
    $result = $statement->execute(array(
        $_SESSION['userid']
    ));
} else {
    $statement = $pdo->prepare("SELECT game_data.*, games.game_typ FROM games, game_data, rounds, user_friend WHERE game_data.round_id = rounds.id AND games.id = game_data.game_id AND ( ( rounds.user_id = user_friend.userId1 AND user_friend.userId2 = ? ) OR rounds.user_id = ? ) GROUP BY id ");
    $result = $statement->execute(array(
        $_SESSION['userid'],
        $_SESSION['userid']
    ));
}
$game_data = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

$averagePoints = $gamesOverall = array();
if ($statement->rowCount() > 0) {
    foreach ($game_data as $row) {
        if ($row['game_typ'] == NULL) {
            // Bei laufenden Runden steht der Spieltyp des aktuellen Spiels noch nicht in der Datenbank
            continue;
        }
        $gamesOverall[$row['game_id']] = true;
        $playerId = $row['player_id'];
        $playerName = $players[$playerId];
        $gamePoints = $row['punkte'];
        if ($summenPunkteSystem && $gamePoints < 0) {
            $gamePoints = 0;
        }
        if (isset($averagePoints[$playerId])) {
            $averagePoints[$playerId]['games'] ++;
            $averagePoints[$playerId]['points'] += $gamePoints;
            $averagePoints[$playerId]['average'] = $averagePoints[$playerId]['points'] / $averagePoints[$playerId]['games'];
        } else {
            $averagePoints[$playerId] = array(
                'games' => 1,
                'playerName' => $playerName,
                'points' => $gamePoints,
                'average' => $gamePoints
            );
            $sortName[] = $playerName;
        }
    }
    array_multisort($sortName, SORT_ASC, $averagePoints);
}
$smarty->assign('averagePoints', $averagePoints);
$smarty->assign('gamesOverall', sizeof($gamesOverall));

/*
 * ********************************************************************************
 * Häufigkeit der Spieltypen
 *
 */

$statement = $pdo->prepare("SELECT * FROM `game_types`");
$result = $statement->execute();
$gameTypeNames = $statement->fetchall(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
$smarty->assign('gameTypeNames', $gameTypeNames);
$statement = $pdo->prepare("SELECT games.* FROM games, game_data, user_player WHERE user_player.user_id = ? AND user_player.player_id = game_data.player_id AND game_data.game_id = games.id ORDER BY games.id ASC");
$result = $statement->execute(array(
    $_SESSION['userid']
));
$gameTypes = $playerSum = array();
if ($statement->rowCount() > 0) {
    $games = $statement->fetchall(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    foreach ($games as $gameID => $game) {
        $gameType = $game['game_typ'];
        if ($gameType == NULL) {
            // Bei laufenden Runden steht der Spieltyp des aktuellen Spiels noch nicht in der Datenbank
            continue;
        }
        if (! isset($gameTypes[$gameType])) {
            $gameTypes[$gameType] = array(
                'overall' => array(
                    'percent' => 0,
                    'absolut' => 0
                )
            );
            foreach ($players as $playerID => $player) {
                if (! isset($playerSum[$playerID])) {
                    $playerSum[$playerID] = 0;
                }
                $gameTypes[$gameType] += array(
                    $playerID => array(
                        'percent' => 0,
                        'absolut' => 0
                    )
                );
            }
        }
        $gameTypes[$gameType]['overall']['absolut'] ++;
        $gameTypes[$gameType]['overall']['percent'] = round($gameTypes[$gameType]['overall']['absolut'] / sizeof($games) * 100);
        if ($gameType > 1) {
            $statement = $pdo->prepare("SELECT * FROM player_data WHERE game_id = ? AND game_typ = ?");
            $result = $statement->execute(array(
                $gameID,
                $gameType
            ));
            $playerID = $statement->fetch()['player_id'];
            $gameTypes[$gameType][$playerID]['absolut'] ++;
            $playerSum[$playerID] ++;
        } else {
            $statement = $pdo->prepare("SELECT * FROM game_data WHERE game_id = ?");
            $result = $statement->execute(array(
                $gameID
            ));
            while ($playerID = $statement->fetch()) {
                $playerID = $playerID['player_id'];
                $gameTypes[$gameType][$playerID]['absolut'] ++;
                $playerSum[$playerID] ++;
            }
        }
        foreach ($gameTypes as $gameType => $gameTypeData) {
            foreach ($players as $playerID => $player) {
                if ($playerSum[$playerID] > 0) {
                    $gameTypes[$gameType][$playerID]['percent'] = round($gameTypes[$gameType][$playerID]['absolut'] / $playerSum[$playerID] * 100);
                }
            }
        }
    }
}
$smarty->assign('gameTypes', $gameTypes);

/*
 * ********************************************************************************
 * Erspielte Sonderpunkte
 *
 */

$extraPoints = array();
$statement = $pdo->prepare("SELECT player_data.* FROM player_data, user_player WHERE (fuchs_gefangen OR karlchen_gewonnen OR karlchen_gefangen OR doppelkopf) AND user_player.player_id = player_data.player_id AND user_player.user_id = ?");
$result = $statement->execute(array(
    $_SESSION['userid']
));
if ($statement->rowCount() > 0) {
    error_reporting(E_ALL & ~ E_NOTICE);
    while ($row = $statement->fetch()) {
        if ($row['fuchs_gefangen']) {
            $extraPoints[$row['player_id']]['fuchs_gefangen'] ++;
            $extraPoints[$row['fuchs_gefangen']]['fuchs_verloren'] ++;
        }
        if ($row['karlchen_gewonnen']) {
            $extraPoints[$row['player_id']]['karlchen_gewonnen'] ++;
        }
        if ($row['karlchen_gefangen']) {
            $extraPoints[$row['player_id']]['karlchen_gefangen'] ++;
            $extraPoints[$row['karlchen_gefangen']]['karlchen_verloren'] ++;
        }
        if ($row['doppelkopf']) {
            $extraPoints[$row['player_id']]['doppelkopf'] ++;
        }
        $nameSort[] = $players[$row['player_id']];
    }
    error_reporting(E_ALL);
}
$smarty->assign('extraPoints', $extraPoints);
/*
 * ********************************************************************************
 * Historie über alle Doppelkopfrunden
 *
 */

$statement = $pdo->prepare("SELECT * FROM rounds WHERE user_id = ? ORDER BY date DESC");
$statement->execute(array(
    $user['id']
));
$runden = array();
while ($row = $statement->fetch()) {
    $runden[$row['id']] = array(
        'date' => $row['date'],
        'location' => $row['location'],
        'is_running' => $row['is_running'],
        'player' => array()
    );
}

$statement1 = $pdo->prepare("SELECT * FROM round_player WHERE round_id = ?");
$statement2 = $pdo->prepare("SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0");
foreach ($runden as $runde_id => $runde) {
    $statement1->execute(array(
        $runde_id
    ));
    $players = $statement1->fetchAll(PDO::FETCH_ASSOC);
    foreach ($players as $player) {
        $statement2->execute(array(
            $runde_id,
            $player['player_id']
        ));
        $row = $statement2->fetch();
        $runden[$runde_id]['player'][$player['player_id']] = $row['sum(punkte)'];
        ;
    }
}
$smarty->assign('runden', $runden);

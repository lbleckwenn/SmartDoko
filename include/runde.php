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
    $smarty->assign('error', 'Bitte zuerst <a href="login.php">einloggen</a>');
    $page = 'splashscreen';
    return;
}

if (! $f->easycheck()) {
    // *****************************************************************************
    // *** Reloadcheck
    // *****************************************************************************
    $error = 'Bitte nicht die "Reload"-Funktion des Browsers nutzen.';
} else {
    // *****************************************************************************
    // *** nach laufender Runde des Benutzers suchen
    // *****************************************************************************
    $statement = $pdo->prepare("SELECT * FROM rounds WHERE user_id = ? AND is_running = 1");
    $statement->execute(array(
        $user['id']
    ));
    if ($statement->rowCount() == 0) {
        // *****************************************************************************
        // *** Noch keine offene Runde in der Datenbank gefunden
        // *****************************************************************************
        $step = 1;
        if (isset($_GET['newRound'])) {
            // *****************************************************************************
            // *** Aber es wurden Daten für eine neue Runde übermittelt
            // *****************************************************************************
            include ('runde_neu.php');
        } else {
            // *****************************************************************************
            // *** Anzeige der bisherigen Runden mit Ergebnissen
            // *****************************************************************************
            include ('runde_historie.php');
        }
    } else {
        // *****************************************************************************
        // *** offene Runde gefunden, Daten der Runde laden
        // *****************************************************************************
        $step = 2;
        $round = $statement->fetch();
        $round_id = $round['id'];
        $aktuellesSpiel = $round['games'];
        // laudende Runde löschen
        if (isset($_GET['abbrechen'])) {
            $statement = $pdo->prepare("DELETE FROM games WHERE round_id = ?");
            $statement->execute(array(
                $round_id
            ));
            $statement = $pdo->prepare("DELETE FROM game_data WHERE round_id = ?");
            $statement->execute(array(
                $round_id
            ));
            $statement = $pdo->prepare("DELETE FROM player_data WHERE round_id = ?");
            $statement->execute(array(
                $round_id
            ));
            $statement = $pdo->prepare("DELETE FROM round_player WHERE round_id = ?");
            $statement->execute(array(
                $round_id
            ));
            $statement = $pdo->prepare("DELETE FROM rounds WHERE id = ?");
            $statement->execute(array(
                $round_id
            ));
        }
        // *****************************************************************************
        $anzahlSpieler = $round['player'];
        $statement = $pdo->prepare("SELECT * FROM games WHERE round_id = ? AND game_number = ?");
        $statement->execute(array(
            $round_id,
            $aktuellesSpiel
        ));
        $game = $statement->fetch();
        $game_id = $game['id'];
        // *****************************************************************************
        // *** schauen, ob für die Runde schon Spieler festgelegt wurden
        // *****************************************************************************
        $statement = $pdo->prepare("SELECT players.* FROM round_player, players WHERE round_player.round_id = ? AND round_player.player_id = players.id");
        $result = $statement->execute(array(
            $round_id
        ));
        if ($statement->rowCount() != $anzahlSpieler) {
            // *****************************************************************************
            // *** noch keine Spieler für die Runde festgelegt
            // *****************************************************************************
            if (isset($_GET['selectPlayer'])) {
                if (GetParam('submit') == 'save') {
                    $spieler = GetParam('spieler');
                    if (checkIfTwoElementsEqual($spieler)) {
                        $error = "Die Mitspielerauswahl ist fehlerhaft.";
                    }
                    if (! $error) {
                        $statement1 = $pdo->prepare("INSERT INTO round_player (round_id, player_id, platz, spielt, gibt) VALUES (?, ?, ?, ?, ?)");
                        $statement2 = $pdo->prepare("INSERT INTO game_data (round_id, game_id, player_id) VALUES (?, ?, ?)");
                        for ($i = 0; $i < $round['player']; $i ++) {
                            $mitspieler = array(
                                $round_id,
                                $spieler[$i],
                                $i + 1,
                                (($anzahlSpieler - $i) > 4 ? 0 : 1), // Bei mehr als 4 Spielern spielen nur die letzten 4 im ersten Spiel
                                ($i == 0 ? 1 : 0) // Spieler auf Platz 1 ist der erste Geber
                            );
                            $statement1->execute($mitspieler);
                            if ($anzahlSpieler - $i < 5) {
                                $mitspieler = array(
                                    $round_id,
                                    $game_id,
                                    $spieler[$i]
                                );
                                $statement2->execute($mitspieler);
                            }
                        }
                        $success = "Mitspielerauswahl gespeichert.";
                        $step = 3;
                    }
                } else {
                    $statement = $pdo->prepare("DELETE FROM games WHERE round_id = ?");
                    $statement->execute(array(
                        $round_id
                    ));
                    $statement = $pdo->prepare("DELETE FROM game_data WHERE round_id = ?");
                    $statement->execute(array(
                        $round_id
                    ));
                    $statement = $pdo->prepare("DELETE FROM round_player WHERE round_id = ?");
                    $statement->execute(array(
                        $round_id
                    ));
                    $statement = $pdo->prepare("DELETE FROM rounds WHERE round_id = ?");
                    $statement->execute(array(
                        $round_id
                    ));
                    $step = 1;
                }
            }
        } else {
            // *****************************************************************************
            // *** Spieler für Runde wurden bereits festgelegt
            // *****************************************************************************
            $step = 3;
            if (isset($_GET['spieldaten'])) {
                $spielTyp = GetParam('spielTyp');
                $spieler = GetParam('spieler');
                $ansage = GetParam('ansage');
                $absage = GetParam('absage');
                $partner = GetParam('partner');
                $sonderpunkt = GetParam('sonderpunkt');
                $gegner = GetParam('gegner');
                if ($sonderpunkt != 'null') {
                    include ('runde_sonderpunkt.php');
                }
                if ($spielTyp != 0) {
                    include ('runde_spieltyp.php');
                }
                if ($ansage != 'null') {
                    include ('runde_ansage.php');
                }
                if ($absage != 'null') {
                    include ('runde_absage.php');
                }
            }
            $smarty->assign('aktuellesSpiel', $aktuellesSpiel);
            // *****************************************************************************
            // *** Spieler des Spiels ermitteln
            // *****************************************************************************
            $ansage = $absage = array(
                're' => false,
                'kontra' => false
            );
            $gameTyp = 0;
            $statement = $pdo->prepare("SELECT game_data.*, player_data.game_typ, player_data.ansage, player_data.absage FROM game_data LEFT JOIN player_data ON player_data.game_id = :gameId AND (player_data.absage IS NOT NULL OR player_data.game_typ IS NOT NULL OR player_data.ansage IS NOT NULL) AND player_data.player_id = game_data.player_id WHERE game_data.game_id = :gameId");
            $result = $statement->execute(array(
                'gameId' => $game_id
            ));
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                if ($row['game_typ']) {
                    $gameTyp = $row['game_typ'];
                }
                if ($row['ansage']) {
                    $ansage[$row['ansage']] = true;
                }
                if ($row['absage']) {
                    $absage[$row['partei']] = $row['absage'];
                }
            }
            if ($gameTyp == 0 && ($ansage['re'] || $ansage['kontra'])) {
                $gameTyp = 1;
            }
            $statement = $pdo->prepare("SELECT players.*, CONCAT(players.vorname, ' ', players.nachname) as komplett, round_player.spielt, gd.partei as partei, sum(game_data.punkte) as punkte, round_player.gibt FROM round_player, players LEFT JOIN game_data ON round_id = :roundId and game_data.player_id = players.id and punkte > 0 LEFT JOIN game_data gd ON gd.game_id = :gameId and gd.player_id = players.id WHERE round_player.round_id = :roundId AND round_player.player_id = players.id GROUP BY id ORDER BY round_player.platz");
            $result = $statement->execute(array(
                'roundId' => $round_id,
                'gameId' => $game_id
            ));
            $alleSpieler = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            $jsObjectSpieler = '{';
            $re = $kontra = 0;
            foreach ($alleSpieler as $spielerId => $spieler) {
                $spielerName = ($mitNachnamen ? $spieler['komplett'] : $spieler['vorname']);
                if ($spieler['gibt']) {
                    $geber = $spielerName;
                }
                if ($spieler['spielt']) {
                    $jsObjectSpieler .= sprintf("%d: {name: '%s', partei: %s}, ", $spielerId, $spielerName, ($spieler['partei'] ? "'{$spieler ['partei']}'" : 'null'));
                    if ($spieler['partei'] == 're')
                        $re ++;
                    if ($spieler['partei'] == 'kontra')
                        $kontra ++;
                }
            }
            $smarty->assign('jsObjectSpieler', $jsObjectSpieler . '}');
            $smarty->assign('jsObjectSpielTyp', $gameTyp);
            $smarty->assign('jsObjectAnzahl', sprintf('{re: %d, kontra: %d}', $re, $kontra));
            $smarty->assign('jsObjectAnsagen', sprintf('{re: %s, kontra: %s}', ($ansage['re'] ? 'true' : 'false'), ($ansage['kontra'] ? 'true' : 'false')));
            $smarty->assign('jsObjectAbsagen', sprintf('{re: %s, kontra: %s}', ($absage['re'] ? "'{$absage ['re']}'" : 'null'), ($absage['kontra'] ? "'{$absage ['kontra']}'" : 'null')));
            $smarty->assign('alleSpieler', $alleSpieler);
            $smarty->assign('geber', $geber);
        }
    }

    $smarty->assign('step', $step);
}

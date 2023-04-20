<?php
$statement = $pdo->prepare("SELECT * FROM rounds WHERE user_id = ? ORDER BY date DESC");
$statement->execute(array(
    $user['id']
));
$runden = array();
while ($row = $statement->fetch()) {
    $runden[$row['id']] = array(
        'date' => strtotime($row['date']),
        'games' => $row['games'],
        'location' => $row['location'],
        'is_running' => $row['is_running'],
        'player' => array()
    );
}

$statement1 = $pdo->prepare("SELECT * FROM round_player WHERE round_id = ?");
$statement2 = $pdo->prepare("SELECT sum(punkte) FROM `game_data` WHERE round_id = ? AND player_id = ? AND punkte > 0");
$statement3 = $pdo->prepare("SELECT count(*) from games, game_data WHERE games.round_id = ? and game_data.player_id = ? and game_data.partei = games.gewinner and games.id = game_data.game_id");
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
        $statement3->execute(array(
            $runde_id,
            $player['player_id']
        ));
        $row = $statement3->fetch();
        $runden[$runde_id]['siege'][$player['player_id']] = $row['count(*)'];
    }
}
$smarty->assign('runden', $runden);
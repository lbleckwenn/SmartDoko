<?php

// Nur über CLI ausführbar
if (php_sapi_name() !== 'cli') {
    exit('Kein Zugriff.');
}

require __DIR__ . '/../../config/config.php';
$config = require __DIR__ . '/../../config/config.php';

$pdo = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}",
    $config['db']['user'],
    $config['db']['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Unbestätigte Accounts älter als 48 Stunden löschen
$stmt = $pdo->prepare("DELETE FROM users WHERE aktiv = 0 AND verify_token_erstellt < NOW() - INTERVAL 48 HOUR");
$stmt->execute();

$anzahl = $stmt->rowCount();
echo date('Y-m-d H:i:s') . " – {$anzahl} abgelaufene Accounts gelöscht.\n";

<?php

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Session früh starten
session_start();

// Autoloader
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Konfiguration laden
$config = require __DIR__ . '/../config/config.php';

// Datenbankverbindung
$pdo = \Core\Database::connect($config['db']);

// Abgelaufene unbestätigte Accounts aufräumen (maximal einmal pro Stunde)
$stmt = $pdo->query("SELECT wert FROM settings WHERE schluessel = 'letztes_cleanup'");
$letztesCleanup = $stmt->fetchColumn();

if (strtotime($letztesCleanup) < time() - 3600) {
    $pdo->exec("DELETE FROM users WHERE aktiv = 0 AND verify_token_erstellt < NOW() - INTERVAL 48 HOUR");
    $pdo->exec("UPDATE settings SET wert = NOW() WHERE schluessel = 'letztes_cleanup'");
}

// Router
$router = new \Core\Router($config['base_path']);

// Controller
$mailer = new \Core\Mailer($config['mail']['absender']);
$auth = new \Controller\AuthController($pdo, $mailer);

// Routen
$router->get('/', function () {
    if (isset($_SESSION['user_id'])) {
        // Eingeloggte Nutzer kommen später hier zur App
        echo "Dashboard kommt noch.";
    } else {
        require __DIR__ . '/../templates/home.php';
    }
});

$router->get('/login', fn() => $auth->showLogin());
$router->post('/login', fn() => $auth->handleLogin());
$router->get('/logout', fn() => $auth->handleLogout());
$router->get('/register', fn() => $auth->showRegister());
$router->post('/register', fn() => $auth->handleRegister());
$router->get('/register-bestaetigung', function () {
    require __DIR__ . '/../templates/auth/register_bestaetigung.php';
});
$router->get('/verify', fn() => $auth->handleVerify());
$router->get('/resend-verify', fn() => $auth->showResendVerify());
$router->post('/resend-verify', fn() => $auth->handleResendVerify());

$router->dispatch();

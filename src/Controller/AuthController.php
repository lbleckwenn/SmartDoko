<?php

namespace Controller;

use PDO;

class AuthController
{
    public function __construct(private PDO $pdo, private \Core\Mailer $mailer) {}

    public function showLogin(): void
    {
        require __DIR__ . '/../../templates/auth/login.php';
    }

    public function handleLogin(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $passwort = $_POST['passwort'] ?? '';

        if ($email === '' || $passwort === '') {
            $error = 'Bitte E-Mail und Passwort eingeben.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user === false || !password_verify($passwort, $user['passwort'])) {
            $error = 'E-Mail oder Passwort falsch.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        // Konto aktiv?
        if (!$user['aktiv']) {
            $error = 'Bitte bestätige zuerst deine E-Mail-Adresse.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['vorname'];

        header('Location: /');
        exit;
    }

    public function handleLogout(): void
    {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
    public function showRegister(): void
    {
        require __DIR__ . '/../../templates/auth/register.php';
    }

    public function handleRegister(): void
    {
        $vorname  = trim($_POST['vorname'] ?? '');
        $nachname = trim($_POST['nachname'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $passwort = $_POST['passwort'] ?? '';
        $passwort2 = $_POST['passwort2'] ?? '';

        if ($vorname === '' || $nachname === '' || $email === '' || $passwort === '') {
            $error = 'Bitte alle Felder ausfüllen.';
            require __DIR__ . '/../../templates/auth/register.php';
            return;
        }

        if ($passwort !== $passwort2) {
            $error = 'Die Passwörter stimmen nicht überein.';
            require __DIR__ . '/../../templates/auth/register.php';
            return;
        }

        if (strlen($passwort) < 8) {
            $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
            require __DIR__ . '/../../templates/auth/register.php';
            return;
        }

        // Prüfen ob E-Mail bereits vergeben
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch() !== false) {
            $error = 'Diese E-Mail-Adresse ist bereits registriert.';
            require __DIR__ . '/../../templates/auth/register.php';
            return;
        }

        // Benutzer anlegen
        $hash = password_hash($passwort, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO users (vorname, nachname, email, passwort) VALUES (?, ?, ?, ?)");
        $stmt->execute([$vorname, $nachname, $email, $hash]);

        // Token generieren
        $token = bin2hex(random_bytes(32));
        $stmt = $this->pdo->prepare("UPDATE users SET verify_token = ?, verify_token_erstellt = NOW() WHERE id = ?");
        $stmt->execute([$token, $this->pdo->lastInsertId()]);

        // Verifizierungs-Mail senden
        $this->mailer->sendVerifizierung($email, $vorname, $token);

        // NICHT direkt einloggen – erst nach Verifizierung
        header('Location: /register-bestaetigung');
        exit;
    }
    public function handleVerify(): void
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            header('Location: /');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE verify_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user === false) {
            $error = 'Ungültiger oder abgelaufener Link.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        // Token älter als 24 Stunden?
        $erstellt = new \DateTime($user['verify_token_erstellt']);
        $jetzt = new \DateTime();
        if ($jetzt->diff($erstellt)->h >= 24) {
            $error = 'Der Bestätigungslink ist abgelaufen. Bitte erneut registrieren.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        // Konto aktivieren
        $stmt = $this->pdo->prepare("UPDATE users SET aktiv = 1, verify_token = NULL, verify_token_erstellt = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);

        // Einloggen
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['vorname'];

        header('Location: /');
        exit;
    }
    public function showResendVerify(): void
    {
        require __DIR__ . '/../../templates/auth/resend_verify.php';
    }

    public function handleResendVerify(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $error = 'Bitte E-Mail-Adresse eingeben.';
            require __DIR__ . '/../../templates/auth/resend_verify.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Bewusst keine Unterscheidung ob E-Mail existiert oder nicht
        // – verhindert dass man herausfinden kann welche E-Mails registriert sind
        if ($user === false || $user['aktiv'] == 1) {
            $info = 'Falls diese E-Mail-Adresse bei uns registriert und noch nicht bestätigt ist, erhältst du in Kürze eine neue E-Mail.';
            require __DIR__ . '/../../templates/auth/resend_verify.php';
            return;
        }

        // Neuen Token generieren
        $token = bin2hex(random_bytes(32));
        $stmt = $this->pdo->prepare("UPDATE users SET verify_token = ?, verify_token_erstellt = NOW() WHERE id = ?");
        $stmt->execute([$token, $user['id']]);

        // Mail senden
        $this->mailer->sendVerifizierung($email, $user['vorname'], $token);

        $info = 'Falls diese E-Mail-Adresse bei uns registriert und noch nicht bestätigt ist, erhältst du in Kürze eine neue E-Mail.';
        require __DIR__ . '/../../templates/auth/resend_verify.php';
    }
    public function showPasswordReset(): void
    {
        require __DIR__ . '/../../templates/auth/password_reset.php';
    }

    public function handlePasswordReset(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            $error = 'Bitte E-Mail-Adresse eingeben.';
            require __DIR__ . '/../../templates/auth/password_reset.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Bewusst keine Unterscheidung ob E-Mail existiert oder nicht
        if ($user !== false && $user['aktiv'] == 1) {
            $token = bin2hex(random_bytes(32));
            $stmt = $this->pdo->prepare("UPDATE users SET reset_token = ?, reset_token_erstellt = NOW() WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
            $this->mailer->sendPasswordReset($email, $user['vorname'], $token);
        }

        $info = 'Falls diese E-Mail-Adresse bei uns registriert ist, erhältst du in Kürze eine E-Mail mit einem Link zum Zurücksetzen des Passworts.';
        require __DIR__ . '/../../templates/auth/password_reset.php';
    }

    public function showNewPassword(): void
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            header('Location: /login');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user === false) {
            $error = 'Ungültiger oder abgelaufener Link.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        // Token älter als 24 Stunden?
        $erstellt = new \DateTime($user['reset_token_erstellt']);
        $jetzt = new \DateTime();
        if ($jetzt->diff($erstellt)->h >= 24) {
            $error = 'Der Link ist abgelaufen. Bitte fordere einen neuen an.';
            require __DIR__ . '/../../templates/auth/password_reset.php';
            return;
        }

        require __DIR__ . '/../../templates/auth/new_password.php';
    }

    public function handleNewPassword(): void
    {
        $token     = $_POST['token'] ?? '';
        $passwort  = $_POST['passwort'] ?? '';
        $passwort2 = $_POST['passwort2'] ?? '';

        if ($token === '') {
            header('Location: /login');
            exit;
        }

        if ($passwort === '' || $passwort2 === '') {
            $error = 'Bitte beide Felder ausfüllen.';
            require __DIR__ . '/../../templates/auth/new_password.php';
            return;
        }

        if ($passwort !== $passwort2) {
            $error = 'Die Passwörter stimmen nicht überein.';
            require __DIR__ . '/../../templates/auth/new_password.php';
            return;
        }

        if (strlen($passwort) < 8) {
            $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
            require __DIR__ . '/../../templates/auth/new_password.php';
            return;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user === false) {
            $error = 'Ungültiger oder abgelaufener Link.';
            require __DIR__ . '/../../templates/auth/login.php';
            return;
        }

        // Passwort aktualisieren und Token löschen
        $hash = password_hash($passwort, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET passwort = ?, reset_token = NULL, reset_token_erstellt = NULL WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);

        $info = 'Dein Passwort wurde erfolgreich geändert.';
        require __DIR__ . '/../../templates/auth/login.php';
    }
}

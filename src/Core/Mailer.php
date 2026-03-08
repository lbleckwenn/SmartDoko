<?php

namespace Core;

class Mailer
{
    public function __construct(private string $absender) {}

    public function send(string $empfaenger, string $betreff, string $nachricht): bool
    {
        $headers = implode("\r\n", [
            'From: ' . $this->absender,
            'Content-Type: text/html; charset=utf-8',
            'MIME-Version: 1.0',
        ]);

        return mail($empfaenger, $betreff, $nachricht, $headers);
    }

    public function sendVerifizierung(string $empfaenger, string $vorname, string $token): bool
    {
        $link = 'http://' . $_SERVER['HTTP_HOST'] . '/verify?token=' . $token;

        $nachricht = "
            <p>Hallo {$vorname},</p>
            <p>bitte bestätige deine E-Mail-Adresse durch Klick auf folgenden Link:</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Der Link ist 24 Stunden gültig.</p>
            <p>SmartDoko</p>
        ";

        return $this->send($empfaenger, 'SmartDoko – E-Mail bestätigen', $nachricht);
    }
    public function sendPasswordReset(string $empfaenger, string $vorname, string $token): bool
    {
        $link = 'http://' . $_SERVER['HTTP_HOST'] . '/new-password?token=' . $token;

        $nachricht = "
        <p>Hallo {$vorname},</p>
        <p>du hast das Zurücksetzen deines Passworts angefordert. Klicke auf folgenden Link:</p>
        <p><a href='{$link}'>{$link}</a></p>
        <p>Der Link ist 24 Stunden gültig.</p>
        <p>Falls du kein neues Passwort angefordert hast, ignoriere diese E-Mail.</p>
        <p>SmartDoko</p>
    ";

        return $this->send($empfaenger, 'SmartDoko – Passwort zurücksetzen', $nachricht);
    }
}

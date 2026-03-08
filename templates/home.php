<?php require __DIR__ . '/layout.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-8 text-center">
        <h1>SmartDoko</h1>
        <p class="lead mt-3">
            Herzlich willkommen zu SmartDoko. SmartDoko ist eine Webseite zur Auswertung 
            von Doppelkopfrunden mit dem Smartphone, Tablet oder am PC.
        </p>
        <p>
            SmartDoko ist unter der GPLv3 lizenziert und kann auf 
            <a href="https://github.com/lbleckwenn/SmartDoko">GitHub</a> heruntergeladen werden.
        </p>
        <div class="mt-4">
            <a href="/login" class="btn btn-primary me-2">Jetzt anmelden</a>
            <a href="/register" class="btn btn-outline-primary">Jetzt registrieren</a>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <h3>Features</h3>
        <ul>
            <li>Abrechnung von Doppelkopfrunden</li>
            <li>Detaillierte Anzeige der Spielwertung</li>
            <li>Benutzereigene Spielrunden</li>
            <li>Mitspielerverwaltung</li>
            <li>Teilen von Ergebnissen mit Freunden</li>
            <li>Statistiken</li>
            <li>Responsives Webdesign für PC, Tablet und Smartphone</li>
        </ul>
    </div>
    <div class="col-md-6">
        <h3>Regelwerk</h3>
        <p>
            SmartDoko basiert auf den Turnierspielregeln des Deutschen Doppelkopf Verbands. 
            Beim Normalspiel erhalten die Spieler der Siegerpartei positive, die Verlierer 
            negative Spielpunkte (Plus-Minus-Wertung).
        </p>
        <a href="/regeln" class="btn btn-outline-secondary btn-sm">Zu den Regeln</a>
    </div>
</div>

<?php require __DIR__ . '/layout_end.php'; ?>
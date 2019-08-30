<?php
include ('./include/gewinner.php');
/*
 * Es wurde wie folgt abgerechnet (für Re):
 * Spielausgang : verloren mit 57
 * Die Abrechnung ist in den TSR geregelt. Unter 7.1.4 steht, dass KEINE Partei gewonnen hat, wenn beide Parteien ihr abgesagtes Ziel nicht erreicht haben. Es zählen dann nur die Punkte unter 7.2.2 (a), (e) und (f). Das wären hier:
 *
 * (a): für die Kontra-Partei 2 Punkte für:
 * - unter 90 gespielt
 * - unter 60 gespielt
 *
 * (e): für die Re-Partei 1 Punkt für:
 * - 30 Augen gegen Absage "Schwarz"
 *
 * (f): für die Kontra-Partei 4 Punkte für:
 * - 30 Augen gegen Absage "Schwarz"
 * - 60 Augen gegen Absage "Keine 30"
 * - 90 Augen gegen Absage "Keine 60"
 * - 120 Augen gegen Absage "Keine 90"
 *
 * Die Summe der Punkte für die Re-Partei ist daher:
 * (-2) + 1 + (-4) = -5
 */
$reAugen = 57;
$ansagen = array (
		're' => true,
		'kontra' => true
);
$absagen = array (
		're' => 0,
		'kontra' => 0
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );
/*
 * Re + keine 30
 * Kontra + keine 60
 * Kontra erreicht keine 30 Punkte
 */
$reAugen = 240 - 29;
$ansagen = array (
		're' => true,
		'kontra' => true
);
$absagen = array (
		're' => 30,
		'kontra' => 60
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );

/*
 * Wenn z.B. "Re, keine 90" und "Kontra, schwarz" gesagt wurde
 * (abgesehen davon, dasz sowas eigentlich nicht vorkommen kann,
 * aber mir geht es hier jetzt nur um das rechnerische Beispiel),
 * und das Spiel 75:165 ausgeht, bekommt dann Re 2 Punkte wegen
 * 2* 7.2.2e (60 gegen "keine 30" erreicht und 30 gegen "schwarz"
 * erreicht), und Kontra 2 Punkte wegen 7.2.2f (120 gegen "keine
 * 90" erreicht") + 7.2.2a (unter 90 gespielt)?
 *
 * Am einfachsten ist es, wenn man die zutreffenden Aussagen unter 7.2.2 abhakt.
 * In dem Beispiel ("Re, keine 90" und "Kontra, schwarz" abgesagt. Spiel endet 165:75 für Kontra.):
 * 7.2.2a) unter 90 gespielt, 1 Punkt für Kontra
 * An- und Absagen zählen nicht, siehe 7.1.4 der TSR.
 * 7.2.2e) Von der Re-Partei wurden 60 Augen gegen "keine 30" erreicht, 1 Punkt für Re
 * Von der Re-Partei wurden 30 Augen gegen "schwarz" erreicht, 1 Punkt für Re
 * 7.2.2f) Von der Kontra-Partei wurden 120 Augen gegen "keine 90" erreicht, 1 Punkt für Kontra
 *
 * Die Punkte (2:2) werden verrechnet, so dass jeder Spieler 0 Punkte erhält.
 */
$reAugen = 75;
$ansagen = array (
		're' => true,
		'kontra' => true
);
$absagen = array (
		're' => 90,
		'kontra' => 0
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );

/*
 * Die Re-Partei sagt "Re" und "Keine 90". Allerdings schafft es die Kontra-Partei auf 110 Augen. Damit hat die Kontra-Partei
 * gewonnen, denn sie hat sich nicht durch eigene Absagen zu einer höheren Augenzahl verpflichtet. Den Punkt "Verlierer unter 90
 * gespielt" gibt es hier nicht, da die Re-Partei ja 90 Augen hat! Wie gewohnt bei einem Sieg der Kontra-Partei im Normalspiel gibt es
 * auch den "Gegen die Kreuz-Damen gewonnen"-Punkt. Insgesamt sind es 5 Punkte:
 */
$reAugen = 240 - 110;
$ansagen = array (
		're' => true,
		'kontra' => false
);
$absagen = array (
		're' => 90,
		'kontra' => null
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );

/*
 * Die Re-Partei sagt "Re", "Keine 90" und "Keine 60". Die Kontra-Partei glaubt jedoch, 60 Augen erreichen zu können, und sagt
 * "Kontra". Am Ende hat sie 70 Augen und somit gewonnen, da sie sich nicht durch Absagen zu einer höheren Augenzahl verpflichtet
 * hat. Sie bekommt 8 Punkte:
 */
$reAugen = 240 - 70;
$ansagen = array (
		're' => true,
		'kontra' => true
);
$absagen = array (
		're' => 60,
		'kontra' => null
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );

/*
 * Wie im vorherigen Beispiel sagt die Re-Partei "Re", "Keine 90" und "Keine 60" sowie die Kontra-Partei ebenso "Kontra", "Keine 90"
 * und "Keine 60". Nun erreicht die Re-Partei 170 Augen und die Kontra-Partei 70 Augen. Wieder hat niemand seine Absage erfüllt,
 * und die dreizeilige Tabelle kann benutzt werden. Im Gegensatz zu vorhin hat eine Partei, nämlich die Re-Partei, die gewöhnliche
 * Stufe "Verlierer unter 90 gespielt" erreicht. Insgesamt bekommt die Re-Partei 3 Punkte:
 */
$reAugen = 170;
$ansagen = array (
		're' => true,
		'kontra' => true
);
$absagen = array (
		're' => 60,
		'kontra' => 60
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );

/*
 * Eigenes Szenario: wird schwarz richtig berechnet?
 */
$reAugen = 240;
$ansagen = array (
		're' => true,
		'kontra' => false
);
$absagen = array (
		're' => 0,
		'kontra' => null
);
$sonderpunkte = array (
		're' => array (),
		'kontra' => array ()
);
$gewinner = ermitteleGewinner ( $reAugen, $ansagen, $absagen );
$punkte = zaehlePunkte ( $reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal' );
echo ("Gewinner: $gewinner | Punkte Re: {$punkte['re']} | Punkte Kontra: {$punkte['kontra']}<br>\n");
print_r ( $punkte ['log'] );
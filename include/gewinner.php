<?php
function ermitteleGewinner($rePunkte, $ansagen, $absagen) {
	$kontraPunkte = 240 - $rePunkte;
	extract ( $ansagen );
	if (empty ( $absagen )) {
		// keine Absagen
		if ($re == $kontra || ($re && ! $kontra)) {
			// Keine Ansage, nur Re oder Re und Kontra angesagt
			$reGewinntAb = 121;
			$kontraGewinntAb = 120;
		} else {
			// nur Kontra angesagt.
			$reGewinntAb = 120;
			$kontraGewinntAb = 121;
		}
	} else {
		// mit Absagen
		if (isset ( $absagen ['re'] )) {
			// Absage von Re-Partei an Kontra-Partei
			$reGewinntAb = 240 - $absagen ['re'] + (($absagen ['re']) ? 1 : 0);
		} else {
			// Absage von Kontra-Partei an Re-Partei
			$reGewinntAb = $absagen ['kontra'] + (($absagen ['kontra']) ? 0 : 1);
		}
		if (isset ( $absagen ['kontra'] )) {
			// Absage von Kontra-Partei an Re-Partei
			$kontraGewinntAb = 240 - $absagen ['kontra'] + (($absagen ['kontra']) ? 1 : 0);
		} else {
			// Absage von Re-Partei an Kontra-Partei
			$kontraGewinntAb = $absagen ['re'] + (($absagen ['re']) ? 0 : 1);
		}
	}
	if ($rePunkte >= $reGewinntAb) {
		return 're';
	}
	if ($kontraPunkte >= $kontraGewinntAb) {
		return 'kontra';
	}
	return 'niemand';
}
function zaehlePunkte($reAugen, $ansagen, $absagen, $sonderpunkte, $gewinner, $spielTyp = 'normal') {
	$kontraAugen = 240 - $reAugen;
	if ($gewinner == 'niemand') {
		$augenVerlierer = min ( $reAugen, $kontraAugen );
	} else {
		$augenVerlierer = ($gewinner == 're' ? $kontraAugen : $reAugen);
	}
	$auswertungsLog = array ();
	// Regel 7.2.2 (a) - Punkte für Sieg und unter xx gespielt
	if ($gewinner != 'niemand') {
		$auswertungsLog ['7.2.2 (a) - Allgemein'] [] = array (
				'text' => 'gewonnen',
				'punkte' => 1
		);
	}
	for($augen = 90; $augen >= 0; $augen -= 30) {
		if ($augenVerlierer < $augen || ($augenVerlierer == 0 && $augen == 0)) {
			$auswertungsLog ['7.2.2 (a) - Allgemein'] [] = array (
					'text' => ($augen > 0 ? "unter $augen gespielt" : 'schwarz gespielt'),
					'punkte' => 1
			);
		}
	}
	if ($gewinner != 'niemand') {
		// Regel 7.2.2 (b) - Punkte für Ansagen
		if (isset ( $ansagen ['re'] ) && $ansagen ['re']) {
			$auswertungsLog ['7.2.2 (b) - Es wurde'] [] = array (
					'text' => '"Re" angesagt',
					'punkte' => 2
			);
		}
		if (isset ( $ansagen ['kontra'] ) && $ansagen ['kontra']) {
			$auswertungsLog ['7.2.2 (b) - Es wurde'] [] = array (
					'text' => '"Kontra" angesagt',
					'punkte' => 2
			);
		}

		// Regel 7.2.2 (c) - Punkte für Absagen der Re-Partei
		if (isset ( $absagen ['re'] )) {
			for($augen = 90; $augen >= 0; $augen -= 30) {
				if ($absagen ['re'] <= $augen) {
					$auswertungsLog ['7.2.2 (c) - Es wurde von der Re-Partei'] [] = array (
							'text' => ($augen > 0 ? "'keine $augen'" : 'schwarz') . ' abgesagt',
							'punkte' => 1
					);
				}
			}
		}

		// Regel 7.2.2 (d) - Punkte für Absagen der Kontra-Partei
		if (isset ( $absagen ['kontra'] )) {
			for($augen = 90; $augen >= 0; $augen -= 30) {
				if ($absagen ['kontra'] <= $augen) {
					$auswertungsLog ['7.2.2 (d) - Es wurde von der Kontra-Partei'] [] = array (
							'text' => ($augen > 0 ? "'keine $augen'" : 'schwarz') . ' abgesagt',
							'punkte' => 1
					);
				}
			}
		}
	}
	// Regel 7.2.2 (e) - gegen Absagen Kontra-Partei erreicht
	if (isset ( $absagen ['kontra'] )) {
		for($augen = 90; $augen >= 0; $augen -= 30) {
			if ($reAugen >= $augen + 30 && $absagen ['kontra'] <= $augen) {
				$auswertungsLog ['7.2.2 (e) - Es wurden von der Re-Partei'] [] = array (
						'text' => sprintf ( "%d Augen gegen '%s' erreicht", $augen + 30, ($augen > 0 ? "keine $augen" : 'schwarz') ),
						'punkte' => ($kontraAugen < $reAugen || $gewinner == 're' ? 1 : - 1)
				);
			}
		}
	}
	// Regel 7.2.2 (f) - gegen Absagen Re-Partei erreicht
	if (isset ( $absagen ['re'] )) {
		for($augen = 90; $augen >= 0; $augen -= 30) {
			if ($kontraAugen >= $augen + 30 && $absagen ['re'] <= $augen) {
				$auswertungsLog ['7.2.2 (f) - Es wurden von der Kontra-Partei'] [] = array (
						'text' => sprintf ( "%d Augen gegen '%s' erreicht", $augen + 30, ($augen > 0 ? "keine $augen" : 'schwarz') ),
						'punkte' => ($kontraAugen >= $reAugen ? 1 : - 1)
				);
			}
		}
	}
	// Regel 7.2.3 - Sonderpunkte
	if ($spielTyp == 'normal') {
		if ($gewinner == 'kontra') {
			$auswertungsLog ['7.2.3 - Sonderpunkte'] [] = array (
					'text' => 'gegen die Kreuz Damen gewonnen',
					'punkte' => 1
			);
		}
		foreach ( $sonderpunkte as $partei => $parteiSonderpunkte ) {
			if (is_array ( $parteiSonderpunkte )) {
				if ($partei == $gewinner) {
					$multiplikator = 1;
				} elseif ($gewinner == 'niemand' && $partei == 're' && $reAugen > $kontraAugen) {
					$multiplikator = 1;
				} elseif ($gewinner == 'niemand' && $partei == 'kontra' && $kontraAugen >= $reAugen) {
					$multiplikator = 1;
				} else {
					$multiplikator = - 1;
				}
				foreach ( $parteiSonderpunkte as $sonderpunkt ) {
					$auswertungsLog ['7.2.3 - Sonderpunkte'] [] = array (
							'text' => ucfirst ( $partei ) . ": $sonderpunkt",
							'punkte' => 1 * $multiplikator
					);
				}
			}
		}
	}
	$spielpunkte = 0;
	foreach ( $auswertungsLog as $regel => $punkte ) {
		foreach ( $punkte as $punkt ) {
			$spielpunkte += $punkt ['punkte'];
		}
	}
	if ($gewinner == 'niemand') {
		return array (
				're' => $spielpunkte * (($reAugen > $kontraAugen) ? 1 : - 1) /* * (($spielTyp == 'solo') ? 3 : 1)*/,
				'kontra' => $spielpunkte * (($reAugen > $kontraAugen) ? - 1 : 1),
				'log' => $auswertungsLog
		);
	} else {
		return array (
				're' => $spielpunkte * (($gewinner == 're') ? 1 : - 1) /* * (($spielTyp == 'solo') ? 3 : 1)*/,
				'kontra' => $spielpunkte * (($gewinner == 'kontra') ? 1 : - 1),
				'log' => $auswertungsLog
		);
	}
}

// Initialisierung
var spielTyp = spielDaten.spielTyp;
var alleSpieler = spielDaten.alleSpieler;
var anzeige = {
	re : true,
	kontra : true,
	absage : true,
	sonderpunkt : true,
	stillehochzeit : true
}
var auswahl = 'spieler';
var spieler;
var partner;
var gegner;
var ansage;
var absage;
var sonderpunkt;
var spielName;
var players = alleSpielerIds;
$('#menu').html(createMenu('Auswahl:', players, [ 'abrechnung', 'beenden' ]));

$(document).on("click", '#buttons > button.btn', function(event) {
	var buttonId = this.id;
	var absagen = allAbsagen();
	if (buttonId in absagen) {
		if (!ansage) {
			if (spielTyp == 1) {
				if (spielDaten.ansagen.re == 1) {
					ansage = 're';
				} else {
					ansage = 'kontra';
				}
			} else {
				ansage = 're';
			}
		}
		absage = absagen[buttonId];
		$('#menu').html(confirmationRequest());
	}
	var solos = allSolos();
	if (buttonId in solos) {
		spielTyp = solos[buttonId];
		spielName = buttonId;
		$('#menu').html(createMenu('', [], getPossibleButtons()));
	}
	var sonderpunkte = allSonderpunkte();
	if (buttonId in sonderpunkte) {
		sonderpunkt = buttonId;
		if (sonderpunkt.substr(-8) == 'gefangen') {
			auswahl = 'gegner';
			players = getPlayersWithout(spieler);
			$('#menu').html(createMenu('Gefangen von:', players, [ 'abbrechen' ]));
		} else {
			$('#menu').html(confirmationRequest());
		}
	}
	if ($.isNumeric(buttonId)) {
		switch (auswahl) {
		case 'spieler':
			spieler = buttonId;
			break;
		case 'partner':
			partner = buttonId;
			break;
		case 'gegner':
			gegner = buttonId;
			break;
		}
		if (sonderpunkt) {
			$('#menu').html(confirmationRequest());
			return;
		}
		if (spielTyp) {
			var topText = 'Auswahl:';
			if (spielTyp == 2 || spielTyp == 4) {
				var topText = 'Absage gilt auch als "Re"-Ansage:';
			}
			$('#menu').html(createMenu(topText, [], getPossibleButtons()));
		} else {
			$('#menu').html(createMenu('Bitte auswählen:', [], [ 're', 'kontra', 'sonderpunkt', 'hochzeit', 'armut', 'solo', 'abbrechen' ]));
		}
	}
	switch (buttonId) {
	case 'abbrechen':
		location.reload();
		break;
	case 'ueberspringen':
		$('#menu').html(confirmationRequest());
		break;
	case 're':
	case 'kontra':
		if (!spielTyp) {
			spielTyp = 1;
		}
		ansage = buttonId;
		if (anzeige.absage) {
			anzeige.re = false;
			anzeige.kontra = false;
			anzeige.sonderpunkt = false;
			anzeige.stillehochzeit = false;
			$('#menu').html(createMenu('Absage auswählen:', [], getPossibleButtons()));
		} else {
			$('#menu').html(confirmationRequest());
		}
		break;
	case 'hochzeit':
		spielTyp = 2;
		spielName = buttonId;
	case 'armut':
		if (spielTyp != 2) {
			spielTyp = 4;
			spielName = buttonId;
		}
		auswahl = 'partner';
		anzeige.kontra = false;
		anzeige.sonderpunkt = false;
		anzeige.stillehochzeit = false;
		players = getPlayersWithout(spieler);
		$('#menu').html(createMenu('Mitspieler auswählen', players, [ 'abbrechen' ]));
		break;
	case 'solo':
		anzeige.kontra = false;
		anzeige.sonderpunkt = false;
		anzeige.stillehochzeit = false;
		$('#menu').html(createMenu('Solo auswählen', [], [ 'damensolo', 'bubensolo', 'fleischlos', 'kreuzsolo', 'piksolo', 'herzsolo', 'karosolo', 'stillehochzeit', 'abbrechen' ]));
		break;
	case 'stillehochzeit':
		spielTyp = 3;
		spielName = buttonId;
		$('#menu').html(confirmationRequest());
		break;
	case 'sonderpunkt':
		$('#menu').html(createMenu('Sonderpunkt auswählen', [], [ 'doppelkopf', 'fuchsgefangen', 'karlchen', 'karlchengefangen', 'abbrechen' ]));
		break;
	}
});
$(document).on("click", '#buttons > button.btn', function(event) {
	var buttonId = this.id;
});
function createMenu(text, players, buttons) {
	var retvar = '<span id="buttons">';
	if (text) {
		retvar += '<span class="font-weight-bold">' + text + '</span>';
	}
	for (i = 0; i < players.length; i++) {
		retvar += createButton(players[i]);
	}
	for (i = 0; i < buttons.length; i++) {
		retvar += createButton(buttons[i]);
	}
	retvar += debug();
	retvar += '</span>';
	return retvar;
}
function confirmationRequest() {
	var retvar = '<p class="font-weight-bold">Bitte Bestätigen:</p><p>';
	var buttons = allButtons();
	if (spielTyp != spielDaten.spielTyp) {
		if (spielTyp == 1) {
			retvar += 'Normalspiel';
		} else if (spielTyp == 2 || spielTyp == 4) {
			retvar += alleSpieler[spieler].vorname + ' und ' + alleSpieler[partner].vorname + ' spielen eine ' + capitalizeFirstLetter(spielName);
		} else {
			if (spielTyp == 3) {
				var artikel = 'eine ';
			} else {
				var artikel = 'ein ';
			}
			retvar += alleSpieler[spieler].vorname + ' spielt ' + artikel + capitalizeFirstLetter(buttons[spielName].text);
		}
		retvar += '.<br>';
	}
	if (ansage && spielDaten.ansagen[ansage] == 0) {
		retvar += alleSpieler[spieler].vorname + ' hat "' + capitalizeFirstLetter(ansage) + '" an';
		if (absage) {
			retvar += '- und "' + absage + '" ab';
		}
		retvar += 'gesagt.</br>';
	} else if (absage) {
		if (ansage) {
			var partei = ansage;
		} else {
			var partei = alleSpieler[spieler].partei;
		}
		retvar += alleSpieler[spieler].vorname + ' hat "' + absage + '" für die "' + capitalizeFirstLetter(partei) + '"-Partei abgesagt.</br>';
	}
	if (sonderpunkt) {
		retvar += 'Sonderpunkt für ' + alleSpieler[spieler].vorname + ': ' + capitalizeFirstLetter(buttons[sonderpunkt].text);
		if (gegner) {
			retvar += ' von ' + alleSpieler[gegner].vorname;
		}
	}
	retvar += '</p>';
	retvar += createButton('speichern');
	retvar += createButton('abbrechen');
	retvar += '<input type="text" value="' + spielTyp + '">';
	retvar += '<input type="text" value="' + spieler + '">';
	retvar += '<input type="text" value="' + ansage + '">';
	retvar += '<input type="text" value="' + absage + '">';
	retvar += '<input type="text" value="' + partner + '">';
	retvar += '<input type="text" value="' + sonderpunkt + '">';
	retvar += '<input type="text" value="' + gegner + '">';
	retvar += debug();
	return retvar;
}
function createButton(button) {
	var buttons = allButtons();
	if ($.isNumeric(button)) {
		buttons[button] = {
			farbe : 'btn-primary',
			text : alleSpieler[button].vorname
		};
	}
	return '<button type="button" class="btn ' + buttons[button].farbe + ' btn-block" id="' + button + '">' + buttons[button].text + '</button>';
}
function getPlayersWithout(player) {
	var players = [];
	for (i = 0; i < alleSpielerIds.length; i++) {
		if (alleSpielerIds[i] != player) {
			players.push(alleSpielerIds[i]);
		}
	}
	return players;
}
function getPossibleButtons() {
	var buttons = [];
	if (alleSpieler[spieler].partei == null && (spielDaten.ansagen.re + spielDaten.ansagen.kontra < 3)) {
		if (anzeige.re && (spielDaten.ansagen.re == 0 || (spielDaten.ansagen.re == 1 && spielDaten.ansagen.kontra != 0))) {
			buttons.push('re');
		}
		if (anzeige.kontra && (spielDaten.ansagen.kontra == 0 || (spielDaten.ansagen.kontra == 1 && spielDaten.ansagen.re != 0))) {
			buttons.push('kontra');
		}
	}
	if ((spielDaten.ansagen.re == spielDaten.ansagen.kontra && spielDaten.ansagen.re != 1) || alleSpieler[spieler].partei != null || ansage
			|| ((spielDaten.ansagen.re + spielDaten.ansagen.kontra) % 2 != 0)) {
		buttons.push('keine90', 'keine60', 'keine30', 'schwarz');
		anzeige.absage = false;
	}
	if (anzeige.sonderpunkt) {
		buttons.push('sonderpunkt');
	}
	if (anzeige.stillehochzeit && alleSpieler[spieler].partei != 'kontra') {
		buttons.push('stillehochzeit');
	}
	if (spielTyp != spielDaten.spielTyp) {
		buttons.push('ueberspringen');
	}
	buttons.push('abbrechen');
	return buttons;
}
function zeigeAnsage(partei) {
	var retvar
	for ( var spielerId in alleSpieler) {
		if (allespieler[spielerId].partei == partei) {
			return false;
		}
	}
	return true;
}
function capitalizeFirstLetter(string) {
	// https://stackoverflow.com/questions/1026069/how-do-i-make-the-first-letter-of-a-string-uppercase-in-javascript
	return string.charAt(0).toUpperCase() + string.slice(1);
}
function allSolos() {
	return {
		kreuzsolo : 5,
		piksolo : 6,
		herzsolo : 7,
		karosolo : 8,
		damensolo : 9,
		bubensolo : 10,
		fleischlos : 11
	};
}
function allAbsagen() {
	return {
		keine90 : 'keine 90',
		keine60 : 'keine 60',
		keine30 : 'keine 30',
		schwarz : 'schwarz'
	};
}
function allSonderpunkte() {
	return {
		doppelkopf : 0,
		fuchsgefangen : 1,
		karlchen : 0,
		karlchengefangen : 1
	};
}
function allButtons() {
	return {
		kreuzsolo : {
			farbe : 'btn-primary',
			text : 'Farbsolo (Kreuz)'
		},
		piksolo : {
			farbe : 'btn-primary',
			text : 'Farbsolo (Pik)'
		},
		herzsolo : {
			farbe : 'btn-primary',
			text : 'Farbsolo (Herz)'
		},
		karosolo : {
			farbe : 'btn-primary',
			text : 'Farbsolo (Karo)'
		},
		damensolo : {
			farbe : 'btn-primary',
			text : 'Damensolo'
		},
		bubensolo : {
			farbe : 'btn-primary',
			text : 'Bubensolo'
		},
		fleischlos : {
			farbe : 'btn-primary',
			text : 'Fleischlossolo'
		},
		re : {
			farbe : 'btn-primary',
			text : 'Ansage: Re'
		},
		kontra : {
			farbe : 'btn-primary',
			text : 'Ansage: Kontra'
		},
		sonderpunkt : {
			farbe : 'btn-secondary',
			text : 'Sonderpunkt'
		},
		hochzeit : {
			farbe : 'btn-warning',
			text : 'Hochzeit'
		},
		armut : {
			farbe : 'btn-warning',
			text : 'Armut'
		},
		solo : {
			farbe : 'btn-info',
			text : 'Solo'
		},
		stillehochzeit : {
			farbe : 'btn-info',
			text : 'Stille Hochzeit'
		},
		keine90 : {
			farbe : 'btn-warning',
			text : 'Keine 90'
		},
		keine60 : {
			farbe : 'btn-warning',
			text : 'Keine 60'
		},
		keine30 : {
			farbe : 'btn-warning',
			text : 'Keine 30'
		},
		schwarz : {
			farbe : 'btn-warning',
			text : 'Schwarz'
		},
		abrechnung : {
			farbe : 'btn-success',
			text : 'Spielabrechnung'
		},
		speichern : {
			farbe : 'btn-success',
			text : 'Speichern'
		},
		ueberspringen : {
			farbe : 'btn-success',
			text : 'Überspringen'
		},
		beenden : {
			farbe : 'btn-danger',
			text : 'Runde beenden'
		},
		abbrechen : {
			farbe : 'btn-danger',
			text : 'Abbrechen'
		},
		doppelkopf : {
			farbe : 'btn-primary',
			text : 'Doppelkopf'
		},
		fuchsgefangen : {
			farbe : 'btn-primary',
			text : 'Fuchs gefangen'
		},
		karlchen : {
			farbe : 'btn-primary',
			text : 'Karlchen'
		},
		karlchengefangen : {
			farbe : 'btn-primary',
			text : 'Karlchen gefangen'
		}
	};
}
function debug() {
	var retvar = '<dl class="row mt-3">';
	retvar += '<dt class="col-7">auswahl</dt><dd class="col-5">' + auswahl + '</dd>';
	retvar += '<dt class="col-7">spielTyp</dt><dd class="col-5">' + spielTyp + '</dd>';
	retvar += '<dt class="col-7">spielName</dt><dd class="col-5">' + spielName + '</dd>';
	retvar += '<dt class="col-7">spieler</dt><dd class="col-5">' + spieler + '</dd>';
	retvar += '<dt class="col-7">ansage</dt><dd class="col-5">' + ansage + '</dd>';
	retvar += '<dt class="col-7">absage</dt><dd class="col-5">' + absage + '</dd>';
	retvar += '<dt class="col-7">partner</dt><dd class="col-5">' + partner + '</dd>';
	retvar += '<dt class="col-7">gegner</dt><dd class="col-5">' + gegner + '</dd>';
	retvar += '<dt class="col-7">sonderpunkt</dt><dd class="col-5">' + sonderpunkt + '</dd>';
	retvar += '<dt class="col-7">anzeige.re</dt><dd class="col-5">' + anzeige.re + '</dd>';
	retvar += '<dt class="col-7">anzeige.kontra</dt><dd class="col-5">' + anzeige.kontra + '</dd>';
	retvar += '<dt class="col-7">anzeige.absage</dt><dd class="col-5">' + anzeige.absage + '</dd>';
	retvar += '<dt class="col-7">anzeige.sonderpunkt</dt><dd class="col-5">' + anzeige.sonderpunkt + '</dd>';
	retvar += '<dt class="col-7">anzeige.stillehochzeit</dt><dd class="col-5">' + anzeige.stillehochzeit + '</dd>';
	retvar += '</dl>';
	return retvar;
}

/*
 * $('.parent-element').on('click', '.mylink', function(){ alert ("new link
 * clicked!"); })
 */
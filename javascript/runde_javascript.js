// Initialisierung

var spielTyp;
var anzeigeAnsagen = true;
var anzeigeSonderpunkt = true;
var anzeigeStilleHochzeit = true;
var auswahl = 'spieler';
var spieler;
var partner;
var gegner;
var sonderpunkt;
var players = alleSpielerIds;
$('#menu').html(createMenu('Auswahl:', players, [ 'abrechnung', 'beenden' ]));

$(document).on("click", '#buttons > button.btn', function(event) {
	var buttonId = this.id;
	var solos = {
		kreuzsolo : 5,
		piksolo : 6,
		herzsolo : 7,
		karosolo : 8,
		damensolo : 9,
		bubensolo : 10,
		fleischlos : 11
	};
	if (buttonId in solos) {
		spielTyp = solos[buttonId];
		$('#menu').html(createMenu('', [], getPossibleButtons()));
		return;
	}
	sonderpunkte = {
		doppelkopf : 0,
		fuchsgefangen : 1,
		karlchen : 0,
		karlchengefangen : 1
	};
	if (buttonId in sonderpunkte) {
		sonderpunkt = buttonId;		
		$('#menu').html(createMenu('', [], ['speichern','abbrechen']));
		return;
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
		if (spielTyp) {
			$('#menu').html(createMenu('', [], getPossibleButtons()));
		} else {
			$('#menu').html(createMenu('Bitte auswählen:', [], [ 're', 'kontra', 'sonderpunkt', 'hochzeit', 'armut', 'solo', 'abbrechen' ]));
		}
	}
	switch (buttonId) {
	case 're':
	case 'kontra':
		// Spieler hat Re oder Kontra angesagt
		spielTyp = 1;
		anzeigeAnsagen = false;
		anzeigeSonderpunkt = false;
		anzeigeStilleHochzeit = false;
		$('#menu').html(createMenu('Absage auswählen', [], getPossibleButtons()));
		break;
	case 'hochzeit':
		spielTyp = 2;
		auswahl = 'partner';
		anzeigeSonderpunkt = false;
		anzeigeStilleHochzeit = false;
		players = getPlayersWithout(spieler);
		$('#menu').html(createMenu('Mitspieler auswählen', players, [ 'abbrechen' ]));
		break;
	case 'armut':
		spielTyp = 4;
		auswahl = 'partner';
		anzeigeSonderpunkt = false;
		anzeigeStilleHochzeit = false;
		players = getPlayersWithout(spieler);
		$('#menu').html(createMenu('Mitspieler auswählen', players, [ 'abbrechen' ]));
		break;
	case 'solo':
		anzeigeSonderpunkt = false;
		anzeigeStilleHochzeit = false;
		$('#menu').html(createMenu('Solo auswählen', [], [ 'kreuzsolo', 'piksolo', 'herzsolo', 'karosolo', 'damensolo', 'bubensolo', 'fleischlos', 'abbrechen' ]));
		break;
	case 'sonderpunkt':
		$('#menu').html(createMenu('Sonderpunkt auswählen', [], [ 'doppelkopf', 'fuchsgefangen', 'karlchen', 'karlchengefangen', 'abbrechen' ]));
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
function debug() {
	var retvar = '<dl class="row mt-3">';
	retvar += '<dt class="col-7">spielTyp</dt><dd class="col-5">' + spielTyp + '</dd>';
	retvar += '<dt class="col-7">spieler</dt><dd class="col-5">' + spieler + '</dd>';
	retvar += '<dt class="col-7">partner</dt><dd class="col-5">' + partner + '</dd>';
	retvar += '<dt class="col-7">gegner</dt><dd class="col-5">' + gegner + '</dd>';
	retvar += '<dt class="col-7">sonderpunkt</dt><dd class="col-5">' + sonderpunkt + '</dd>';
	retvar += '<dt class="col-7">anzeigeAnsagen</dt><dd class="col-5">' + anzeigeAnsagen + '</dd>';
	retvar += '<dt class="col-7">anzeigeSonderpunkt</dt><dd class="col-5">' + anzeigeSonderpunkt + '</dd>';
	retvar += '<dt class="col-7">anzeigeStilleHochzeit</dt><dd class="col-5">' + anzeigeStilleHochzeit + '</dd>';
	retvar += '</dl>';
	return retvar;
}
function createButton(button) {
	var buttons = allButtons();
	if ($.isNumeric(button)) {
		buttons[button] = {
			farbe : 'btn-primary',
			text : alleSpieler[button]
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
	if (anzeigeAnsagen) {
		buttons.push('re', 'kontra');
	}
	buttons.push('keine90', 'keine60', 'keine30', 'schwarz');
	if (anzeigeSonderpunkt) {
		buttons.push('sonderpunkt');
	}
	if (anzeigeStilleHochzeit) {
		buttons.push('stillehochzeit');
	}
	buttons.push('abbrechen');
	return buttons;
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
			text : 'Bubuensolo'
		},
		fleischlos : {
			farbe : 'btn-primary',
			text : 'Fleischloser'
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
/*
 * $('.parent-element').on('click', '.mylink', function(){ alert ("new link
 * clicked!"); })
 */
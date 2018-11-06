function createButton(button) {
	var buttons = {
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
		abbrechen : {
			farbe : 'btn-danger',
			text : 'Abbrechen'
		}
	};
	return '<button type="button" class="btn ' + buttons[button]['farbe']
			+ ' btn-block" id="' + button + '">' + buttons[button]['text']
			+ '</button>';
}
function createMenu(menuId, buttons) {
	var return_value = '<span id="' + menuId + '">';
	for (i = 0; i < buttons.length; i++) {
		return_value += createButton(buttons[i]);
	}
	return_value += '</span>';
	return return_value;
}
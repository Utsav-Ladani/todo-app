/**
 * Character Name Handler
 */

// Call function using jQuery
jQuery(function ($) {
	// run on document ready
	$(document).ready(function () {
		// get the translation function
		const { __ } = wp.i18n;

		// get DOM objects
		const actorInput = $('#rt-movie-meta-crew-actor');
		const charactersName = $('#rt-characters-name');
		const characterNameInput = $(
			'#rt-movie-meta-crew-actor-character-name'
		);

		// get the values from the input
		let characterRawValues = characterNameInput.val();

		if (characterRawValues !== '') {
			characterRawValues = JSON.parse(characterRawValues);
		} else {
			characterRawValues = {};
		}
		const characterValues = characterRawValues;

		// record the change in the input for character name
		const recordChange = function () {
			// get the id
			const id = $(this).attr('id');

			// get the list of actor ids
			const actorIDs = actorInput.val();

			// if the id is not in the list of actor ids, return
			if (id === '' || actorIDs.indexOf(id) === -1) {
				return;
			}

			// record the change
			characterValues[id] = $(this).val();

			// update the input
			const strValue = JSON.stringify(characterValues);
			characterNameInput.val(strValue);
		};

		// set the character name box
		const setCharactersNameBox = () => {
			const actorIDs = actorInput.val();

			// create the empty list
			const characterNameList = $('<ul>');

			// loop through the actor ids
			actorIDs.forEach(function (actorID) {
				// if the actor id is empty, return
				if (actorID === '') {
					return;
				}

				// get the actor name from html
				const actorName = $('#rt-movie-meta-crew-actor')
					.find('option[value="' + actorID + '"]')
					.prop('selected', true)
					.text();

				// if the actor name is empty, return
				if (actorName === '') {
					return;
				}

				// create the input to enter the character name
				const input = $('<input />', {
					type: 'text',
					name: actorID,
					id: actorID,
					value:
						actorID in characterValues
							? characterValues[actorID]
							: '',
					placeholder: __('Enter Character Name'),
					autocomplete: false,
				});

				// add the listener to record the change
				input.on('change', recordChange);

				// create the label
				const label = $('<label />', {
					for: actorID,
					text: actorName,
				});

				// create the list item
				const item = $('<li />');
				item.append(label);
				item.append(input);

				// add the list item to the list
				characterNameList.append(item);
			});

			// render the list
			charactersName.html(characterNameList);
		};

		// run this on page load to set up the character name box
		setCharactersNameBox();

		// update the value and ui when selected box value changes
		actorInput.on('change', setCharactersNameBox);
	});
});

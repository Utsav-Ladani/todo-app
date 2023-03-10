/**
 * Character Name Handler
 */

// call anonymous function to avoid global scope access by user
(function () {
	// run on document ready
	document.addEventListener('DOMContentLoaded', () => {
		// get the translation function
		const { __ } = wp.i18n;

		// get DOM objects
		const actorInput = document.getElementById('rt-movie-meta-crew-actor');
		const charactersName = document.getElementById('rt-characters-name');
		const characterNameInput = document.getElementById(
			'rt-movie-meta-crew-actor-character-name'
		);

		// get the values from the input
		let characterRawValues = characterNameInput.value;

		if (characterRawValues !== '') {
			characterRawValues = JSON.parse(characterRawValues);
		} else {
			characterRawValues = {};
		}
		const characterValues = characterRawValues;

		// record the change in the input for character name
		const recordChange = (event) => {
			event.preventDefault();

			const { target } = event;

			// get the id
			const id = target.getAttribute('id');

			// get the list of actor ids
			const actorIDs = actorInput.value;

			// if the id is not in the list of actor ids, return
			if (id === '' || actorIDs.indexOf(id) === -1) {
				return;
			}

			// record the change
			characterValues[id] = target.value;

			// update the input
			const strValue = JSON.stringify(characterValues);
			characterNameInput.value = strValue;
		};

		// get the selected options from the select box in {id, name} format
		const getOptionsValueArray = (options) => {
			return [...options]
				.filter((option) => option.selected)
				.map((option) => ({
					id: option.value,
					name: option.text,
				}));
		};

		// add attributes to an element
		const addAttribute = (element, attributes) => {
			Object.keys(attributes).forEach((attribute) => {
				if (attribute === 'innerText') {
					element.innerText = attributes[attribute];
					return;
				}

				element.setAttribute(attribute, attributes[attribute]);
			});
		};

		// set the character name box
		const setCharactersNameBox = () => {
			const actors = getOptionsValueArray(actorInput.options);

			// create the empty list
			const characterNameList = document.createElement('ul');

			// loop through the actor ids
			actors.forEach(function (actor) {
				// if the actor id is empty, return
				if (actor.id === '' || actor.name === '') {
					return;
				}

				// create the input to enter the character name
				const input = document.createElement('input');
				const inputAttributes = {
					type: 'text',
					name: actor.name,
					id: actor.id,
					value:
						actor.id in characterValues
							? characterValues[actor.id]
							: '',
					placeholder: __('Enter Character Name'),
					autocomplete: 'off',
				};

				addAttribute(input, inputAttributes);

				// add the listener to record the change
				input.addEventListener('change', recordChange);

				// create the label
				const label = document.createElement('label');
				const labelAttributes = {
					for: actor.id,
					innerText: actor.name,
				};

				addAttribute(label, labelAttributes);

				// create the list item
				const item = document.createElement('li');
				item.append(label);
				item.append(input);

				// add the list item to the list
				characterNameList.append(item);
			});

			// render the list
			charactersName.replaceChildren(characterNameList);
		};

		// run this on page load to set up the character name box
		setCharactersNameBox();

		// update the value and ui when selected box value changes
		actorInput.addEventListener('change', setCharactersNameBox);
	});
})();

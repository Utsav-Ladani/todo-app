/**
 * Image Upload Handler
 *
 * @version 1.0.0
 */

// call anonymous function to avoid global scope access by user.
(function () {
	// run it after the DOM is ready
	document.addEventListener('DOMContentLoaded', () => {
		// get i18n functions
		const { __ } = wp.i18n;

		// get DOM objects
		const addImageButton = document.getElementById('add-images-custom-btn');
		const imagePreviewContainer = document.getElementById(
			'image-preview-container'
		);
		const imageInput = document.getElementById('rt-upload-images');
		const removeImageButton = document.getElementsByClassName(
			'rt-remove-image-btn'
		);

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

		// init frame object
		let frame = null;

		// remove image action handler
		const removeImageHandler = function (eventRemove) {
			eventRemove.preventDefault();

			const { target } = eventRemove;

			// get image id
			let id = target.getAttribute('data-image-id');
			id = parseInt(id);

			// remove image from value
			let value = imageInput.value;
			value = value.split(',');
			value = value.filter(function (item) {
				return item !== String(id);
			});

			// update input value
			value = value.join(',');
			imageInput.value = value;

			// remove image preview box
			target.parentNode.remove();
		};

		[...removeImageButton].forEach(function (item) {
			item.addEventListener('click', removeImageHandler);
		});

		// add listener on add image button
		addImageButton.addEventListener('click', function (event) {
			event.preventDefault();

			// open media frame if already exists
			if (frame) {
				frame.open();
				return;
			}

			// create media frame
			frame = wp.media({
				title: __('Select or Upload Images for Movie', 'movie-library'),
				button: {
					text: __('Add Images', 'movie-library'),
				},
				library: {
					type: 'image',
				},
				multiple: true,
			});

			// add listener on select images
			frame.on('select', function () {
				// get selected images
				const images = frame.state().get('selection').toJSON();

				// get value from input
				let rawValue = imageInput.value.split(',');

				// remove empty values
				rawValue = rawValue.filter(function (item) {
					return item !== '';
				});

				const imageIDs = rawValue;
				const imageTags = document.createDocumentFragment();

				// add images to preview box
				images.forEach(function (image) {
					// validate the URL
					let imageURL = null;
					try {
						imageURL = new URL(image.url);
					} catch (e) {
						// console.error('Wrong URL');
						return;
					}

					// check if image already exists
					if (imageIDs.includes(String(image.id))) {
						return;
					}

					// create image tag
					const img = document.createElement('img');
					const imgAttributes = {
						src: imageURL,
						class: 'widefat',
					};

					addAttribute(img, imgAttributes);

					// create remove button
					const btn = document.createElement('button');
					const btnAttributes = {
						class: 'button rt-remove-image-btn widefat',
						'data-image-id': image.id,
						innerText: __('Remove', 'movie-library'),
					};

					addAttribute(btn, btnAttributes);

					// add listener on remove button
					btn.addEventListener('click', removeImageHandler);

					// create image container
					const div = document.createElement('div');
					const divAttributes = {
						class: 'image-item',
						'data-image-id': image.id,
					};

					addAttribute(div, divAttributes);

					// append image and button to container
					div.append(img);
					div.append(btn);

					// add container to array
					imageTags.append(div);

					// add image id to array if it is valid
					if (parseInt(image.id)) {
						imageIDs.push(image.id);
					}
				});

				// update input value
				const value = imageIDs.join(',');
				imageInput.value = value;

				// append images to preview box
				imagePreviewContainer.append(imageTags);
			});

			// open media frame
			frame.open();
		});
	});
})();

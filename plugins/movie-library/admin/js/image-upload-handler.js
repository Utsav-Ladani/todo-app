/**
 * Image Upload Handler
 *
 * @version 1.0.0
 */

// call the function with jQuery
jQuery(function ($) {
	// run it after the DOM is ready
	$(document).ready(function () {
		// get i18n functions
		const { __ } = wp.i18n;

		// get DOM objects
		const metaBox = $('#movie-library-image-upload-handler');
		const addImageButton = metaBox.find('#add-images-custom-btn');
		const imagePreviewContainer = metaBox.find('#image-preview-container');
		const imageInput = metaBox.find('#rt-upload-images');
		const removeImageButton = metaBox.find('.rt-remove-image-btn');

		// init frame object
		let frame = null;

		// remove image action handler
		const removeImageHandler = function (eventRemove) {
			eventRemove.preventDefault();

			// get image id
			let id = $(this).attr('data-image-id');
			id = parseInt(id);

			// remove image from value
			let value = imageInput.val();
			value = value.split(',');
			value = value.filter(function (item) {
				return item !== String(id);
			});

			// update input value
			value = value.join(',');
			imageInput.val(value);

			// remove image preview box
			$(this).parent().remove();
		};

		// add listener on remove image button
		removeImageButton.on('click', removeImageHandler);

		// add listener on add image button
		addImageButton.on('click', function (event) {
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
				let rawValue = imageInput.val().split(',');

				// remove empty values
				rawValue = rawValue.filter(function (item) {
					return item !== '';
				});

				const imageIDs = rawValue;
				const imageTags = [];

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
					const img = $('<img />', {
						src: imageURL,
						class: 'widefat',
					});

					// create remove button
					const btn = $('<button />', {
						class: 'button rt-remove-image-btn widefat',
						'data-image-id': image.id,
						text: __('Remove', 'movie-library'),
					});

					// add listener on remove button
					btn.on('click', removeImageHandler);

					// create image container
					const div = $('<div />', {
						class: 'image-item',
						'data-image-id': image.id,
					});

					// append image and button to container
					div.append(img);
					div.append(btn);

					// add container to array
					imageTags.push(div);

					// add image id to array if it is valid
					if (parseInt(image.id)) {
						imageIDs.push(image.id);
					}
				});

				// update input value
				const value = imageIDs.join(',');
				imageInput.val(value);

				// append images to preview box
				imagePreviewContainer.append(imageTags);
			});

			// open media frame
			frame.open();
		});
	});
});

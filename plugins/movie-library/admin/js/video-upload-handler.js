/**
 * Video Upload Handler
 *
 * @version 1.0.0
 */

// call the function with jQuery
jQuery(function ($) {
	// run it after the DOM is ready
	$(document).ready(function ($) {
		// get i18n functions
		const { __ } = wp.i18n;

		// get DOM objects
		const metaBox = $('#movie-library-video-upload-handler');
		const addVideoButton = metaBox.find('#add-videos-custom-btn');
		const videoPreviewContainer = metaBox.find('#video-preview-container');
		const videoInput = metaBox.find('#rt-upload-videos');
		const removeVideoButton = metaBox.find('.rt-remove-video-btn');

		// init frame object
		let frame = null;

		// remove image action handler
		const removeVideoHandler = function (eventRemove) {
			eventRemove.preventDefault();

			let id = $(this).attr('data-video-id');
			id = parseInt(id);

			let value = videoInput.val();
			value = value.split(',');
			value = value.filter(function (item) {
				return item !== String(id);
			});

			value = value.join(',');
			videoInput.val(value);

			$(this).parent().remove();
		};

		// add listener on remove image button
		removeVideoButton.on('click', removeVideoHandler);

		// add listener on add image button
		addVideoButton.on('click', function (event) {
			event.preventDefault();

			// open media frame if already exists
			if (frame) {
				frame.open();
				return;
			}

			// create media frame
			frame = wp.media({
				title: __('Select or Upload Videos for Movie', 'movie-library'),
				button: {
					text: __('Add Videos', 'movie-library'),
				},
				library: {
					type: 'video',
				},
				multiple: true,
			});

			// add listener on select
			frame.on('select', function () {
				// get selected images
				const videos = frame.state().get('selection').toJSON();

				// get current value
				let rawValue = videoInput.val().split(',');

				// remove empty values
				rawValue = rawValue.filter(function (item) {
					return item !== '';
				});

				const videoIDs = rawValue;
				const videoTags = [];

				// create video preview container
				videos.forEach(function (video) {

					// check if video is already added
					let videoURL = null;
					try {
						videoURL = new URL(video.url);
					} catch (e) {
						// console.error('Wrong URL');
						return;
					}

					// check if video is already added
					if (videoIDs.includes(String(video.id))) {
						return;
					}

					// create video tag
					const videoTag = $('<video />', {
						controls: true,
						class: 'widefat',
					});

					// create source tag
					const source = $('<source />', {
						src: videoURL,
						type: video.mime,
					});

					// append source tag to video tag
					videoTag.append(source);

					// create remove button
					const btn = $('<button />', {
						class: 'button rt-remove-video-btn widefat',
						'data-video-id': video.id,
						text: __('Remove', 'movie-library'),
					});

					// add listener on remove button
					btn.on('click', removeVideoHandler);

					// create video container
					const div = $('<div />', {
						class: 'video-item',
						'data-video-id': video.id,
					});

					// append video tag and remove button to container
					div.append(videoTag);
					div.append(btn);

					// append container to video preview container
					videoTags.push(div);

					// add video id to video ids array
					if (parseInt(video.id)) {
						videoIDs.push(video.id);
					}
				});

				// add listener on remove image button
				const value = videoIDs.join(',');
				videoInput.val(value);

				// append video preview container to DOM
				videoPreviewContainer.append(videoTags);
			});

			// open media frame
			frame.open();
		});
	});
});

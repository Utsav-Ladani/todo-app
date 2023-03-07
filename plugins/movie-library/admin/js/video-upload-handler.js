/**
 * Video Upload Handler
 *
 * @version 1.0.0
 */

// call the function
(function () {
	// run it after the DOM is ready
	document.addEventListener('DOMContentLoaded', () => {
		// get i18n functions
		const { __ } = wp.i18n;

		// get DOM objects
		const metaBox = document.getElementById(
			'movie-library-video-upload-handler'
		);
		const addVideoButton = document.getElementById('add-videos-custom-btn');
		const videoPreviewContainer = document.getElementById(
			'video-preview-container'
		);
		const videoInput = document.getElementById('rt-upload-videos');
		const removeVideoButton = document.getElementsByClassName(
			'rt-remove-video-btn'
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
		const removeVideoHandler = function (eventRemove) {
			eventRemove.preventDefault();

			const { target } = eventRemove;

			let id = target.getAttribute('data-video-id');
			id = parseInt(id);

			let value = videoInput.value;
			value = value.split(',');
			value = value.filter(function (item) {
				return item !== String(id);
			});

			value = value.join(',');
			videoInput.value = value;

			target.parentNode.remove();
		};

		// add listener on remove image button
		[...removeVideoButton].forEach(function (item) {
			item.addEventListener('click', removeVideoHandler);
		});

		// add listener on add image button
		addVideoButton.addEventListener('click', function (event) {
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
				let rawValue = videoInput.value.split(',');

				// remove empty values
				rawValue = rawValue.filter(function (item) {
					return item !== '';
				});

				const videoIDs = rawValue;
				const videoTags = document.createDocumentFragment();

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
					const videoTag = document.createElement('video');
					const videoAttributes = {
						controls: true,
						class: 'widefat',
					};

					addAttribute(videoTag, videoAttributes);

					// create source tag
					const source = document.createElement('source');
					const sourceAttributes = {
						src: videoURL,
						type: video.mime,
					};

					addAttribute(source, sourceAttributes);

					// append source tag to video tag
					videoTag.append(source);

					// create remove button
					const btn = document.createElement('button');
					const btnAttributes = {
						class: 'button rt-remove-video-btn widefat',
						'data-video-id': video.id,
						innerText: __('Remove', 'movie-library'),
					};

					addAttribute(btn, btnAttributes);

					// add listener on remove button
					btn.addEventListener('click', removeVideoHandler);

					// create video container
					const div = document.createElement('div');
					const divAttributes = {
						class: 'video-item',
						'data-video-id': video.id,
					};

					addAttribute(div, divAttributes);

					// append video tag and remove button to container
					div.append(videoTag);
					div.append(btn);

					// append container to video preview container
					videoTags.append(div);

					// add video id to video ids array
					if (parseInt(video.id)) {
						videoIDs.push(video.id);
					}
				});

				// add listener on remove image button
				const value = videoIDs.join(',');
				videoInput.value = value;

				// append video preview container to DOM
				videoPreviewContainer.append(videoTags);
			});

			// open media frame
			frame.open();
		});
	});
})();

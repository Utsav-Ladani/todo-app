// run the anonymous function to avoid global scope access by user.
(function () {
	// run things after DOM is loaded.
	document.addEventListener('DOMContentLoaded', function () {
		const { __ } = wp.i18n;

		// validate social media links.
		const isValidUrl = (urlString) => {
			// regex for url validation.
			const urlPattern = new RegExp(
				'^(https?:\\/\\/)?' + // validate protocol
					'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // validate domain name
					'((\\d{1,3}\\.){3}\\d{1,3}))' + // validate OR ip (v4) address
					'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // validate port and path
					'(\\?[;&a-z\\d%_.~+=-]*)?' + // validate query string
					'(\\#[-a-z\\d_]*)?$',
				'i'
			);

			return !!urlPattern.test(urlString);
		};

		// validate social media links for given id.
		const validation = function (id) {
			const input = document.getElementById(id);

			// don't validate if input not found.
			if (!input) {
				return false;
			}

			// add listener to input
			input.addEventListener('input', function (e) {
				e.preventDefault();

				// get the DOM object
				const value = e.target.value;
				const valueError = document.getElementById(id + '-error');

				value.trim();

				// allowed only letters, commas, and spaces and show error if not.
				if (value === '' || !isValidUrl(value)) {
					valueError.style.display = 'block';
					valueError.innerText = __('Invalid URL.');
				} else {
					valueError.innerText = '';
					valueError.style.display = 'none';
				}
			});
		};

		// array of social media DOM ids.
		const socials = [
			'rt-person-meta-social-twitter',
			'rt-person-meta-social-facebook',
			'rt-person-meta-social-instagram',
			'rt-person-meta-social-web',
		];

		// validate each social media links.
		socials.forEach(function (id) {
			validation(id);
		});
	});
})();

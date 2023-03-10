// run anonymous function to avoid global scope access by user.
(function () {
	// run things after DOM is loaded.
	document.addEventListener('DOMContentLoaded', function () {
		const { __ } = wp.i18n;

		// validate birthplace
		(function () {
			const birthplaceInput = document.getElementById(
				'rt-person-meta-basic-birth-place'
			);

			// don't validate if input not found.
			if (!birthplaceInput) {
				return false;
			}

			// add listener to input
			birthplaceInput.addEventListener('input', function (e) {
				e.preventDefault();

				// get the DOM object
				const place = e.target.value;
				const placeError = document.getElementById(
					'rt-person-meta-basic-birth-place-error'
				);

				place.trim();

				// allowed only letters, commas, and spaces and show error if not.
				if (place === '' || !place.match(/^[a-zA-Z, ]+$/)) {
					placeError.style.display = 'block';
					placeError.innerText = __(
						'Birthplace only accepts letters, commas, and spaces.'
					);
				} else {
					placeError.innerText = '';
					placeError.style.display = 'none';
				}
			});
		})();
	});
})();

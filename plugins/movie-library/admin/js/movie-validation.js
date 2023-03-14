// call anonymous function to avoid global scope access by user.
(function () {
	// run things after DOM is loaded.
	document.addEventListener('DOMContentLoaded', function () {
		// get the i18n object from wp global object.
		const { __ } = wp.i18n;

		// valida the movie rating.
		(function () {
			const ratingInput = document.getElementById(
				'rt-movie-meta-basic-rating'
			);

			// return if input not found.
			if (!ratingInput) {
				return false;
			}

			// add event listener on input.
			ratingInput.addEventListener('input', function (e) {
				e.preventDefault();

				// get the DOM object
				const rating = e.target.value;
				const ratingError = document.getElementById(
					'rt-movie-meta-basic-rating-error'
				);

				// validate the rating and show error.
				if (
					rating === '' ||
					!rating.match(/^-?\d*(\.\d+)?$/) ||
					rating < 0 ||
					rating > 10
				) {
					ratingError.style.display = 'block';
					ratingError.innerText = __(
						'Rating must be between 0 and 10'
					);
				} else {
					ratingError.innerText = '';
					ratingError.style.display = 'none';
				}
			});
		})();

		// validate the movie runtime.
		(function () {
			const runtimeInput = document.getElementById(
				'rt-movie-meta-basic-runtime'
			);

			// return if input not found.
			if (!runtimeInput) {
				return false;
			}

			// add event listener on input.
			runtimeInput.addEventListener('input', function (e) {
				e.preventDefault();

				// get the DOM object
				const runtime = e.target.value;
				const runtimeError = document.getElementById(
					'rt-movie-meta-basic-runtime-error'
				);

				// validate the runtime and show error.
				if (
					runtime === '' ||
					!runtime.match(/^[0-9]+$/) ||
					parseInt(runtime) < 0 ||
					parseInt(runtime) > 5000
				) {
					runtimeError.style.display = 'block';
					runtimeError.innerText = __(
						'Runtime must be between 0 and 5000 minutes.'
					);
				} else {
					runtimeError.innerText = '';
					runtimeError.style.display = 'none';
				}
			});
		})();
	});
})();

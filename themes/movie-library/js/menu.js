/**
 * Toggle menu
 * It is used to toggle the menu when user clicks on the menu button.
 *
 * @package
 */

// use IIFE to avoid polluting the global scope
(function () {
	// wait for the DOM to be ready
	document.addEventListener('DOMContentLoaded', function () {
		// get the menu button
		const menuBtn = document.getElementById('mobile-toggle-btn');

		if (!menuBtn) return;

		// store the status of the menu
		let isClose = true;

		// add event listener to the menu button
		menuBtn.addEventListener('click', function () {
			// get all the elements to toggle
			const elementsToToggle =
				document.getElementsByClassName('mobile-toggle');

			// toggle the elements
			for (let i = 0; i < elementsToToggle.length; i++) {
				elementsToToggle[i].classList.toggle('close');
			}

			// toggle the menu button
			menuBtn.classList.toggle('close-svg');
			menuBtn.classList.toggle('bar-svg');

			// toggle the status of the menu
			isClose = !isClose;
		});
	});

	// wait for the window to be loaded
	document.addEventListener('DOMContentLoaded', function () {
		// get the menu button
		const searchMenuBtn = document.getElementById('search-form-btn');

		if (!searchMenuBtn) return;

		// store the status of the menu
		let isClose = true;

		// add event listener to the menu button
		searchMenuBtn.addEventListener('click', function () {
			// get all the elements to toggle
			const elementsToToggle =
				document.getElementsByClassName('toggle-search-form');

			if (!elementsToToggle) return;

			// toggle the elements
			for (let i = 0; i < elementsToToggle.length; i++) {
				elementsToToggle[i].classList.toggle('search-close');
			}

			// toggle the status of the menu
			isClose = !isClose;
		});
	});

	// wait for the window to be loaded
	document.addEventListener('DOMContentLoaded', function () {
		// get the menu button
		const searchMenuBtn = document.getElementById('search-form-btn-mobile');

		if (!searchMenuBtn) return;

		// store the status of the menu
		let isClose = true;

		// add event listener to the menu button
		searchMenuBtn.addEventListener('click', function () {
			// get all the elements to toggle
			const elementsToToggle = document.getElementsByClassName(
				'toggle-search-form-mobile'
			);

			if (!elementsToToggle) return;

			// toggle the elements
			for (let i = 0; i < elementsToToggle.length; i++) {
				elementsToToggle[i].classList.toggle('search-close');
			}

			// toggle the status of the menu
			isClose = !isClose;
		});
	});
})();

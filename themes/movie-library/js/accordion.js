/**
 * Accordion for the drop-down menu in the header
 *
 * @package
 */

// use IIFE to avoid polluting the global scope
(function () {
	// wait for the DOM to be ready
	document.addEventListener('DOMContentLoaded', function () {
		// get all the drop-down menus
		const menus = document.getElementsByClassName('drop-down-menu');

		// if there are no drop-down menus, return
		for (let i = 0; i < menus.length; i++) {
			// add event listener to each drop-down menu
			menus[i].addEventListener('click', function () {
				// get the arrow and the menu
				const menuBtn = this.getElementsByClassName(
					'drop-down-arrow-svg'
				);
				const menuContent = this.getElementsByTagName('nav');

				// if there is no arrow or menu, return
				if (!menuBtn || !menuContent) return;

				// toggle the arrow and the menu
				menuBtn[0].classList.toggle('rotate-180');
				menuContent[0].classList.toggle('close-drop-down-menu');
			});
		}
	});
})();

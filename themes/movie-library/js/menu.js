/**
 * Toggle menu
 * It is used to toggle the menu when user clicks on the menu button.
 *
 * @package Movie Library
 */

// use IIFE to avoid polluting the global scope
(
    function() {
        // wait for the DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // get the menu button
            const menuBtn = document.getElementById('mobile-toggle-btn');

            // store the status of the menu
            let isClose = true;

            // add event listener to the menu button
            menuBtn.addEventListener('click', function() {
                // get all the elements to toggle
                const elementsToToggle = document.getElementsByClassName('mobile-toggle');

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
    }
)();

/**
 * Slider for the home page
 * It is used to display the slider on the home page.
 * Slider slides automatically after every 5 seconds.
 */

// use IIFE to avoid polluting the global scope
(
    function() {
        // wait for the DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {

            // set the index of the slide to 0
            let slideIndex = 0;

            // get all the slides and dots
            const slides = document.getElementsByClassName("slide");
            const dots = document.getElementsByClassName("dot");

            // add event listener to each dot
            for (let i = 0; i < dots.length; i++) {
                dots[i].addEventListener("click", function() {
                    showSlides(i);
                });
            }

            // function to show the slides
            function showSlides(n = slideIndex) {
                // hide all slides
                for (let i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }

                // remove active class from all dots
                for (let i = 0; i < dots.length; i++) {
                    dots[i].classList.remove("active");
                }

                // set the slide number.
                slideIndex = n;

                // show the slide and add active class to the dot
                slides[slideIndex].style.display = "unset";
                dots[slideIndex].classList.add("active");

                // increment the slide index
                slideIndex++;
                if (slideIndex >= slides.length) {
                    slideIndex = 0;
                }

                // Change slide after every 5 seconds
                // setTimeout(showSlides, 5000);
            }

            // init the slide show
            showSlides();
        });
    }
)();

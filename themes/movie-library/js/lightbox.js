/**
 * Lightbox
 * It is used to display video in lightbox when user clicks on video button of any video.
 *
 * @package Movie Library
 */

// use IIFE to avoid polluting the global scope
(function() {
    // wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // get all the elements
        const lightbox = document.getElementById('lightbox');
        const lightboxVideo = document.getElementById('lightbox-video');
        const videoBtn = document.getElementsByClassName('video-btn');
        const closeBtn = document.getElementById('lightbox-close-btn');

        // if there are no elements, return
        if( !lightbox || !lightboxVideo || !videoBtn || !closeBtn) {
            return;
        }

        // add event listener to close button
        closeBtn.addEventListener('click', function() {
            // remove the video and hide the lightbox
            lightbox.classList.add('display-none');
            lightboxVideo.replaceChildren();
        });

        // add event listener to each video button
        for(let i = 0; i < videoBtn.length; i++) {
            // add event listener to each video button
            videoBtn[i].addEventListener('click', function() {

                // create video element
                const video = document.createElement('video');
                video.setAttribute('controls', 'controls');
                video.classList.add('video-player');
                video.setAttribute('autoplay', 'autoplay');

                // get the video source and set it to the video element
                const src = this.getAttribute('video-src');
                video.setAttribute('src', src);

                // add the video to the lightbox
                lightboxVideo.replaceChildren(video);
                lightbox.classList.remove('display-none');
            });
        }
    });
})();

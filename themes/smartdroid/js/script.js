/**
 * Script for SmartDroid theme
 * It contains the common functions for the theme
 *
 * @package SmartDroid
 */

// wrapper function to avoid global variables conflicts
(function() {
    // toggle search form
    document.addEventListener('DOMContentLoaded', function() {
        const searchIcon = document.querySelector('.search-icon');

        // return if search icon is not found
        if( ! searchIcon ) return;

        // add event listener on search icon to toggle the search form
        searchIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const searchIconI = searchIcon.querySelector('.fa');
            const searchForm = document.querySelector('.search-form');

            // return if search icon or search form is not found
            if( ! searchIconI || ! searchForm ) return;

            // toggle the search icon and search form
            searchIconI.classList.toggle('fa-search');
            searchIconI.classList.toggle('fa-close');
            searchForm.classList.toggle('hide');
        });
    });

    // toggle mobile sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const closeIcon = document.querySelector('.close-icon');
        const menuIcon = document.querySelector('.menu-icon');

        // return if close icon or menu icon is not found
        if( ! closeIcon || ! menuIcon ) return;

        // add event listener on menu icon to toggle the mobile sidebar
        menuIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const sidebarWrapper = document.querySelector('.mobile-sidebar-wrapper');
            const sidebar = document.querySelector('.mobile-sidebar');

            if( ! sidebarWrapper || ! sidebar ) return;

            // add background first and then menu
            sidebarWrapper.classList.toggle('mobile-overlay');
            sidebar.classList.toggle('slide-out');
        });

        // add event listener on close icon to toggle the mobile sidebar
        closeIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const sidebarWrapper = document.querySelector('.mobile-sidebar-wrapper');
            const sidebar = document.querySelector('.mobile-sidebar');

            if( ! sidebar ) return;

            // remove menu first and then background
            sidebar.classList.toggle('slide-out');

            // remove background after css animation over
            setTimeout(function() {
                sidebarWrapper.classList.toggle('mobile-overlay');
            }, 200);
        });
    });

})();

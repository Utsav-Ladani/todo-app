/**
 * Script for SmartDroid theme
 * It contains the common functions for the theme
 *
 * @package SmartDroid
 */

(function() {
    // toggle search form
    document.addEventListener('DOMContentLoaded', function() {
        const searchIcon = document.querySelector('.search-icon');

        if( ! searchIcon ) return;

        searchIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const searchIconI = searchIcon.querySelector('.fa');
            const searchForm = document.querySelector('.search-form');

            if( ! searchIconI || ! searchForm ) return;

            searchIconI.classList.toggle('fa-search');
            searchIconI.classList.toggle('fa-close');

            searchForm.classList.toggle('hide');
        });
    });

    // toggle mobile sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const closeIcon = document.querySelector('.close-icon');
        const menuIcon = document.querySelector('.menu-icon');

        if( ! closeIcon || ! menuIcon ) return;

        menuIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const sidebarWrapper = document.querySelector('.mobile-sidebar-wrapper');
            const sidebar = document.querySelector('.mobile-sidebar');

            if( ! sidebarWrapper || ! sidebar ) return;

            sidebarWrapper.classList.toggle('mobile-overlay');
            sidebar.classList.toggle('slide-out');
        });

        closeIcon.addEventListener('click', function(e) {
            e.stopPropagation();

            const sidebarWrapper = document.querySelector('.mobile-sidebar-wrapper');
            const sidebar = document.querySelector('.mobile-sidebar');

            if( ! sidebar ) return;

            sidebar.classList.toggle('slide-out');

            setTimeout(function() {
                sidebarWrapper.classList.toggle('mobile-overlay');
            }, 200);
        });
    });

})();
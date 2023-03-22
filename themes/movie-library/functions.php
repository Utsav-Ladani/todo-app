<?php
/**
 * Movie Library theme functions.php.
 * It contains all the functions and hooks to enqueue styles and scripts.
 * It also registers the menus.
 *
 * @package Movie Library
 */

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

// check before declaring the function.
if ( ! function_exists( 'enqueue_style' ) ) {
	/**
	 * Enqueue all stylesheets.
	 */
	function enqueue_style() {
		// dequeue parent theme's style.
		wp_dequeue_style( 'twenty-twenty-one-style' );

		// enqueue main stylesheet.
		wp_enqueue_style(
			'style',
			get_stylesheet_uri(),
			array(),
			filemtime( get_stylesheet_directory() . '/style.css' )
		);

		wp_enqueue_style(
			'header-footer-style',
			get_stylesheet_directory_uri() . '/css/header-footer.css',
			array(),
			filemtime( get_stylesheet_directory() . '/css/header-footer.css' )
		);

		// enqueue style for movie archive page.
		if ( is_post_type_archive( Movie::SLUG ) ) {
			wp_enqueue_style(
				'movie-archive-style',
				get_stylesheet_directory_uri() . '/css/archive-movie.css',
				array(),
				filemtime( get_stylesheet_directory() . '/css/archive-movie.css' )
			);
		}

		// enqueue style for person archive page.
		if ( is_post_type_archive( Person::SLUG ) ) {
			wp_enqueue_style(
				'person-archive-style',
				get_stylesheet_directory_uri() . '/css/archive-person.css',
				array(),
				filemtime( get_stylesheet_directory() . '/css/archive-person.css' )
			);
		}

		// enqueue style for single movie page.
		if ( is_singular( Movie::SLUG ) ) {
			wp_enqueue_style(
				'single-movie-style',
				get_stylesheet_directory_uri() . '/css/single-movie.css',
				array(),
				filemtime( get_stylesheet_directory() . '/css/single-movie.css' )
			);
		}

		// enqueue style for single person page.
		if ( is_singular( Person::SLUG ) ) {
			wp_enqueue_style(
				'single-person-style',
				get_stylesheet_directory_uri() . '/css/single-person.css',
				array(),
				filemtime( get_stylesheet_directory() . '/css/single-person.css' )
			);
		}
	}
}

// use lower priority to override the parent theme's style.
add_action( 'wp_enqueue_scripts', 'enqueue_style', 20 );

// check before declaring the function.
if ( ! function_exists( 'enqueue_script' ) ) {
	/**
	 * Enqueue all scripts.
	 */
	function enqueue_script() {
		// slider script.
		if ( is_post_type_archive( Movie::SLUG ) ) {
			wp_enqueue_script(
				'slider-script',
				get_stylesheet_directory_uri() . '/js/slider.js',
				array(),
				filemtime( get_stylesheet_directory() . '/js/slider.js' ),
				true
			);
		}

		// enqueue main script.
		wp_enqueue_script(
			'menu-script',
			get_stylesheet_directory_uri() . '/js/menu.js',
			array(),
			filemtime( get_stylesheet_directory() . '/js/menu.js' ),
			true
		);

		// enqueue accordion script for header menu.
		wp_enqueue_script(
			'accordion-script',
			get_stylesheet_directory_uri() . '/js/accordion.js',
			array(),
			filemtime( get_stylesheet_directory() . '/js/accordion.js' ),
			true
		);

		// enqueue lightbox script to play the video.
		if ( is_single() ) {
			wp_enqueue_script(
				'lightbox-script',
				get_stylesheet_directory_uri() . '/js/lightbox.js',
				array(),
				filemtime( get_stylesheet_directory() . '/js/lightbox.js' ),
				true
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_script' );


// check before declaring the function.
if ( ! function_exists( 'setup_movie_library_theme' ) ) {
	/**
	 * Set up the theme.
	 * Register menus, load translation files, add theme supports, etc.
	 *
	 * @return void
	 * @hooks after_setup_theme
	 */
	function setup_movie_library_theme() {
		// load translation files.
		load_theme_textdomain( 'movie-library', get_stylesheet_directory() . '/languages' );

		// primary menus.
		register_nav_menu( 'primary-menu-movie', __( 'Primary Menu For Movie', 'movie-library' ) );

		// footer menus.
		register_nav_menu( 'footer-menu-company', __( 'Company Menu', 'movie-library' ) );
		register_nav_menu( 'footer-menu-explore', __( 'Explore Menu', 'movie-library' ) );

		// quick links menus shown in the sidebar.
		register_nav_menu( 'quick-link-menu-movie', __( 'Quick Links Movie', 'movie-library' ) );
		register_nav_menu( 'quick-link-menu-person', __( 'Quick Links Person', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'setup_movie_library_theme' );

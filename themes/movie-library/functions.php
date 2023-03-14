<?php
/**
 * Movie Library theme functions.php.
 * It contains all the functions and hooks to enqueue styles and scripts.
 * It also registers the menus.
 *
 * @package Movie Library
 */

// check before declaring the function.
if ( ! function_exists( 'enqueue_style' ) ) {
	/**
	 * Enqueue all stylesheets.
	 */
	function enqueue_style() {

		// enqueue main stylesheet.
		wp_enqueue_style(
			'style',
			get_stylesheet_uri(),
			array(),
			filemtime( get_stylesheet_directory() . '/style.css' )
		);

		// enqueue mobile stylesheet.
		wp_enqueue_style(
			'style-mobile',
			get_stylesheet_directory_uri() . '/style-mobile.css',
			array(),
			filemtime( get_stylesheet_directory() . '/style-mobile.css' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_style' );

// check before declaring the function.
if ( ! function_exists( 'enqueue_script' ) ) {
	/**
	 * Enqueue all scripts.
	 */
	function enqueue_script() {
		// slider script.
		if ( get_post_type() === 'rt-movie' && is_archive() ) {
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
		wp_enqueue_script(
			'lightbox-script',
			get_stylesheet_directory_uri() . '/js/lightbox.js',
			array(),
			filemtime( get_stylesheet_directory() . '/js/lightbox.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_script' );

// check before declaring the function.
if ( ! function_exists( 'register_primary_menu_for_movie' ) ) {
	/**
	 * Register primary menu for movie.
	 */
	function register_primary_menu_for_movie() {
		register_nav_menu( 'primary-menu-movie', __( 'Primary Menu For Movie', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'register_primary_menu_for_movie' );

// check before declaring the function.
if ( ! function_exists( 'register_footer_menu_for_company' ) ) {
	/**
	 * Register footer menu for company.
	 */
	function register_footer_menu_for_company() {
		register_nav_menu( 'footer-menu-company', __( 'Company Menu', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'register_footer_menu_for_company' );

// check before declaring the function.
if ( ! function_exists( 'register_footer_menu_for_explore' ) ) {
	/**
	 * Register footer menu for explore.
	 */
	function register_footer_menu_for_explore() {
		register_nav_menu( 'footer-menu-explore', __( 'Explore Menu', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'register_footer_menu_for_explore' );

// check before declaring the function.
if ( ! function_exists( 'register_quick_link_menu_for_movie' ) ) {
	/**
	 * Register quick link menu for movie.
	 */
	function register_quick_link_menu_for_movie() {
		register_nav_menu( 'quick-link-menu-movie', __( 'Quick Links Movie', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'register_quick_link_menu_for_movie' );

// check before declaring the function.
if ( ! function_exists( 'register_quick_link_menu_for_person' ) ) {
	/**
	 * Register quick link menu for person.
	 */
	function register_quick_link_menu_for_person() {
		register_nav_menu( 'quick-link-menu-person', __( 'Quick Links Person', 'movie-library' ) );
	}
}
add_action( 'after_setup_theme', 'register_quick_link_menu_for_person' );

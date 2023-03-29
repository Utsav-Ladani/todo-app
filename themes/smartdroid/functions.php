<?php
/**
 * Functions and definitions
 *
 * @package WordPress
 */

if ( ! function_exists( 'setup_smartdroid_theme' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @return void
	 */
	function setup_smartdroid_theme() {
		add_theme_support( 'custom-logo' );

		// Add support for post thumbnails.
		add_theme_support( 'post-thumbnails' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		register_nav_menus(
			array(
				'primary-menu' => esc_html__( 'Primary Menu', 'smartdroid' ),
				'footer-menu'  => esc_html__( 'Footer Menu', 'smartdroid' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'setup_smartdroid_theme' );

if ( ! function_exists( 'enqueue_smartdroid_styles' ) ) {
	/**
	 * Enqueue styles.
	 *
	 * @return void
	 */
	function enqueue_smartdroid_styles() : void {
		wp_enqueue_style(
			'smartdroid-style',
			get_template_directory_uri() . '/style.css',
			array(),
			filemtime( get_template_directory() . '/style.css' )
		);

		// enqueue style for archive page.
		if ( is_archive() ) {
			wp_enqueue_style(
				'smartdroid-archive-style',
				get_template_directory_uri() . '/css/archive-style.css',
				array(),
				filemtime( get_template_directory() . '/css/archive-style.css' )
			);
		}

		if ( is_singular() ) {
			wp_enqueue_style(
				'smartdroid-single-style',
				get_template_directory_uri() . '/css/single-style.css',
				array(),
				filemtime( get_template_directory() . '/css/single-style.css' )
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_smartdroid_styles' );


if ( ! function_exists( 'enqueue_smartdroid_scripts' ) ) {
	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	function enqueue_smartdroid_scripts() : void {
		wp_enqueue_script(
			'smartdroid-script',
			get_template_directory_uri() . '/js/script.js',
			array(),
			filemtime( get_template_directory() . '/js/script.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_smartdroid_scripts' );

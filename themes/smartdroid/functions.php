<?php
/**
* Functions and definitions
*
* @package WordPress
*/

if( ! function_exists( 'setup_smartdroid_theme' ) ) {
	function setup_smartdroid_theme() {
		add_theme_support( 'custom-logo' );

		register_nav_menus(
			array(
				'primary-menu' => esc_html__( 'Primary Menu', 'smartdroid' ),
				'footer-menu' => esc_html__( 'Footer Menu', 'smartdroid' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'setup_smartdroid_theme' );

if( ! function_exists( 'enqueue_smartdroid_styles' ) ) {
	function enqueue_smartdroid_styles() : void {
		wp_enqueue_style(
			'smartdroid-style',
			get_template_directory_uri(). '/style.css',
			array(),
			filemtime( get_template_directory() . '/style.css' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_smartdroid_styles' );


if( ! function_exists( 'enqueue_smartdroid_scripts' ) ) {
	function enqueue_smartdroid_scripts() : void {
		wp_enqueue_script(
			'smartdroid-script',
			get_template_directory_uri(). '/js/script.js',
			array(),
			filemtime( get_template_directory() . '/js/script.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_smartdroid_scripts' );

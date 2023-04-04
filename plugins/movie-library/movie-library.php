<?php
/**
 * Plugin Name:       Movie Library
 * Description:       Plugin to manage movie library
 * Version:           1.0.0
 * Requires at least: 5.9
 * Requires PHP:      7.3
 * Author:            rtCamp
 * Author URI:        https://github.com/rtCamp/
 * Text Domain:       movie-library
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Movie_Library
 */

namespace Movie_Library;

use Movie_Library\APIs\Movie_Library_Metadata_API;

require_once __DIR__ . '/class-movie-library.php';

Movie_Library::init();
Movie_Library_Metadata_API::init();


add_action( 'init', function() {
//	\Movie_Library\Movie_Library_Update::update();
//	die();

//	$result = Movie_Library_Metadata_API::get_movie_meta( 68, 'rt-movie-meta-basic-release-date' );
//	print_r( $result );

//	$result = Movie_Library_Metadata_API::add_movie_meta( 2, 'rt-movie-meta-basic-release-date', '999', true );
//	print_r( $result );

//	$result = Movie_Library_Metadata_API::delete_movie_meta( 68, 'rt-movie-meta-basic-release-date' );
//	print_r( $result );

//	$result = Movie_Library_Metadata_API::update_movie_meta( 72, 'rt-movie-meta-basic-release-date', '12-12' );
//	print_r( $result );

	die();
} );

// register activation hook.
register_activation_hook( __FILE__, array( Movie_Library::class, 'activate' ) );

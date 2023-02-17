<?php
/**
 * Movie Library constants
 *
 * Declare all constants here.
 *
 * @package MovieLibrary
 * @author  Utsav Ladani
 */

namespace Movie_Library;

// define constants below.
/**
 * @const string MOVIE_LIBRARY_VERSION Plugin version.
 */
define('MOVIE_LIBRARY_VERSION', '1.0.0');

/**
 * @const string MOVIE_LIBRARY_PLUGIN_DIR Plugin directory path.
 */
define( "MOVIE_LIBRARY_PLUGIN_DIR", plugin_dir_path(__FILE__) );

/**
 * @const string MOVIE_LIBRARY_NAMESPACE Plugin namespace.
 */
define( "MOVIE_LIBRARY_NAMESPACE", __NAMESPACE__ );

/**
 * @const string MOVIE_LIBRARY_PLUGIN_URL Plugin directory URL.
 */
define( "MOVIE_LIBRARY_PLUGIN_URL", plugin_dir_url(__FILE__) );
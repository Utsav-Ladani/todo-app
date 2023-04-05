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

require_once __DIR__ . '/class-movie-library.php';

// initialize plugin.
//Movie_Library::init();

// initialize update functionality.
//Movie_Library_Update::init();

// register activation hook.
register_activation_hook( __FILE__, array( Movie_Library::class, 'activate' ) );

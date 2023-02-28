<?php
/**
 * Movie Library
 *
 * @package           MovieLibrary
 * @author            rtCamp
 * @copyright         2023 by rtCamp
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Movie Library
 * Plugin URI:        https://example.com/movie-library
 * Description:       Plugin to manage movie library
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Author:            rtCamp
 * Author URI:        https://github.com/rtCamp/
 * Text Domain:       movie-library
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */

namespace Movie_Library;

require_once __DIR__ . '/class-movie-library.php';

\Movie_Library\Movie_Library::init();

use Movie_Library\Movie_Library;

// register activation hook.
register_activation_hook( __FILE__, array( Movie_Library::class, 'activate' ) );

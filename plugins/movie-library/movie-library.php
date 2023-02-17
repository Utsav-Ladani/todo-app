<?php
/**
 * Movie Library
 *
 * @package           MovieLibrary
 * @author            Utsav Ladani
 * @copyright         2023 by Utsav Ladani
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Movie Library
 * Plugin URI:        https://example.com/movie-library
 * Description:       Plugin to manage movie library
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      8.0
 * Author:            Utsav Ladani
 * Author URI:        https://github.com/Utsav-Ladani/
 * Text Domain:       movie-library
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */

namespace Movie_Library;

require_once __DIR__ . '/plugin-constant.php';
require_once MOVIE_LIBRARY_PLUGIN_DIR . 'autoloader.php';

// custom post type
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

// hierarchical taxonomy
use Movie_Library\Taxonomy\Hierarchical\Genre;
use Movie_Library\Taxonomy\Hierarchical\Label;
use Movie_Library\Taxonomy\Hierarchical\Language;
use Movie_Library\Taxonomy\Hierarchical\Production_Company;

/**
 * Main class of the plugin.
 *
 * It registers the autoloader and activation hook.
 */
class Movie_Library {
	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public static function init() : void {
		// register the autoloader.
		Autoloader::register();

		// register activation hook.
		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );

		// add custom post type.
		Movie::init();
		Person::init();

		// add custom taxonomy.
		Genre::init();
		Label::init();
		Language::init();
		Production_Company::init();
	}

	/**
	 * Do something on activation.
	 *
	 * @return void
	 */
	public static function activate() : void {

		// register custom post type to flush rewrite rules.
		Movie::register_movie_post_type();
		Person::register_person_post_type();

		// register custom taxonomy to flush rewrite rules.
		Genre::register_genre_taxonomy();
		Label::register_label_taxonomy();
		Language::register_language_taxonomy();
		Production_Company::register_production_company_taxonomy();

		// flush rewrite rules.
		flush_rewrite_rules();
	}
}

Movie_Library::init();

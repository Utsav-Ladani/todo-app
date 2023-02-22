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
use Movie_Library\Taxonomy\Hierarchical\Career;

// non-hierarchical taxonomy
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;

//non-hierarchical shadow taxonomy
use Movie_Library\Shadow_Taxonomy\Non_Hierarchical\Shadow_Person;

// meta box
use Movie_Library\Meta_Box\Basic_Meta_Box;
use Movie_Library\Meta_Box\Crew_Meta_Box;
use Movie_Library\Meta_Box\Person_Basic_Meta_Box;
use Movie_Library\Meta_Box\Social_Meta_Box;
use Movie_Library\Meta_Box\Images_Meta_Box;
use Movie_Library\Meta_Box\Videos_Meta_Box;

// shortcode
use Movie_Library\Shortcode\Movie_Shortcode;
use Movie_Library\Shortcode\Person_Shortcode;

// setting
use Movie_Library\Setting\Setting;

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

		// add hierarchical taxonomy.
		Genre::init();
		Label::init();
		Language::init();
		Production_Company::init();
		Career::init();

		// add non-hierarchical taxonomy.
		Tag::init();

		// add non-hierarchical shadow taxonomy.
		Shadow_Person::init();

		// add meta box.
		Basic_Meta_Box::init();
		Crew_Meta_Box::init();
		Person_Basic_Meta_Box::init();
		Social_Meta_Box::init();
		Images_Meta_Box::init();
		Videos_Meta_Box::init();

		// add shortcode.
		Movie_Shortcode::init();
		Person_Shortcode::init();

		// add setting.
		Setting::init();
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

		// register hierarchical taxonomy to flush rewrite rules.
		Genre::register_genre_taxonomy();
		Label::register_label_taxonomy();
		Language::register_language_taxonomy();
		Production_Company::register_production_company_taxonomy();
		Career::register_career_taxonomy();

		// register non-hierarchical taxonomy to flush rewrite rules.
		Tag::register_tag_taxonomy();

		// register non-hierarchical shadow taxonomy to flush rewrite rules.
		Shadow_Person::register_shadow_person_taxonomy();

		// flush rewrite rules.
		flush_rewrite_rules();
	}
}

Movie_Library::init();

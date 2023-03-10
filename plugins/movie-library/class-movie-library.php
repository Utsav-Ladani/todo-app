<?php
/**
 * Main class of the plugin.
 * It registers the autoloader and activation hook.
 *
 * @package   Movie_Library
 */

namespace Movie_Library;

require_once __DIR__ . '/plugin-constant.php';
require_once MOVIE_LIBRARY_PLUGIN_DIR . 'class-autoloader.php';

// custom post type.
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

// hierarchical taxonomy.
use Movie_Library\Taxonomy\Hierarchical\Genre;
use Movie_Library\Taxonomy\Hierarchical\Label;
use Movie_Library\Taxonomy\Hierarchical\Language;
use Movie_Library\Taxonomy\Hierarchical\Production_Company;
use Movie_Library\Taxonomy\Hierarchical\Career;

// non-hierarchical taxonomy.
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;

// non-hierarchical shadow taxonomy.
use Movie_Library\Shadow_Taxonomy\Non_Hierarchical\Shadow_Person;

// meta box.
use Movie_Library\Meta_Box\Basic_Meta_Box;
use Movie_Library\Meta_Box\Crew_Meta_Box;
use Movie_Library\Meta_Box\Person_Basic_Meta_Box;
use Movie_Library\Meta_Box\Social_Meta_Box;
use Movie_Library\Meta_Box\Images_Meta_Box;
use Movie_Library\Meta_Box\Videos_Meta_Box;

// shortcode.
use Movie_Library\Shortcode\Movie_Shortcode;
use Movie_Library\Shortcode\Person_Shortcode;

// setting.
use Movie_Library\Setting\Setting;

/**
 * Main class of the plugin.
 *
 * It registers the autoloader and activation hook.
 */
abstract class Movie_Library {
	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public static function init() : void {
		// register the autoloader.
		Autoloader::register();

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

<?php
/**
 * Movie Manager Role
 * Movie Manager can manage movie and person custom post types.
 * It also can manage movie and person taxonomies.
 *
 * @package Movie Library
 */

namespace Movie_Library\Role;

// CPT.
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

// Taxonomy.
use Movie_Library\Taxonomy\Hierarchical\Genre;
use Movie_Library\Taxonomy\Hierarchical\Label;
use Movie_Library\Taxonomy\Hierarchical\Language;
use Movie_Library\Taxonomy\Hierarchical\Production_Company;
use Movie_Library\Taxonomy\Hierarchical\Career;
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;


/**
 * Class Movie_Manager
 * It adds a new role and the capabilities to manage movie and person custom post type and its taxonomies.
 */
abstract class Movie_Manager {
	/**
	 * Add Movie Manager Role
	 * Add assign capabilities to movie manager and admin.
	 *
	 * @return void
	 */
	public static function add_movie_manager_role() : void {
		// get all capabilities of movie library.
		$capabilities = self::get_movie_library_capabilities();

		// get admin role.
		$admin_role = get_role( 'administrator' );

		// add capabilities to admin role.
		foreach ( $capabilities as $capability ) {
			$admin_role->add_cap( $capability );
		}

		// set each capability to true.
		$capabilities = array_fill_keys( $capabilities, true );

		// add movie manager role.
		add_role( 'movie-manager', __( 'Movie Manager', 'movie-library' ), $capabilities );
	}

	/**
	 * Get Movie Library Capabilities
	 * It returns array of all capabilities that movie manager and admin required.
	 *
	 * @return array
	 */
	public static function get_movie_library_capabilities() : array {
		$capabilities = array();

		// merge all capabilities.
		$capabilities = array_merge( $capabilities, self::get_capabilities_of_cpts() );
		$capabilities = array_merge( $capabilities, self::get_capabilities_of_taxonomies() );
		$capabilities = array_merge( $capabilities, self::get_custom_capabilities() );

		// remove duplicate capabilities.
		return array_unique( $capabilities );
	}

	/**
	 * Get Capabilities of Custom Post Types
	 * It return array of capabilities of movie and person custom post types.
	 *
	 * @return array
	 */
	public static function get_capabilities_of_cpts() : array {
		// custom post type slugs.
		$custom_post_types = array( Movie::SLUG, Person::SLUG );

		$capabilities = array();

		// get capabilities of each custom post type.
		foreach ( $custom_post_types as $custom_post_type ) {
			// get object of custom post type.
			$custom_post_type_obj = get_post_type_object( $custom_post_type );

			// return if object it invalid.
			if ( ! $custom_post_type_obj instanceof \WP_Post_Type ) {
				continue;
			}

			// convert standard class object to array.
			$custom_post_type_caps = (array) $custom_post_type_obj->cap;

			// merge capabilities.
			$capabilities = array_merge( $capabilities, array_values( $custom_post_type_caps ) );
		}

		// remove duplicate capabilities.
		return array_unique( $capabilities );
	}

	/**
	 * Get Capabilities of Taxonomies
	 * It returns array of capabilities of movie and person taxonomies.
	 *
	 * @return array
	 */
	public static function get_capabilities_of_taxonomies() : array {
		// taxonomy slugs.
		$taxonomies = array( Genre::SLUG, Label::SLUG, Language::SLUG, Production_Company::SLUG, Tag::SLUG, Career::SLUG );

		$capabilities = array();

		// get capabilities of each taxonomy.
		foreach ( $taxonomies as $taxonomy ) {
			// get object of taxonomy.
			$taxonomy_obj = get_taxonomy( $taxonomy );

			// return if object it invalid.
			if ( ! $taxonomy_obj instanceof \WP_Taxonomy ) {
				continue;
			}

			// convert standard class object to array.
			$taxonomy_caps = (array) $taxonomy_obj->cap;

			// merge capabilities.
			$capabilities = array_merge( $capabilities, array_values( $taxonomy_caps ) );
		}

		// remove duplicate capabilities.
		return array_unique( $capabilities );
	}

	/**
	 * Remove Movie Manager Role
	 * Remove movie manager role and revoke the admin capabilities to manage movie and person custom post type and its taxonomies.
	 *
	 * @return void
	 */
	public static function remove_movie_manager_role() : void {
		// get all capabilities of movie library.
		$capabilities = self::get_movie_library_capabilities();

		// get admin role.
		$admin_role = get_role( 'administrator' );

		// remove capabilities from admin role.
		foreach ( $capabilities as $capability ) {
			$admin_role->remove_cap( $capability );
		}

		// remove movie manager role.
		remove_role( 'movie-manager' );
	}

	/**
	 * Get Custom Capabilities
	 * Define all custom capabilities of movie library here
	 *
	 * @return array
	 */
	public static function get_custom_capabilities() : array {
		return array(
			'movie_library_settings', // manage movie library settings.
			'upload_files', // upload files.
		);
	}
}

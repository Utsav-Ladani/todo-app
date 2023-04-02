<?php
/**
 * Movie Manager Role
 * Movie Manager can manage movie and person custom post types.
 * It also can manage movie and person taxonomies.
 *
 * @package Movie Library
 */

namespace Movie_Library\Role;

// CPT
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

// Taxonomy
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
	// TODO: Add role
	// TODO: Add caps
	// TODO: Assign caps to role and admin

	public static function add_movie_manager_role() : void {
		$capabilities = self::get_movie_library_capabilities();

		$admin_role = get_role( 'administrator' );

		foreach ( $capabilities as $capability ) {
			$admin_role->add_cap( $capability );
		}

		$capabilities = array_fill_keys( $capabilities, true );

		add_role( 'movie-manager', __( 'Movie Manager', 'movie-library' ), $capabilities );
	}

	public static function get_movie_library_capabilities() : array {
		$capabilities = array();

		$capabilities = array_merge( $capabilities, self::get_capabilities_of_cpts() );
		$capabilities = array_merge( $capabilities, self::get_capabilities_of_taxonomies() );
		$capabilities = array_merge( $capabilities, self::get_custom_capabilities() );

		return array_unique( $capabilities );
	}

	public static function get_capabilities_of_cpts() : array {
		$custom_post_types = array( Movie::SLUG, Person::SLUG );

		$capabilities = array();

		foreach ( $custom_post_types as $custom_post_type ) {
			$custom_post_type_obj = get_post_type_object( $custom_post_type );

			if( ! $custom_post_type_obj instanceof \WP_Post_Type ) {
				continue;
			}

			$custom_post_type_caps = (array) $custom_post_type_obj->cap;

			$capabilities = array_merge( $capabilities, array_values( $custom_post_type_caps ) );
		}

		return array_unique( $capabilities );
	}

	public static function get_capabilities_of_taxonomies() : array {
		$taxonomies = array( Genre::SLUG, Label::SLUG, Language::SLUG, Production_Company::SLUG, Tag::SLUG, Career::SLUG );

		$capabilities = array();

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_obj = get_taxonomy( $taxonomy );

			if( ! $taxonomy_obj instanceof \WP_Taxonomy ) {
				continue;
			}

			$taxonomy_caps = (array) $taxonomy_obj->cap;

			$capabilities = array_merge( $capabilities, array_values( $taxonomy_caps ) );
		}

		return array_unique( $capabilities );
	}

	public static function remove_movie_manager_role() : void {
		$capabilities = self::get_movie_library_capabilities();

		$admin_role = get_role( 'administrator' );

		foreach ( $capabilities as $capability ) {
			$admin_role->remove_cap( $capability );
		}

		remove_role( 'movie-manager' );
	}

	public static function get_custom_capabilities() : array {
		return array(
			'movie_library_settings',
			'upload_files',
		);
	}
}

<?php
/**
 * Create the Person shadow taxonomy.
 *
 * @package   Movie_Library\Shadow_Taxonomy\Non_Hierarchical
 */

namespace Movie_Library\Shadow_Taxonomy\Non_Hierarchical;

use Movie_Library\Custom_Post_Type\Movie;

/**
 * Class Shadow_Person
 *
 * Create the Person shadow taxonomy.
 */
abstract class Shadow_Person {
	/**
	 * The slug of the shadow taxonomy.
	 *
	 * @var string
	 */
	const SLUG = '_rt-movie-person';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_shadow_person_taxonomy' ) );
	}

	/**
	 * Register the Person shadow taxonomy.
	 *
	 * @return void
	 */
	public static function register_shadow_person_taxonomy(): void {
		$labels = array(
			'name'              => __( 'Persons', 'movie-library' ),
			'singular_name'     => __( 'Person', 'movie-library' ),
			'search_items'      => __( 'Search Persons', 'movie-library' ),
			'all_items'         => __( 'All Persons', 'movie-library' ),
			'parent_item'       => __( 'Parent Person', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Person:', 'movie-library' ),
			'edit_item'         => __( 'Edit Person', 'movie-library' ),
			'update_item'       => __( 'Update Person', 'movie-library' ),
			'add_new_item'      => __( 'Add New Person', 'movie-library' ),
			'new_item_name'     => __( 'New Person Name', 'movie-library' ),
			'menu_name'         => __( 'Person', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Person', 'movie-library' ),
			'public'             => false,
			'publicly_queryable' => false,
			'hierarchical'       => false,
			'show_ui'            => false,
			'rewrite'            => false,
		);

		register_taxonomy( self::SLUG, array( Movie::SLUG ), $args );
	}
}

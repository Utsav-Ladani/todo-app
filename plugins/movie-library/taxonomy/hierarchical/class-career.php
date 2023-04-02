<?php
/**
 * Create the Career taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */

namespace Movie_Library\Taxonomy\Hierarchical;

use Movie_Library\Custom_Post_Type\Person;

/**
 * Class Career
 *
 * Create the Career taxonomy for Person.
 */
abstract class Career {
	/**
	 * The slug of the taxonomy.
	 *
	 * @var string
	 */
	const SLUG = 'rt-person-career';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_career_taxonomy' ) );
	}

	/**
	 * Register the Career taxonomy.
	 *
	 * @return void
	 */
	public static function register_career_taxonomy(): void {
		$labels = array(
			'name'              => __( 'Careers', 'movie-library' ),
			'singular_name'     => __( 'Career', 'movie-library' ),
			'search_items'      => __( 'Search Careers', 'movie-library' ),
			'all_items'         => __( 'All Careers', 'movie-library' ),
			'parent_item'       => __( 'Parent Career', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Career:', 'movie-library' ),
			'edit_item'         => __( 'Edit Career', 'movie-library' ),
			'update_item'       => __( 'Update Career', 'movie-library' ),
			'add_new_item'      => __( 'Add New Career', 'movie-library' ),
			'new_item_name'     => __( 'New Career Name', 'movie-library' ),
			'menu_name'         => __( 'Career', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Career', 'movie-library' ),
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_ui_in_menu'    => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'capabilities'         => array(
				'manage_terms' => 'manage_careers',
				'edit_terms'   => 'edit_careers',
				'delete_terms' => 'delete_careers',
				'assign_terms' => 'assign_careers',
			),
		);

		register_taxonomy( self::SLUG, array( Person::SLUG ), $args );
	}
}

<?php
/**
 * Create the Career taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */

namespace Movie_Library\Taxonomy\Hierarchical;

/**
 * Class Career
 *
 * Create the Career taxonomy for Person.
 */
class Career {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'init', array( __CLASS__, 'register_career_taxonomy' ) );
	}

	/**
	 * Register the Career taxonomy.
	 *
	 * @return void
	 */
	public static function register_career_taxonomy(): void
	{
		$labels = [
			'name'          => _x( 'Careers', 'taxonomy general name', 'movie-library' ),
			'singular_name' => _x( 'Career', 'taxonomy singular name', 'movie-library' ),
			'search_items'  => __( 'Search Careers', 'movie-library' ),
			'all_items'     => __( 'All Careers', 'movie-library' ),
			'parent_item'   => __( 'Parent Career', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Career:', 'movie-library' ),
			'edit_item'     => __( 'Edit Career', 'movie-library' ),
			'update_item'   => __( 'Update Career', 'movie-library' ),
			'add_new_item'  => __( 'Add New Career', 'movie-library' ),
			'new_item_name' => __( 'New Career Name', 'movie-library' ),
			'menu_name'     => __( 'Career', 'movie-library' ),
		];

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Career', 'movie-library' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'hierarchical'        => true,
			'show_ui'             => true,
			'show_ui_in_menu'     => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'query_var'           => true,
			'show_tagcloud'       => true,
			'show_in_quick_edit'  => true,
			'show_admin_column'   => true,
		);

		register_taxonomy( 'rt-person-career', array( 'rt-person' ), $args );
	}
}

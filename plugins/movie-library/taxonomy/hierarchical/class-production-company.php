<?php
/**
 * Create the Production Company taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */

namespace Movie_Library\Taxonomy\Hierarchical;

use Movie_Library\Custom_Post_Type\Movie;

/**
 * Class Production_Company
 *
 * Create the Production Company taxonomy.
 */
abstract class Production_Company {
	/**
	 * The slug of the taxonomy.
	 *
	 * @var string
	 */
	const SLUG = 'rt-movie-production-company';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_production_company_taxonomy' ) );
	}

	/**
	 * Register the Production Company taxonomy.
	 *
	 * @return void
	 */
	public static function register_production_company_taxonomy(): void {
		$labels = array(
			'name'              => __( 'Production Companies', 'movie-library' ),
			'singular_name'     => __( 'Production Company', 'movie-library' ),
			'search_items'      => __( 'Search Production Companies', 'movie-library' ),
			'all_items'         => __( 'All Production Companies', 'movie-library' ),
			'parent_item'       => __( 'Parent Production Company', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Production Company:', 'movie-library' ),
			'edit_item'         => __( 'Edit Production Company', 'movie-library' ),
			'update_item'       => __( 'Update Production Company', 'movie-library' ),
			'add_new_item'      => __( 'Add New Production Company', 'movie-library' ),
			'new_item_name'     => __( 'New Production Company Name', 'movie-library' ),
			'menu_name'         => __( 'Production Company', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Production Company', 'movie-library' ),
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
				'manage_terms' => 'manage_production_companies',
				'edit_terms'   => 'edit_production_companies',
				'delete_terms' => 'delete_production_companies',
				'assign_terms' => 'assign_production_companies',
			),
		);

		register_taxonomy( self::SLUG, array( Movie::SLUG ), $args );
	}
}

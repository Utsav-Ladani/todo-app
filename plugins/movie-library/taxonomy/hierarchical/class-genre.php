<?php
/**
 * Create the Genre taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */

namespace Movie_Library\Taxonomy\Hierarchical;

use Movie_Library\Custom_Post_Type\Movie;

/**
 * Class Genre
 *
 * Create the Genre taxonomy.
 */
abstract class Genre {
	/**
	 * The slug of the taxonomy.
	 *
	 * @var string
	 */
	const SLUG = 'rt-movie-genre';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_genre_taxonomy' ) );
	}

	/**
	 * Register the Genre taxonomy.
	 *
	 * @return void
	 */
	public static function register_genre_taxonomy(): void {
		$labels = array(
			'name'              => __( 'Genres', 'movie-library' ),
			'singular_name'     => __( 'Genre', 'movie-library' ),
			'search_items'      => __( 'Search Genres', 'movie-library' ),
			'all_items'         => __( 'All Genres', 'movie-library' ),
			'parent_item'       => __( 'Parent Genre', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Genre:', 'movie-library' ),
			'edit_item'         => __( 'Edit Genre', 'movie-library' ),
			'update_item'       => __( 'Update Genre', 'movie-library' ),
			'add_new_item'      => __( 'Add New Genre', 'movie-library' ),
			'new_item_name'     => __( 'New Genre Name', 'movie-library' ),
			'menu_name'         => __( 'Genre', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Genre', 'movie-library' ),
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
			'capabilities'       => array(
				'manage_terms' => 'manage_genres',
				'edit_terms'   => 'edit_genres',
				'delete_terms' => 'delete_genres',
				'assign_terms' => 'assign_genres',
			),
		);

		register_taxonomy( self::SLUG, array( Movie::SLUG ), $args );
	}
}

<?php
/**
 * Create the Language taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */

namespace Movie_Library\Taxonomy\Hierarchical;

/**
 * Class Language
 *
 * Create the Language taxonomy.
 */
abstract class Language {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_language_taxonomy' ) );
	}

	/**
	 * Register the Language taxonomy.
	 *
	 * @return void
	 */
	public static function register_language_taxonomy(): void {
		$labels = array(
			'name'              => _x( 'Languages', 'taxonomy general name', 'movie-library' ),
			'singular_name'     => _x( 'Language', 'taxonomy singular name', 'movie-library' ),
			'search_items'      => __( 'Search Languages', 'movie-library' ),
			'all_items'         => __( 'All Languages', 'movie-library' ),
			'parent_item'       => __( 'Parent Language', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Language:', 'movie-library' ),
			'edit_item'         => __( 'Edit Language', 'movie-library' ),
			'update_item'       => __( 'Update Language', 'movie-library' ),
			'add_new_item'      => __( 'Add New Language', 'movie-library' ),
			'new_item_name'     => __( 'New Language Name', 'movie-library' ),
			'menu_name'         => __( 'Language', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Language', 'movie-library' ),
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
		);

		register_taxonomy( 'rt-movie-language', array( 'rt-movie' ), $args );
	}
}

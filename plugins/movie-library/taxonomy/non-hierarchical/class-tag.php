<?php
/**
 * Create the Tag taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Non_Hierarchical
 */

namespace Movie_Library\Taxonomy\Non_Hierarchical;

use Movie_Library\Custom_Post_Type\Movie;

/**
 * Class Tag
 *
 * Create the Tag taxonomy.
 */
abstract class Tag {
	/**
	 * The slug of the taxonomy.
	 *
	 * @var string
	 */
	const SLUG = 'rt-movie-tag';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_tag_taxonomy' ) );
	}

	/**
	 * Register the Tag taxonomy.
	 *
	 * @return void
	 */
	public static function register_tag_taxonomy(): void {
		$labels = array(
			'name'              => __( 'Tags', 'movie-library' ),
			'singular_name'     => __( 'Tag', 'movie-library' ),
			'search_items'      => __( 'Search Tags', 'movie-library' ),
			'all_items'         => __( 'All Tags', 'movie-library' ),
			'parent_item'       => __( 'Parent Tag', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Tag:', 'movie-library' ),
			'edit_item'         => __( 'Edit Tag', 'movie-library' ),
			'update_item'       => __( 'Update Tag', 'movie-library' ),
			'add_new_item'      => __( 'Add New Tag', 'movie-library' ),
			'new_item_name'     => __( 'New Tag Name', 'movie-library' ),
			'menu_name'         => __( 'Tag', 'movie-library' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Tag', 'movie-library' ),
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_ui_in_menu'    => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'show_tagcloud'      => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
		);

		register_taxonomy( self::SLUG, array( Movie::SLUG ), $args );
	}
}

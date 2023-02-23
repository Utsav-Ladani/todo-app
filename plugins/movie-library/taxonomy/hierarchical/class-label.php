<?php
/**
 * Create the Label taxonomy.
 *
 * @package   Movie_Library\Taxonomy\Hierarchical
 */
namespace Movie_Library\Taxonomy\Hierarchical;

/**
 * Class Label
 *
 * Create the Label taxonomy.
 */
abstract class Label {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'init', array( __CLASS__, 'register_label_taxonomy' ) );
	}

	/**
	 * Register the Label taxonomy.
	 *
	 * @return void
	 */
	public static function register_label_taxonomy(): void
	{
		$labels = [
			'name'          => _x( 'Labels', 'taxonomy general name', 'movie-library' ),
			'singular_name' => _x( 'Label', 'taxonomy singular name', 'movie-library' ),
			'search_items'  => __( 'Search Labels', 'movie-library' ),
			'all_items'     => __( 'All Labels', 'movie-library' ),
			'parent_item'   => __( 'Parent Label', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Label:', 'movie-library' ),
			'edit_item'     => __( 'Edit Label', 'movie-library' ),
			'update_item'   => __( 'Update Label', 'movie-library' ),
			'add_new_item'  => __( 'Add New Label', 'movie-library' ),
			'new_item_name' => __( 'New Label Name', 'movie-library' ),
			'menu_name'     => __( 'Label', 'movie-library' ),
		];

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Label', 'movie-library' ),
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

		register_taxonomy( 'rt-movie-label', array( 'rt-movie' ), $args );
	}
}

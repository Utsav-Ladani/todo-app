<?php

namespace Movie_Library\Taxonomy\Hierarchical;

class Genre {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'init', array( __CLASS__, 'register_genre_taxonomy' ) );
	}

	/**
	 * Register the taxonomy.
	 *
	 * @return void
	 */
	public static function register_genre_taxonomy(): void
	{
		$labels = [
			'name'          => _x( 'Genres', 'taxonomy general name', 'movie-library' ),
			'singular_name' => _x( 'Genre', 'taxonomy singular name', 'movie-library' ),
			'search_items'  => __( 'Search Genres', 'movie-library' ),
			'all_items'     => __( 'All Genres', 'movie-library' ),
			'parent_item'   => __( 'Parent Genre', 'movie-library' ),
			'parent_item_colon' => __( 'Parent Genre:', 'movie-library' ),
			'edit_item'     => __( 'Edit Genre', 'movie-library' ),
			'update_item'   => __( 'Update Genre', 'movie-library' ),
			'add_new_item'  => __( 'Add New Genre', 'movie-library' ),
			'new_item_name' => __( 'New Genre Name', 'movie-library' ),
			'menu_name'     => __( 'Genre', 'movie-library' ),
		];

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Genre', 'movie-library' ),
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

		register_taxonomy( 'rt-movie-genre', array( 'rt-movie' ), $args );
	}
}

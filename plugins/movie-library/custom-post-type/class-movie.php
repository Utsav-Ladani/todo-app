<?php

namespace Movie_Library\Custom_Post_Type;

class Movie
{

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'init', array( __CLASS__, 'register_movie_post_type' ) );
	}

	/**
	 * Register movie post type.
	 *
	 * @return void
	 */
	public static function register_movie_post_type(): void
	{
		$labels = array(
			'name'                  => _x( 'Movies', 'Post Type General Name', 'movie-library' ),
			'singular_name'         => _x( 'Movie', 'Post Type Singular Name', 'movie-library' ),
			'menu_name'             => _x( 'Movies', 'Admin Menu text', 'movie-library' ),
			'name_admin_bar'        => _x( 'Movie', 'Add New on Toolbar', 'movie-library' ),
			'archives'              => __( 'Movie Archives', 'movie-library' ),
			'attributes'            => __( 'Movie Attributes', 'movie-library' ),
			'parent_item_colon'     => __( 'Parent Movie:', 'movie-library' ),
			'all_items'             => __( 'All Movies', 'movie-library' ),
			'add_new_item'          => __( 'Add New Movie', 'movie-library' ),
			'add_new'               => __( 'Add New', 'movie-library' ),
			'new_item'              => __( 'New Movie', 'movie-library' ),
			'edit_item'             => __( 'Edit Movie', 'movie-library' ),
			'update_item'           => __( 'Update Movie', 'movie-library' ),
			'view_item'             => __( 'View Movie', 'movie-library' ),
			'view_items'            => __( 'View Movies', 'movie-library' ),
			'search_items'          => __( 'Search Movie', 'movie-library' ),
			'not_found'             => __( 'Not found', 'movie-library' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'movie-library' ),
			'set_featured_image'    => __( 'Set featured image', 'movie-library' ),
			'remove_featured_image' => __( 'Remove featured image', 'movie-library' ),
			'use_featured_image'    => __( 'Use as featured image', 'movie-library' ),
			'insert_into_item'      => __( 'Insert into Movie', 'movie-library' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Movie', 'movie-library' ),
			'items_list'            => __( 'Movies list', 'movie-library' ),
			'items_list_navigation' => __( 'Movies list navigation', 'movie-library' ),
			'filter_items_list'     => __( 'Filter Movies list', 'movie-library' ),
			'featured_image'        => __( 'Movie Poster', 'movie-library' ),
			'post_content'          => __( 'Plot', 'movie-library' ),
			'post_excerpt'          => __( 'Description', 'movie-library' ),
			'post_title'            => __( 'Title', 'movie-library' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comment' ),
			'has_archive'         => true,
			'rewrite'             => array ( 'slug' => 'rt-movie' ),
			'query_var'           => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-editor-video',
			'show_in_rest'        => true,
			'rest_base'           => 'movie',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);

		register_post_type( 'rt-movie', $args );
	}
}
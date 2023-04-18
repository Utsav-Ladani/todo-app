<?php
/**
 * Create the custom post type for Movie.
 *
 * @package   Movie_Library\Custom_Post_Type
 */

namespace Movie_Library\Custom_Post_Type;

/**
 * Movie class.
 *
 * Create the custom post type for movie.
 */
abstract class Movie {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const SLUG = 'rt-movie';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_movie_post_type' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'change_enter_title_here' ) );
		add_filter( 'write_your_story', array( __CLASS__, 'change_write_your_story' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'custom_excerpt_heading' ) );
	}

	/**
	 * Register movie post type.
	 *
	 * @return void
	 */
	public static function register_movie_post_type(): void {
		$labels = array(
			'name'                  => __( 'Movies', 'movie-library' ),
			'singular_name'         => __( 'Movie', 'movie-library' ),
			'menu_name'             => __( 'Movies', 'movie-library' ),
			'name_admin_bar'        => __( 'Movie', 'movie-library' ),
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
			'insert_into_item'      => __( 'Add into Movie', 'movie-library' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Movie', 'movie-library' ),
			'items_list'            => __( 'Movies list', 'movie-library' ),
			'items_list_navigation' => __( 'Movies list navigation', 'movie-library' ),
			'filter_items_list'     => __( 'Filter Movies list', 'movie-library' ),
			'featured_image'        => __( 'Movie Poster', 'movie-library' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments' ),
			'has_archive'       => true,
			'query_var'         => true,
			'menu_position'     => null,
			'menu_icon'         => 'dashicons-editor-video',
			'show_in_rest'      => true,
			'rest_base'         => 'movie',
			'capability_type'   => 'movie',
			'map_meta_cap'      => true,
		);

		// phpcs:ignore WordPress.NamingConventions.ValidPostTypeSlug.NotStringLiteral
		register_post_type( self::SLUG, $args );
	}

	/**
	 * Change the enter_title_here label for rt-movie post type.
	 *
	 * @param string $title The label text.
	 *
	 * @return string
	 */
	public static function change_enter_title_here( string $title ) : string {
		if ( self::SLUG === get_post_type() ) {
			return __( 'Title', 'movie-library' );
		}
		return $title;
	}

	/**
	 * Change the write_your_story label for rt-movie post type.
	 *
	 * @param string $post_content The label text.
	 *
	 * @return string
	 */
	public static function change_write_your_story( string $post_content ) : string {
		if ( self::SLUG === get_post_type() ) {
			return __( 'Plot', 'movie-library' );
		}
		return $post_content;
	}

	/**
	 * Enqueue the script for custom excerpt heading.
	 *
	 * @return void
	 */
	public static function custom_excerpt_heading() : void {
		// return if post type is not rt-movie.
		if ( self::SLUG !== get_post_type() ) {
			return;
		}

		// enqueue the script.
		wp_enqueue_script(
			'movie-library-admin',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/movie-library-admin.js',
			array( 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/movie-library-admin.js' ),
			true
		);
	}
}

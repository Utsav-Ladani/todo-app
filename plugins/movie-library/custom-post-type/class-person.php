<?php
/**
 * Create the custom post type for Person.
 *
 * @package   Movie_Library\Custom_Post_Type
 */

namespace Movie_Library\Custom_Post_Type;

/**
 * Person class.
 *
 * Create the custom post type for person.
 */
abstract class Person {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const SLUG = 'rt-person';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register_person_post_type' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'change_enter_title_here' ) );
		add_filter( 'write_your_story', array( __CLASS__, 'change_write_your_story' ) );
	}

	/**
	 * Register person post type.
	 *
	 * @return void
	 */
	public static function register_person_post_type(): void {
		$labels = array(
			'name'                  => __( 'Persons', 'movie-library' ),
			'singular_name'         => __( 'Person', 'movie-library' ),
			'menu_name'             => __( 'Persons', 'movie-library' ),
			'name_admin_bar'        => __( 'Person', 'movie-library' ),
			'archives'              => __( 'Person Archives', 'movie-library' ),
			'attributes'            => __( 'Person Attributes', 'movie-library' ),
			'parent_item_colon'     => __( 'Parent Person:', 'movie-library' ),
			'all_items'             => __( 'All Persons', 'movie-library' ),
			'add_new_item'          => __( 'Add New Person', 'movie-library' ),
			'add_new'               => __( 'Add New', 'movie-library' ),
			'new_item'              => __( 'New Person', 'movie-library' ),
			'edit_item'             => __( 'Edit Person', 'movie-library' ),
			'update_item'           => __( 'Update Person', 'movie-library' ),
			'view_item'             => __( 'View Person', 'movie-library' ),
			'view_items'            => __( 'View Persons', 'movie-library' ),
			'search_items'          => __( 'Search Person', 'movie-library' ),
			'not_found'             => __( 'Not found', 'movie-library' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'movie-library' ),
			'set_featured_image'    => __( 'Set featured image', 'movie-library' ),
			'remove_featured_image' => __( 'Remove featured image', 'movie-library' ),
			'use_featured_image'    => __( 'Use as featured image', 'movie-library' ),
			'insert_into_item'      => __( 'Add into Person', 'movie-library' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Person', 'movie-library' ),
			'items_list'            => __( 'Persons list', 'movie-library' ),
			'items_list_navigation' => __( 'Persons list navigation', 'movie-library' ),
			'filter_items_list'     => __( 'Filter Persons list', 'movie-library' ),
			'featured_image'        => __( 'Profile Picture', 'movie-library' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author' ),
			'has_archive'       => true,
			'query_var'         => true,
			'menu_position'     => null,
			'menu_icon'         => 'dashicons-admin-users',
			'show_in_rest'      => true,
			'rest_base'         => 'person',
		);

		register_post_type( self::SLUG, $args );
	}

	/**
	 * Change the enter_title_here label for rt-person post type.
	 *
	 * @param string $title The label text.
	 *
	 * @return string
	 */
	public static function change_enter_title_here( string $title ) : string {
		if ( self::SLUG === get_post_type() ) {
			return __( 'Name', 'movie-library' );
		}
		return $title;
	}

	/**
	 * Change the write_your_story label for rt-person post type.
	 *
	 * @param string $post_content The label text.
	 *
	 * @return string
	 */
	public static function change_write_your_story( string $post_content ) : string {
		if ( self::SLUG === get_post_type() ) {
			return __( 'Biography', 'movie-library' );
		}
		return $post_content;
	}
}

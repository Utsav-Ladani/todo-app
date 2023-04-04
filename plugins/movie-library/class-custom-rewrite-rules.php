<?php
/**
 * Movie Library Custom Rewrite Rules
 * It adds the custom rewrite rules for movie library plugin.
 *
 * @package Movie Library
 */

namespace Movie_Library;

// CPT and Taxonomy.
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;
use Movie_Library\Taxonomy\Hierarchical\Career;
use Movie_Library\Taxonomy\Hierarchical\Genre;

/**
 * Class Custom_Rewrite_Rules
 * It adds the custom rewrite rules for movie and person post types.
 */
class Custom_Rewrite_Rules {
	/**
	 * Add callbacks on respected hooks.
	 *
	 * @return void
	 */
	public static function init() {
		// change the permalink structure of custom post types.
		add_action( 'init', array( __CLASS__, 'add_custom_rewrite_rules' ), 11 );

		// resolve the permalink for custom post types.
		add_action( 'post_type_link', array( __CLASS__, 'generate_movie_post_type_link' ), 10, 2 );
	}

	/**
	 * Add custom rewrite rules for all CPT.
	 * It changes the rewrite rules' structure for movie and person post types.
	 *
	 * @return void
	 */
	public static function add_custom_rewrite_rules() : void {
		// add custom rewrite rules for movie post type.
		$movie_slug = Movie::SLUG;
		$genre_slug = Genre::SLUG;
		self::add_custom_rewrite_rules_for_post_type( $movie_slug, $genre_slug, 'movie' );

		// add custom rewrite rules for person post type.
		$person_slug = Person::SLUG;
		$career_slug = Career::SLUG;
		self::add_custom_rewrite_rules_for_post_type( $person_slug, $career_slug, 'person' );
	}

	/**
	 * Add custom permalink structure for a post type.
	 *
	 * @param string $post_type Post type slug.
	 * @param string $taxonomy Taxonomy slug.
	 * @param string $custom_front Custom front.
	 *
	 * @return void
	 */
	public static function add_custom_rewrite_rules_for_post_type( string $post_type, string $taxonomy, string $custom_front ) : void {
		global $wp_rewrite;

		// get the post type's rewrite rules.
		$args = $wp_rewrite->extra_permastructs[ $post_type ];

		// remove the existing rewrite rules.
		remove_permastruct( $post_type );

		// unset the struct.
		unset( $args['struct'] );

		// remove the front.
		$args['with_front'] = false;

		// add the custom permalink structure.
		add_permastruct( $post_type, "$custom_front/%$taxonomy%/%$post_type%-%post_id%", $args );
	}

	/**
	 * Generate the permalink for custom post types.
	 *
	 * @param string   $permalink Permalink.
	 * @param \WP_Post $post Post object.
	 *
	 * @return string
	 */
	public static function generate_movie_post_type_link( string $permalink, \WP_Post $post ) : string {
		// movie post type.
		if ( Movie::SLUG === $post->post_type ) {
			return self::generate_movie_post_type_link_for_post_type( $permalink, $post, Movie::SLUG, Genre::SLUG, 'genre' );
		}

		// person post type.
		if ( Person::SLUG === $post->post_type ) {
			return self::generate_movie_post_type_link_for_post_type( $permalink, $post, Person::SLUG, Career::SLUG, 'career' );
		}

		// other permalinks.
		return $permalink;
	}

	/**
	 * Generate the permalink for given post types.
	 *
	 * @param string   $permalink Permalink.
	 * @param \WP_Post $post Post object.
	 * @param string   $post_type Post type slug.
	 * @param string   $taxonomy Taxonomy slug.
	 * @param string   $default_term Default term.
	 *
	 * @return string
	 */
	public static function generate_movie_post_type_link_for_post_type( string $permalink, \WP_Post $post, string $post_type, string $taxonomy, string $default_term ) : string {
		// get the term list.
		$terms = get_the_terms( $post, $taxonomy );

		// set the first term as default.
		$first_term = $default_term;

		// if terms are available, set the first term as default.
		if ( is_array( $terms ) ) {
			$first_term = array_shift( $terms );
			$first_term = $first_term->slug ?? $default_term;
		}

		// replace the placeholders with actual values and return the permalink.
		return str_replace(
			array( "%$taxonomy%", "%$post_type%", '%post_id%' ),
			array( $first_term, $post->post_name, $post->ID ),
			$permalink
		);
	}
}

<?php
/**
 * Movie Shortcode add shortcode for movie which will be used in the page to show the movie list using different filters.
 *
 * @package Movie_Library\Shortcode
 */

namespace Movie_Library\Shortcode;

/**
 * Class Movie_Shortcode
 * Add the 'movie' shortcode to show the movie list using given filters.
 */
class Movie_Shortcode {

	/**
	 * Attributes name.
	 * Helps to avoid the code duplication and increase the readability.
	 *
	 * @var array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static array $attributes_name = array(
		'person',
		'genre',
		'label',
		'language',
	);

	/**
	 * Initialize the shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return void
	 * @hooked init movie_shortcode_init
	 */
	public static function init() : void {
		add_action( 'init', array( __CLASS__, 'movie_shortcode_init' ) );
	}

	/**
	 * Add the shortcode for movie post type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function movie_shortcode_init() : void {
		add_shortcode( 'movie', array( __CLASS__, 'render_movie_shortcode' ) );
	}

	/**
	 * Render the movie shortcode.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array  $attributes Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $tag Shortcode tag.
	 * @return string
	 */
	public static function render_movie_shortcode( array $attributes, string $content, string $tag ) : string {
		// Structure the attributes.
		$attributes = self::structure_attributes( $attributes, $tag );

		// Sanitize the attributes.
		$attributes = self::sanitize_shortcode_attributes( $attributes );

		// Structure the query arguments.
		$query_result = self::wp_query_for_movie_shortcode( $attributes );

		// Filter the query result.
		$filtered_result = self::get_filtered_movie_data( $query_result );

		// Return the HTML.
		return self::get_html_with_data( $filtered_result );
	}

	/**
	 * Sanitize the shortcode attributes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $attributes Shortcode attributes.
	 * @return array
	 */
	static public function sanitize_shortcode_attributes( array $attributes ) : array {
		// Change the key to lower case.
		$attributes = array_change_key_case( $attributes, CASE_LOWER );

		$filtered_attributes = array();

		// Sanitize the attributes.
		foreach ( self::$attributes_name as $attribute_name ) {
			$attribute_value = $attributes[$attribute_name];
			$attribute_value = strtolower( $attribute_value );
			$attribute_value = trim( $attribute_value, '-' );

			// Check if the attribute value is valid slug or not.
			if( preg_match( '/^[a-z0-9-]+$/', $attribute_value) ) {
				$filtered_attributes[$attribute_name] = $attribute_value;
			}
		}

		return $filtered_attributes;
	}

	/**
	 * Structure the attributes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $attributes Shortcode attributes.
	 * @param string $tag Shortcode tag.
	 * @return array
	 */
	public static function structure_attributes( array $attributes, string $tag ) : array {
		// Merge the attributes with default attributes.
		return shortcode_atts(
			array(
				'person'   => '',
				'genre'    => '',
				'label'    => '',
				'language' => '',
			),
			$attributes,
			$tag
		);
	}

	/**
	 * Structure the query arguments.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $args Shortcode attributes.
	 * @return array
	 */
	public static function structure_query_args( array $args ) : array {
		$tax_query = array();

		// Add the person query.
		$person_tax_query = self::add_person_query_to_args( $args );

		// Add the other query.
		$other_tax_query = self::add_other_query_to_args( $args );

		// Merge the query.
		$tax_query[] = array_merge( $person_tax_query, $other_tax_query );

		// Add the relation.
		if( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		// Return the query.
		return array(
			'post_type'      => 'rt-movie',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => $tax_query,
		);
	}

	/**
	 * Add the person query to the query arguments.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $args Shortcode attributes.
	 * @return array
	 */
	public static function add_person_query_to_args( array $args ) : array {
		$person_tax_query = array();

		// Check if the person attribute is set or not.
		if( isset( $args['person'] ) && ! empty( $args['person'] ) ) {
			// Add the person query for term id.
			$person_tax_query[] = array(
				'taxonomy' => '_rt-movie-person',
				'field'    => 'term_id',
				'terms'    => (int) $args['person']
			);

			// Add the person query for name.
			$person_tax_query[] = array(
				'taxonomy' => '_rt-movie-person',
				'field'    => 'name',
				'terms'    => sprintf( 'rt-person-%s', $args['person'] )
			);

			// Add the person query for slug.
			if( ! str_contains( $args['person'], ' ' ) ) {
				$person_tax_query[] = array(
					'taxonomy' => '_rt-movie-person',
					'field'    => 'slug',
					'terms'    => sprintf( 'rt-person-%s', $args['person'] )
				);
			}

			// Add the relation.
			if( count( $person_tax_query ) > 1 ) {
				$person_tax_query['relation'] = 'OR';
			}
			else {
				$person_tax_query = $person_tax_query[0];
			}
		}

		return array( $person_tax_query );
	}

	/**
	 * Add the other query to the query arguments like genre, label, and language.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $args Shortcode attributes.
	 * @return array
	 */
	public static function add_other_query_to_args( array $args ) : array {
		$tax_query = array();

		// Loop through the attributes.
		foreach ( self::$attributes_name as $attribute_name ) {
			// Check if the attribute is set or not.
			if (
				isset( $args[$attribute_name] )
				&& ! empty( $args[$attribute_name] )
				&& 'person' !== $attribute_name
			) {
				$sub_tax_query = array();

				// Add the query for term id.
				$sub_tax_query[] = array(
					'taxonomy' => sprintf( 'rt-movie-%s', $attribute_name ),
					'field'    => 'term_id',
					'terms'    => (int) $args[$attribute_name],
				);

				// Add the query for name.
				$sub_tax_query[] = array(
					'taxonomy' => sprintf( 'rt-movie-%s', $attribute_name ),
					'field'    => 'name',
					'terms'    => $args[$attribute_name],
				);

				// Add the query for slug.
				if( ! str_contains( $args[$attribute_name], ' ' ) ) {
					$sub_tax_query[] = array(
						'taxonomy' => sprintf( 'rt-movie-%s', $attribute_name ),
						'field'    => 'slug',
						'terms'    => $args[$attribute_name],
					);
				}

				// Add the relation.
				$sub_tax_query['relation'] = 'OR';

				$tax_query[] = $sub_tax_query;
			}
		}

		return $tax_query;
	}

	/**
	 * Get the movie data from the query result.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $query_args Query arguments.
	 * @return array
	 */
	public static function wp_query_for_movie_shortcode( array $query_args ) : array {
		$query_args = self::structure_query_args( $query_args );

		$movie_query = new \WP_Query( $query_args );

		// Reset the post pointer.
		wp_reset_postdata();

		return $movie_query->posts;
	}

	/**
	 * Filter the movie post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $query_result Query result.
	 * @return array
	 */
	public static function get_filtered_movie_data( array $query_result ) : array {
		$result = array();

		// Loop through the query result.
		foreach ( $query_result as $movie ) {
			$title = $movie->post_title;

			// Get the poster.
			$poster = '';

			// Get the release date.
			$movie_basic_date = get_post_meta( $movie->ID, 'rt-movie-meta-basic', true );
			$release_date = $movie_basic_date['rt-movie-meta-basic-release-date'] ?? '';


			// Get the director.
			$director = get_post_meta( $movie->ID, 'rt-movie-meta-crew-director', true );
			$director = maybe_unserialize( $director ) ?? array();

			if( ! is_array( $director ) ) {
				$director = array();
			}

			// Get the actors.
			$actors = get_post_meta( $movie->ID, 'rt-movie-meta-crew-actor', true );
			$actors = maybe_unserialize( $actors ) ?? array();

			// Limit the actors to 2.
			if( is_array( $actors ) ) {
				$actors = array_slice( $actors, 0, 2 );
			}
			else {
				$actors = array();
			}

			// Structure the movie data.
			$movie_data = array(
				'Title'          => $title,
				'Poster'         => $poster,
				'Release Date'   => $release_date,
				'Director'       => $director,
				'Actor'          => $actors,
			);

			$result[] = $movie_data;
		}

		return $result;
	}

	/**
	 * Get the HTML with the movie data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array $result Movie data.
	 * @return string
	 */
	public static function get_html_with_data( array $result ) : string {
		$movie_items = '';

		// Loop through the movie data.
		foreach ( $result as $movie ) {
			$directors = '';

			// Loop through the directors.
			foreach ( $movie['Director'] as $director ) {
				$directors .= sprintf( "<li class='director-item' > %s </li>", $director );
			}

			$actors = '';

			// Loop through the actors.
			foreach ( $movie['Actor'] as $actor ) {
				$actors .= sprintf( "<li class='actor-item' > %s </li>", $actor );
			}

			// Structure the HTML.
			$content = sprintf( "<h3 class='movie-item__title' > %s </h3>", $movie['Title'] );
			$content .= "<img src='https://www.salonprismla.com/wp-content/uploads/sites/202/2021/07/image-coming-soon-200x300-1.jpg' class='movie-item__poster' />";
			$content .= sprintf( "<p class='movie-item__date' > %s </p>", $movie['Release Date'] );

			$content .= sprintf( "<h5 class='movie-item__director-title' > %s </h5>", 'Director' );
			$content .= sprintf( "<ul class='movie-item__directors' > %s </ul>", $directors );

			$content .= sprintf( "<h5 class='movie-item__actor-title' > %s </h5>", 'Actor' );
			$content .= sprintf( "<ul class='movie-item__actors' > %s </ul>", $actors );

			// Add the content to the movie items.
			$movie_items .= sprintf('<li> %s </li>', $content);
		}

		// Return the HTML.
		return sprintf( "<ul class='movie-items'> %s </ul>", $movie_items );
	}
}
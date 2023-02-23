<?php
/**
 * Person Shortcode which will be used to display person data using person career type.
 *
 * @package Movie_Library\Shortcode
 */

namespace Movie_Library\Shortcode;

/**
 * Class Person_Shortcode
 * It add the 'person' shortcode and render the person post for given career in filter.
 */
abstract class Person_Shortcode {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @hook init person_shortcode_init
	 */
	public static function init() : void {
		// Register the shortcode on init hook.
		add_action( 'init', array( __CLASS__, 'person_shortcode_init' ) );
	}

	/**
	 * Register the 'person' shortcode.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function person_shortcode_init() : void {
		// Add 'person' shortcode.
		add_shortcode( 'person', array( __CLASS__, 'render_person_shortcode' ) );
	}

	/**
	 * Render the person post for given career in filter.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $tag Shortcode tag.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_person_shortcode( mixed $attributes, string $content, string $tag ) : string {
		if( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		// Structure the attributes.
		$attributes = self::structure_attributes( $attributes, $tag );

		// Sanitize the attributes.
		$attributes = self::sanitize_shortcode_attribute( $attributes );

		// Get the query result.
		$query_result = self::wp_query_for_person_shortcode( $attributes );

		// Filter the query result.
		$filtered_result = self::get_filtered_person_data( $query_result, $attributes );

		// Return the html with data.
		return self::get_html_with_data( $filtered_result );
	}

	/**
	 * Sanitize the shortcode attribute.
	 *
	 * @param array $attribute Shortcode attribute.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function sanitize_shortcode_attribute( array $attribute ) : array {
		// Change the key to lower case.
		$attribute = array_change_key_case( $attribute, CASE_LOWER );

		$filtered_value = array();

		// Sanitize the career attribute.
		$attribute_value = $attribute['career'];
		$attribute_value = strtolower( $attribute_value );
		$attribute_value = trim( $attribute_value, '-' );

		// Check if the career slug is valid or not.
		if( preg_match( '/^[a-z0-9-]+$/', $attribute_value) ) {
			$filtered_value['career'] = $attribute_value;
		}

		return $filtered_value;
	}

	/**
	 * Structure the shortcode attributes.
	 *
	 * @param array $attributes Shortcode attributes.
	 * @param string $tag Shortcode tag.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function structure_attributes( array $attributes, string $tag ) : array {
		// Merge the default attributes with the given attributes.
		return shortcode_atts(
			array(
				'career' => '',
			),
			$attributes,
			$tag
		);
	}

	/**
	 * Structure the query arguments.
	 *
	 * @param array $args Query arguments.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function structure_query_args( array $args ) : array {
		$tax_query = array();

		// Check if the career is set or not.
		if( isset( $args['career'] ) && ! empty( $args['career'] ) ) {
			// add tax query for career using term_id.
			$tax_query[] = array(
				'taxonomy' => 'rt-person-career',
				'field'    => 'term_id',
				'terms'    => $args['career']
			);

			// add tax query for career using name.
			$tax_query[] = array(
				'taxonomy' => 'rt-person-career',
				'field'    => 'name',
				'terms'    => $args['career']
			);

			/*
			 * Check if the career slug contains space or not.
			 * Add tax query for career using slug.
			 */
			if( ! str_contains( $args['career'], ' ' ) ) {
				$tax_query[] = array(
					'taxonomy' => 'rt-person-career',
					'field'    => 'slug',
					'terms'    => $args['career']
				);
			}

			// Set the relation for tax query.
			$tax_query['relation'] = 'OR';
		}

		// Return the query arguments.
		return array(
			'post_type'      => 'rt-person',
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => $tax_query,
		);
	}

	/**
	 * Fire the query and return the result.
	 *
	 * @param array $query_args Query arguments.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function wp_query_for_person_shortcode( array $query_args ) : array {
		// Structure the query arguments.
		$query_args = self::structure_query_args( $query_args );

		$person_query = new \WP_Query( $query_args );

		// Reset the post data.
		wp_reset_postdata();

		return $person_query->posts;
	}

	/**
	 * Filter the query result.
	 *
	 * @param array $query_result Query result.
	 * @param array $attributes Shortcode attributes.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_filtered_person_data( array $query_result, mixed $attributes ) : array {
		$result = array();

		// Loop through the query result and get individual data.
		foreach ( $query_result as $person ) {
			$name = $person->post_title;

			// Get the profile picture url.
			$profile_picture = get_post_thumbnail_id( $person->ID );
			if( $profile_picture ) {
				$profile_picture = wp_get_attachment_image_url( $profile_picture);
			}
			else {
				$profile_picture = '';
			}

			// Get the career type of the person.
			$career = self::find_career( $person->ID );

			// Create the person data array of name, profile picture and career.
			$person_data = array(
				'Name' => $name,
				'Profile Picture' => $profile_picture,
				'Career' => $career,
			);

			$result[] = $person_data;
		}

		return $result;
	}

	/**
	 * Find the career of the person.
	 *
	 * @param int $person_id Person ID.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function find_career( int $person_id ) : string {
		$career = '';

		// Get the career terms.
		$terms = get_the_terms( $person_id, 'rt-person-career' );

		// Check if the career terms are set or not.
		if( $terms ) {
			// Get the career name from the terms.
			$terms_name = array_map( function( $term ) {
				return $term->name;
			}, $terms );

			// Implode the career name.
			$career = implode( ', ', $terms_name );
		}

		return $career;
	}

	/**
	 * Get the html with data.
	 *
	 * @param array $result Query result.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_html_with_data( array $result ) : string {
		$person_items = '';

		foreach ( $result as $person ) {
			// Create the html for each person.
			$content = sprintf( "<h3 class='person-item__name' > %s </h3>", $person['Name'] );

			// Add Profile Picture if it is exists.
			if( $person['Profile Picture'] ) {
				$content .= sprintf( "<img src='%s' class='person-item__profile-picture' />", $person['Profile Picture'] );
			}

			$content .= sprintf( "<p class='person-item__career' > %s </p>", $person['Career'] );

			// Add the person html to the person items.
			$person_items .= sprintf('<li> %s </li>', $content);
		}

		// Return the person items html.
		return sprintf( "<ul class='movie-items'> %s </ul>", $person_items );
	}
}

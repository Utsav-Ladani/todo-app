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
class Person_Shortcode {

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
	 * @param array $attributes Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $tag Shortcode tag.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_person_shortcode( array $attributes, string $content, string $tag ) : string {
		// Structure the attributes.
		$attributes = self::structure_attributes( $attributes, $tag );

		// Sanitize the attributes.
		$attributes = self::sanitize_shortcode_attribute( $attributes );

		// Get the query result.
		$query_result = self::wp_query_for_person_shortcode( $attributes );

		// Filter the query result.
		$filtered_result = self::get_filtered_person_data( $query_result );

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
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_filtered_person_data( array $query_result ) : array {
		$result = array();

		// Loop through the query result and get individual data.
		foreach ( $query_result as $person ) {
			$name = $person->post_title;

			$profile_picture = '';

			$career = get_post_meta( $person->ID, 'rt-person-career', true );

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
			$content .= "<img src='https://www.salonprismla.com/wp-content/uploads/sites/202/2021/07/image-coming-soon-200x300-1.jpg' class='person-item__profile-picture' />";
			$content .= sprintf( "<p class='person-item__career' > %s </p>", $person['Career'] );

			// Add the person html to the person items.
			$person_items .= sprintf('<li> %s </li>', $content);
		}

		// Return the person items html.
		return sprintf( "<ul class='movie-items'> %s </ul>", $person_items );
	}
}

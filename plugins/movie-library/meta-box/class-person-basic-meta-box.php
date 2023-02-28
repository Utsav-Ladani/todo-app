<?php
/**
 * Person Basic Meta Box which will be used to add basic information about the person like birthdate, birthplace.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

/**
 * Class Person_Basic_Meta_Box
 * Add the basic information about the person like birthdate, birthplace.
 */
abstract class Person_Basic_Meta_Box {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @hooked add_meta_boxes add_person_basic_meta_box
	 * @hooked save_post_rt-person save_person_basic_meta_data
	 */
	public static function init() : void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_person_basic_meta_box' ) );
		add_action( 'save_post_rt-person', array( __CLASS__, 'save_person_basic_meta_data' ) );
	}

	/**
	 * Add the meta box for the basic information about the person.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_person_basic_meta_box() : void {
		// Add the meta box for the basic information about the person.
		add_meta_box(
			'rt-person-meta-basic',
			__( 'Basic', 'movie-library' ),
			array( __CLASS__, 'render_person_basic_meta_box' ),
			'rt-person',
			'side',
		);
	}

	/**
	 * Render the meta box for the basic information about the person.
	 *
	 * @param \WP_Post $post The post object.
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_person_basic_meta_box( \WP_Post $post ) : void {
		// add nonce field here.

		// Get the metadata for the person.
		$person_basic_meta_data = self::get_person_basic_meta_data( $post->ID );

		// Add nonce for security and authentication.
		wp_nonce_field( 'rt-person-basic-meta-box', 'rt-person-basic-meta-box-nonce' );

		// Render the meta box for the basic information about the person.
		?>
		<label for='rt-person-meta-basic-birth-date' >
			<?php esc_html_e( 'Birth Date', 'movie-library' ); ?>
		</label>
		<input
			type='date'
			class='widefat'
			name='rt-person-meta-basic-birth-date'
			id='rt-person-meta-basic-birth-date'
			max='<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>'
			value=<?php echo esc_attr( $person_basic_meta_data['rt-person-meta-basic-birth-date'] ); ?>
		/>

		<label for='rt-person-meta-basic-birth-place' >
			<?php esc_html_e( 'Birth Place', 'movie-library' ); ?>
		</label>
		<input
			type='text'
			class='widefat'
			name='rt-person-meta-basic-birth-place'
			id='rt-person-meta-basic-birth-place'
			value='<?php echo esc_attr( $person_basic_meta_data['rt-person-meta-basic-birth-place'] ); ?>'
		/>
		<?php
	}

	/**
	 * Get the basic information about the person.
	 *
	 * @param int $post_id The post id.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_person_basic_meta_data( int $post_id ) : array {
		// Get the metadata for the person.
		$data = get_post_meta( $post_id, 'rt-person-meta-basic', true );

		// sanitize data to avoid errors.
		return array(
			'rt-person-meta-basic-birth-date'  => $data['rt-person-meta-basic-birth-date'] ?? '',
			'rt-person-meta-basic-birth-place' => $data['rt-person-meta-basic-birth-place'] ?? '',
		);
	}

	/**
	 * Save the basic information about the person.
	 *
	 * @param int $post_id The post id.
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function save_person_basic_meta_data( int $post_id ) : void {
		// Check whether request type is POST.
		if ( ! isset( $_POST ) ) {
			return;
		}

		// Check whether current user has permission to edit the post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check whether the post is autosave or revision.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_SANITIZE_STRING );

		// Check whether the nonce is set and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return;
		}

		// Add the data if sent by user.
		$meta_value = array();
		$meta_value = self::add_birth_date_to_meta_data( $meta_value );
		$meta_value = self::add_birth_place_to_meta_data( $meta_value );

		// Delete the meta data if no data is sent by user.
		if ( count( $meta_value ) === 0 ) {
			delete_post_meta( $post_id, 'rt-person-meta-basic' );
			return;
		}

		// Update the metadata.
		update_post_meta( $post_id, 'rt-person-meta-basic', $meta_value );
	}

	/**
	 * Sanitize the birthdate.
	 * The date should be in the format YYYY-MM-DD.
	 *
	 * @param string $birth_date The birthdate.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function sanitize_birth_date( string $birth_date ) : string {
		// Remove all whitespace from the string.
		$birth_date = sanitize_text_field( $birth_date );

		// Check whether the date is in the format YYYY-MM-DD.
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $birth_date ) ) {
			return $birth_date;
		}

		return '';
	}

	/**
	 * Sanitize the birthplace.
	 *
	 * @param string $birth_place The birthplace.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function sanitize_birth_place( string $birth_place ) : string {
		// Remove all whitespace from the string.
		$birth_place = trim( $birth_place );

		/**
		 * Check whether the birthplace contains only alphabets and spaces.
		 * The birthplace can contain multiple words.
		 */
		if ( preg_match( '/^\w+(  ?\w+)*$/', $birth_place ) ) {
			return $birth_place;
		}

		return '';
	}

	/**
	 * Add the birthdate to the metadata, if sent by user.
	 *
	 * @param array $meta_value The meta data.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_birth_date_to_meta_data( array $meta_value ) : array {
		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return $meta_value;
		}

		$birth_date = filter_input( INPUT_POST, 'rt-person-meta-basic-birth-date', FILTER_SANITIZE_STRING );

		// Check whether the birthdate is sent by user and is valid or not.
		if ( $birth_date ) {
			$birth_date = self::sanitize_birth_date( $birth_date );

			// Add the birthdate to the metadata, if it is not empty.
			if ( '' !== $birth_date ) {
				$meta_value['rt-person-meta-basic-birth-date'] = $birth_date;
			}
		}

		return $meta_value;
	}

	/**
	 * Add the birthplace to the metadata, if sent by user.
	 *
	 * @param array $meta_value The meta data.
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_birth_place_to_meta_data( array $meta_value ) : array {
		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return $meta_value;
		}

		$birth_place = filter_input( INPUT_POST, 'rt-person-meta-basic-birth-place', FILTER_SANITIZE_STRING );

		// Check whether the birthplace is sent by user and is valid or not.
		if ( $birth_place ) {
			$birth_place = self::sanitize_birth_place( $birth_place );

			// Add the birthplace to the metadata, if it is not empty.
			if ( $birth_place ) {
				$meta_value['rt-person-meta-basic-birth-place'] = $birth_place;
			}
		}

		return $meta_value;
	}

}

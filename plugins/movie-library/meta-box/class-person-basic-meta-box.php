<?php
/**
 * Person Basic Meta Box which will be used to add basic information about the person like birthdate, birthplace.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

use Movie_Library\Custom_Post_Type\Person;

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
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'person_meta_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'person_meta_enqueue_styles' ) );
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
			Person::SLUG,
			'side',
		);
	}

	/**
	 * Enqueue person meta box validation scripts.
	 */
	public static function person_meta_enqueue_scripts() : void {
		// only enqueue script on rt-person post type.
		if ( Person::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'rt-person-validation',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/person-validation.js',
			array( 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/person-validation.js' ),
			true
		);
	}

	/**
	 * Enqueue person meta box styles.
	 */
	public static function person_meta_enqueue_styles() : void {
		// only enqueue styles on rt-person post type.
		if ( Person::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_style(
			'rt-person-meta-box-css',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/css/meta-box.css',
			array(),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/css/meta-box.css' ),
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

		<div id="rt-person-meta-basic-birth-place-error" class="rt-error">
		</div>
		<label for='rt-person-meta-basic-birth-place' >
			<?php esc_html_e( 'Birth Place', 'movie-library' ); ?>
		</label>
		<input
			type='text'
			class='widefat'
			name='rt-person-meta-basic-birth-place'
			id='rt-person-meta-basic-birth-place'
			placeholder='Birth Place'
			autocomplete='off'
			value='<?php echo esc_attr( $person_basic_meta_data['rt-person-meta-basic-birth-place'] ); ?>'
		/>

		<div id="rt-person-meta-basic-full-name-error" class="rt-error">
		</div>
		<label for='rt-person-meta-basic-full-name' >
			<?php esc_html_e( 'Full Name', 'movie-library' ); ?>
		</label>
		<input
			type='text'
			class='widefat'
			name='rt-person-meta-basic-full-name'
			id='rt-person-meta-basic-full-name'
			placeholder='Full Name'
			autocomplete='off'
			value='<?php echo esc_attr( $person_basic_meta_data['rt-person-meta-basic-full-name'] ); ?>'
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
		$meta_keys = array(
			'rt-person-meta-basic-birth-date',
			'rt-person-meta-basic-birth-place',
			'rt-person-meta-basic-full-name',
		);

		$data = array();
		foreach ( $meta_keys as $meta_key ) {
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			// If the meta value is empty, set it to empty string.
			$data[ $meta_key ] = $meta_value ?? '';
		}

		return $data;
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

		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_DEFAULT );

		// Check whether the nonce is set and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return;
		}

		$meta_data = array();

		// collect the meta data.
		$birth_date  = self::add_birth_date_to_meta_data();
		$birth_place = self::add_birth_place_to_meta_data();
		$full_name   = self::add_full_name_to_meta_data();

		// put it all in an array.
		$meta_data['rt-person-meta-basic-birth-date']  = $birth_date;
		$meta_data['rt-person-meta-basic-birth-place'] = $birth_place;
		$meta_data['rt-person-meta-basic-full-name']   = $full_name;

		// add the meta data to database.
		foreach ( $meta_data as $meta_key => $meta_value ) {
			self::add_meta_data_to_database( $meta_key, $meta_value, $post_id );
		}
	}

	/**
	 * Add the meta data to database.
	 *
	 * @param string $meta_key The meta key.
	 * @param string $meta_value The meta value.
	 * @param int    $post_id The post id.
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_meta_data_to_database( string $meta_key, string $meta_value, int $post_id ) : void {
		// Delete the meta data if no data is sent by user.
		if ( empty( $meta_value ) ) {
			delete_post_meta( $post_id, $meta_key );
			return;
		}

		// Update the metadata.
		update_post_meta( $post_id, $meta_key, $meta_value );
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
		if ( preg_match( '/^[a-zA-Z, ]+$/', $birth_place ) ) {
			return $birth_place;
		}

		return '';
	}

	/**
	 * Sanitize the full name.
	 *
	 * @param string $full_name The full name.
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function sanitize_full_name( string $full_name ) : string {
		// Remove all whitespace from the string.
		$full_name = trim( $full_name );

		/**
		 * Check whether the full name contains only alphabets, numbers, and spaces.
		 * The full name can contain multiple words.
		 */
		if ( preg_match( '/^[a-zA-Z0-9 ]+$/', $full_name ) ) {
			return $full_name;
		}

		return '';
	}

	/**
	 * Add the birthdate to the metadata, if sent by user.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_birth_date_to_meta_data() : string {
		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_DEFAULT );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return '';
		}

		$birth_date = filter_input( INPUT_POST, 'rt-person-meta-basic-birth-date', FILTER_DEFAULT );

		// Check whether the birthdate is sent by user and is valid or not.
		if ( $birth_date ) {
			return self::sanitize_birth_date( $birth_date );
		}

		return '';
	}

	/**
	 * Add the birthplace to the metadata, if sent by user.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_birth_place_to_meta_data() : string {
		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_DEFAULT );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return '';
		}

		$birth_place = filter_input( INPUT_POST, 'rt-person-meta-basic-birth-place', FILTER_DEFAULT );

		// Check whether the birthplace is sent by user and is valid or not.
		if ( $birth_place ) {
			return self::sanitize_birth_place( $birth_place );
		}

		return '';
	}

	/**
	 * Add the full name to the metadata, if sent by user.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_full_name_to_meta_data(): string {
		$nonce = filter_input( INPUT_POST, 'rt-person-basic-meta-box-nonce', FILTER_DEFAULT );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-basic-meta-box' ) ) {
			return '';
		}

		$full_name = filter_input( INPUT_POST, 'rt-person-meta-basic-full-name', FILTER_DEFAULT );

		// Check whether the full name is sent by user and is valid or not.
		if ( $full_name ) {
			return self::sanitize_full_name( $full_name );
		}

		return '';
	}

}

<?php
/**
 * Social Meta Box. It adds the boxes for different social media links.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

use Movie_Library\Custom_Post_Type\Person;
use Movie_Library\APIs\Movie_Library_Metadata_API;

/**
 * Class Social_Meta_Box
 * It adds the boxes for different social media links.
 *
 * @package Movie_Library\Meta_Box
 */
abstract class Social_Meta_Box {

	/**
	 * Array of social media.
	 * It used to avoid the code duplication and hard coding.
	 *
	 * @var array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static $social_arr = array(
		array(
			'name' => 'Twitter',
			'type' => 'twitter',
			'id'   => 'rt-person-meta-social-twitter',
		),
		array(
			'name' => 'Facebook',
			'type' => 'facebook',
			'id'   => 'rt-person-meta-social-facebook',
		),
		array(
			'name' => 'Instagram',
			'type' => 'instagram',
			'id'   => 'rt-person-meta-social-instagram',
		),
		array(
			'name' => 'Website',
			'type' => 'website',
			'id'   => 'rt-person-meta-social-web',
		),
	);

	/**
	 * It adds the social meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return void
	 *
	 * @hooked add_meta_boxes add_social_meta_box
	 * @hooked save_post_rt-person save_social_meta_data
	 */
	public static function init() : void {
		// Add the meta box.
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_social_meta_box' ) );

		// Save the meta box.
		add_action( 'save_post_rt-person', array( __CLASS__, 'save_social_meta_data' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'social_meta_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'social_meta_enqueue_styles' ) );
	}

	/**
	 * Adds the social meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function add_social_meta_box() : void {
		// Add the meta box.
		add_meta_box(
			'rt-person-meta-social',
			__( 'Social Information', 'movie-library' ),
			array( __CLASS__, 'render_social_meta_box' ),
			Person::SLUG,
			'side',
		);
	}

	/**
	 * Enqueue social link validation scripts.
	 */
	public static function social_meta_enqueue_scripts() : void {
		// only enqueue script on rt-person post type.
		if ( Person::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'rt-social-validation',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/social-validation.js',
			array( 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/social-validation.js' ),
			true
		);
	}

	/**
	 * Enqueue social meta box styles.
	 */
	public static function social_meta_enqueue_styles() : void {
		// only enqueue styles on rt-person post type.
		if ( Person::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_style(
			'rt-social-meta-box-css',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/css/meta-box.css',
			array(),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/css/meta-box.css' ),
		);
	}

	/**
	 * Renders the social meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_social_meta_box( \WP_Post $post ) : void {
		// Get the social metadata.
		$social_meta_data = self::get_social_meta_data( $post->ID );

		// add nonce field.
		wp_nonce_field( 'rt-person-meta-social', 'rt-person-meta-social-nonce' );

		// Render the social meta box for all the social media.
		foreach ( self::$social_arr as $social ) {
			self::render_social_meta_box_section(
				$social,
				$social_meta_data[ $social['id'] ]
			);
		}
	}

	/**
	 * Renders the social meta box for given social platform.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array  $social_data Social data.
	 * @param string $data        Data.
	 * @return void
	 */
	public static function render_social_meta_box_section( array $social_data, string $data ) : void {
		?>
		<div id='<?php echo esc_attr( $social_data['id'] . '-error' ); ?>' class='rt-error'>
		</div>
		<label for='<?php echo esc_attr( $social_data['id'] ); ?>' >
			<?php esc_html( $social_data['name'] ); ?>
		</label>
		<input
			type='text'
			class='widefat'
			name='<?php echo esc_attr( $social_data['id'] ); ?>'
			id='<?php echo esc_attr( $social_data['id'] ); ?>'
			value='<?php echo esc_attr( $data ); ?>'
			placeholder='<?php echo esc_attr( $social_data['name'] ); ?> profile link'
		/>
		<?php
	}

	/**
	 * Ge the social media links and add it to the array.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param int $post_id ID.
	 * @return array
	 */
	public static function get_social_meta_data( int $post_id ) : array {
		// Get the social metadata.
		$data = Movie_Library_Metadata_API::get_person_meta( $post_id, 'rt-person-meta-social', true );

		$value = array();

		// Add the social media links to the array.
		foreach ( self::$social_arr as $social ) {
			$value[ $social['id'] ] = $data[ $social['id'] ] ?? '';
		}

		return $value;
	}

	/**
	 * Save the social media links into the metadata.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param int $post_id ID.
	 * @return void
	 */
	public static function save_social_meta_data( int $post_id ) : void {
		// Check whether the request type is POST.
		if ( ! isset( $_POST ) ) {
			return;
		}

		// Check whether the user has the permission to edit the post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check whether the post is an autosave or a revision.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-person-meta-social-nonce', FILTER_DEFAULT );

		// Check whether the nonce is set and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-person-meta-social' ) ) {
			return;
		}

		$meta_value = array();

		// Add the social media links to the array.
		foreach ( self::$social_arr as $social ) {
			$meta_value = self::add_social_to_meta_data_by_id( $meta_value, $social['id'] );
		}

		// save it to new database.
		foreach ( $meta_value as $key => $value ) {
			Movie_Library_Metadata_API::update_person_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Add the social media url to the array and also sanitize the url.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param array  $meta_value Meta value.
	 * @param string $social_id  Social ID.
	 * @return array
	 */
	public static function add_social_to_meta_data_by_id( array $meta_value, string $social_id ) : array {
		$nonce = filter_input( INPUT_POST, 'rt-person-meta-social-nonce', FILTER_DEFAULT );

		if ( ! wp_verify_nonce( $nonce, 'rt-person-meta-social' ) ) {
			return $meta_value;
		}

		$url = filter_input( INPUT_POST, $social_id, FILTER_SANITIZE_URL );

		// Check whether the social media link is set.
		if ( $url ) {
			// Sanitize the url.
			$url = self::sanitize_url( $url );

			// Add the url to the array.
			if ( $url ) {
				$meta_value[ $social_id ] = $url;
			}
		}

		return $meta_value;
	}

	/**
	 * Sanitize the url.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @param string $url URL.
	 * @return string
	 */
	public static function sanitize_url( string $url ) : string {
		// Sanitize the url.
		$url = trim( $url );
		$url = esc_url_raw( $url, array( 'https', 'http' ) );
		return filter_var( $url, FILTER_SANITIZE_URL );
	}
}

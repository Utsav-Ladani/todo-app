<?php
/**
 * Basic Meta Box with rating, runtime and release date
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

use Movie_Library\Custom_Post_Type\Movie;

/**
 * Class Basic_Meta_Box
 *
 * Create the basic meta box with rating, runtime and release date.
 * Also save it to the database using update_post_meta function.
 */
abstract class Basic_Meta_Box {

	/**
	 * Initialize the class and add callbacks to hooks.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_basic_meta_box' ) );
		add_action( 'save_post_rt-movie', array( __CLASS__, 'save_basic_meta_data' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'movie_meta_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'movie_meta_enqueue_styles' ) );
	}

	/**
	 * Add the basic meta box to the rt-movie post type.
	 */
	public static function add_basic_meta_box() : void {
		add_meta_box(
			'rt-movie-meta-basic',
			__( 'Basic', 'movie-library' ),
			array( __CLASS__, 'render_basic_meta_box' ),
			Movie::SLUG,
			'side',
		);
	}

	/**
	 * Enqueue movie meta box validation scripts.
	 */
	public static function movie_meta_enqueue_scripts() : void {
		// only enqueue scripts on rt-movie post type.
		if ( Movie::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'rt-movie-validation',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/movie-validation.js',
			array( 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/movie-validation.js' ),
			true
		);
	}

	/**
	 * Enqueue movie meta box styles.
	 */
	public static function movie_meta_enqueue_styles() : void {
		// only enqueue styles on rt-movie post type.
		if ( Movie::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_style(
			'rt-movie-meta-box-css',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/css/meta-box.css',
			array(),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/css/meta-box.css' ),
		);
	}

	/**
	 * Render the basic meta box.
	 *
	 * @param \WP_Post $post The post object.
	 */
	public static function render_basic_meta_box( \WP_Post $post ) : void {

		// get meta data from database.
		$basic_meta_data = self::get_basic_meta_data( $post->ID );

		// add nonce field.
		wp_nonce_field( 'rt-movie-meta-basic', 'rt-movie-meta-basic-nonce' );

		// add html tags.
		?>
		<div id="rt-movie-meta-basic-rating-error" class="rt-error">
		</div>
		<label for='rt-movie-meta-basic-rating' >
			<?php esc_html_e( 'Rating ( Between 0 to 10 )', 'movie-library' ); ?>
		</label>
		<input
			type='number'
			class='widefat'
			name='rt-movie-meta-basic-rating'
			id='rt-movie-meta-basic-rating'
			placeholder='Rating'
			autoComplete='off'
			min='0'
			max='10'
			value=<?php echo esc_attr( $basic_meta_data['rt-movie-meta-basic-rating'] ); ?>
		/>

		<div id="rt-movie-meta-basic-runtime-error" class="rt-error">
		</div>
		<label for='rt-movie-meta-basic-runtime' >
			<?php esc_html_e( 'Runtime', 'movie-library' ); ?>
		</label>
		<input
			type='number'
			class='widefat'
			name='rt-movie-meta-basic-runtime'
			id='rt-movie-meta-basic-runtime'
			placeholder='Runtime in minutes'
			autoComplete='off'
			min='0'
			max='5000'
			value='<?php echo esc_attr( $basic_meta_data['rt-movie-meta-basic-runtime'] ); ?>'
		/>

		<label for='rt-movie-meta-basic-release-date' >
			<?php esc_html_e( 'Release Date', 'movie-library' ); ?>
		</label>
		<input
			type='date'
			class='widefat'
			name='rt-movie-meta-basic-release-date'
			id='rt-movie-meta-basic-release-date'
			min='1800-01-01'
			value='<?php echo esc_attr( $basic_meta_data['rt-movie-meta-basic-release-date'] ); ?>'
		/>
		<?php
	}

	/**
	 * Get the basic metadata from the database.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array
	 */
	public static function get_basic_meta_data( int $post_id ) : array {
		$meta_keys = array(
			'rt-movie-meta-basic-rating',
			'rt-movie-meta-basic-runtime',
			'rt-movie-meta-basic-release-date',
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
	 * Save the basic meta data to the database.
	 *
	 * @param int $post_id The post id.
	 */
	public static function save_basic_meta_data( int $post_id ) : void {
		// check if the post data is set and not empty.
		if ( ! isset( $_POST ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-movie-meta-basic-nonce', FILTER_DEFAULT );

		// check if the nonce is set and valid.
		if ( ! wp_verify_nonce( $nonce, 'rt-movie-meta-basic' )
		) {
			return;
		}

		// check if the current user has permission to edit the post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// check if the post is autosave or revision, then return.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$meta_data = array();

		$rating       = self::add_rating_to_meta_value();
		$runtime      = self::add_runtime_to_meta_value();
		$release_date = self::add_release_date_to_meta_value();

		$meta_data['rt-movie-meta-basic-rating']       = $rating;
		$meta_data['rt-movie-meta-basic-runtime']      = $runtime;
		$meta_data['rt-movie-meta-basic-release-date'] = $release_date;

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
	 * Sanitize the rating.
	 *
	 * @param string $rating The rating.
	 *
	 * @return string
	 */
	public static function sanitize_rating( string $rating ) : string {
		$rating = sanitize_text_field( $rating );

		// check if the rating is numeric and between 0 and 10.
		if ( is_numeric( $rating )
			&& (int) $rating >= 0
			&& (int) $rating <= 10
		) {
			return (string) $rating;
		}

		return '';
	}

	/**
	 * Add the rating to the meta value.
	 *
	 * @return string
	 */
	public static function add_rating_to_meta_value() : string {
		$rating = filter_input( INPUT_POST, 'rt-movie-meta-basic-rating', FILTER_DEFAULT );

		// check if the rating is set and not empty.
		if ( $rating ) {
			return self::sanitize_rating( $rating );
		}

		return '';
	}

	/**
	 * Sanitize the runtime.
	 *
	 * @param string $runtime The runtime.
	 *
	 * @return string
	 */
	public static function sanitize_runtime( string $runtime ) : string {
		$runtime = sanitize_text_field( $runtime );

		// check if the runtime is in the format hh:mm.
		if (
			preg_match( '/^[0-9]+$/', $runtime ) &&
			(int) $runtime >= 0 &&
			(int) $runtime <= 5000
		) {
			return $runtime;
		}

		return '';
	}

	/**
	 * Add the runtime to the meta value.
	 *
	 * @return string
	 */
	public static function add_runtime_to_meta_value() : string {
		$runtime = filter_input( INPUT_POST, 'rt-movie-meta-basic-runtime', FILTER_DEFAULT );

		// check if the runtime is not empty.
		if ( $runtime ) {
			return self::sanitize_runtime( $runtime );
		}

		return '';
	}

	/**
	 * Sanitize the release date.
	 *
	 * @param string $release_date The release date.
	 *
	 * @return string
	 */
	public static function sanitize_release_date( string $release_date ) : string {
		$release_date = sanitize_text_field( $release_date );

		// check if the release date is in the format yyyy-mm-dd.
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $release_date ) ) {
			return $release_date;
		}

		return '';
	}

	/**
	 * Add the release date to the meta value.
	 *
	 * @return string
	 */
	public static function add_release_date_to_meta_value() : string {
		$release_date = filter_input( INPUT_POST, 'rt-movie-meta-basic-release-date', FILTER_DEFAULT );

		if ( $release_date ) {
			return self::sanitize_release_date( $release_date );
		}

		return '';
	}
}

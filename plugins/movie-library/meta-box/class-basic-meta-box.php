<?php
/**
 * Basic Meta Box with rating, runtime and release date
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

/**
 * Class Basic_Meta_Box
 *
 * Create the basic meta box with rating, runtime and release date.
 * Also save it to the database using update_post_meta function.
 */
abstract class Basic_Meta_Box {

	/**
	 * Initialize the class and add callbacks to hooks.
	 */
	public static function init() : void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_basic_meta_box' ) );
		add_action( 'save_post_rt-movie', array( __CLASS__, 'save_basic_meta_data' ) );
	}

	/**
	 * Add the basic meta box to the rt-movie post type.
	 */
	public static function add_basic_meta_box() : void {
		add_meta_box(
			'rt-movie-meta-basic',
			__( 'Basic', 'movie-library' ),
			array( __CLASS__, 'render_basic_meta_box' ),
			'rt-movie',
			'side',
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
		<label for='rt-movie-meta-basic-rating' > Rating </label>
		<input
			type='number'
			class='widefat'
			name='rt-movie-meta-basic-rating'
			id='rt-movie-meta-basic-rating'
			min='0'
			max='10'
			value=<?php echo esc_attr( $basic_meta_data['rt-movie-meta-basic-rating'] ); ?>
		/>

		<label for='rt-movie-meta-basic-runtime' > Runtime </label>
		<input
			type='text'
			class='widefat'
			name='rt-movie-meta-basic-runtime'
			id='rt-movie-meta-basic-runtime'
			value='<?php echo esc_attr( $basic_meta_data['rt-movie-meta-basic-runtime'] ); ?>'
		/>

		<label for='rt-movie-meta-basic-release-date' > Release Date </label>
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
		$data = get_post_meta( $post_id, 'rt-movie-meta-basic', true );

		// if metadata is not set, return empty string.
		return array(
			'rt-movie-meta-basic-rating'       => $data['rt-movie-meta-basic-rating'] ?? '',
			'rt-movie-meta-basic-runtime'      => $data['rt-movie-meta-basic-runtime'] ?? '',
			'rt-movie-meta-basic-release-date' => $data['rt-movie-meta-basic-release-date'] ?? '',
		);
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

		$nonce = filter_input( INPUT_POST, 'rt-movie-meta-basic-nonce', FILTER_SANITIZE_STRING );

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

		// sanitize and add data to array.
		$meta_value = array();
		$meta_value = self::add_rating_to_meta_value( $meta_value );
		$meta_value = self::add_runtime_to_meta_value( $meta_value );
		$meta_value = self::add_release_date_to_meta_value( $meta_value );

		// delete the metadata if the array is empty.
		if ( count( $meta_value ) === 0 ) {
			delete_post_meta( $post_id, 'rt-movie-meta-basic' );
			return;
		}

		// update the metadata.
		update_post_meta( $post_id, 'rt-movie-meta-basic', $meta_value );
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
	 * Add the rating to the meta value array.
	 *
	 * @param array $meta_value The meta value array.
	 *
	 * @return array
	 */
	public static function add_rating_to_meta_value( array $meta_value ) : array {
		$rating = filter_input( INPUT_POST, 'rt-movie-meta-basic-rating', FILTER_SANITIZE_STRING );

		// check if the rating is set and not empty.
		if ( $rating ) {
			$rating = self::sanitize_rating( $rating );
		}

		// add the rating to the array.
		if ( $rating ) {
			$meta_value['rt-movie-meta-basic-rating'] = $rating;
		}

		return $meta_value;
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
		if ( preg_match( '/^[0-9]{2}:[0-9]{2}$/', $runtime ) ) {
			return $runtime;
		}

		return '';
	}

	/**
	 * Add the runtime to the meta value array.
	 *
	 * @param array $meta_value The meta value array.
	 *
	 * @return array
	 */
	public static function add_runtime_to_meta_value( array $meta_value ) : array {
		$runtime = filter_input( INPUT_POST, 'rt-movie-meta-basic-runtime', FILTER_SANITIZE_STRING );

		// check if the runtime is not empty.
		if ( $runtime ) {
			$runtime = self::sanitize_runtime( $runtime );

			// add the runtime to the array.
			if ( $runtime ) {
				$meta_value['rt-movie-meta-basic-runtime'] = $runtime;
			}
		}

		return $meta_value;
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
	 * Add the release date to the meta value array.
	 *
	 * @param array $meta_value The meta value array.
	 *
	 * @return array
	 */
	public static function add_release_date_to_meta_value( array $meta_value ) : array {
		$release_date = filter_input( INPUT_POST, 'rt-movie-meta-basic-release-date', FILTER_SANITIZE_STRING );

		// check if the release date is not empty.
		if ( $release_date ) {
			$release_date = self::sanitize_release_date( $release_date );

			// add the release date to the array.
			if ( $release_date ) {
				$meta_value['rt-movie-meta-basic-release-date'] = $release_date;
			}
		}

		return $meta_value;
	}
}

<?php
/**
 * Images Meta Box.
 * This class is responsible for adding image meta box to movie and person post type.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

/**
 * Class Images_Meta_Box
 * Adds images meta box to movie and person post type.
 */
abstract class Images_Meta_Box {
	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @hooked action add_meta_boxes
	 * @hooked action admin_enqueue_scripts
	 * @hooked action save_post_rt-movie
	 * @hooked action save_post_rt-person
	 */
	public static function init() : void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_image_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_image_media_script' ) );
		add_action( 'save_post_rt-movie', array( __CLASS__, 'save_post_images' ) );
		add_action( 'save_post_rt-person', array( __CLASS__, 'save_post_images' ) );
	}

	/**
	 * Adds images meta box to movie and person post type.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_image_meta_box() : void {
		// Add images meta box to movie post type.
		add_meta_box(
			'rt-media-meta-images',
			__( 'Images', 'movie-library' ),
			array( __CLASS__, 'render_image_meta_box' ),
			'rt-movie',
			'side',
		);

		// Add images meta box to person post type.
		add_meta_box(
			'rt-media-meta-images',
			__( 'Images', 'movie-library' ),
			array( __CLASS__, 'render_image_meta_box' ),
			'rt-person',
			'side',
		);
	}

	/**
	 * Adds media script to admin page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_image_media_script() : void {
		// check is post type is not rt-movie or rt-person, then return.
		if ( ! in_array( get_post_type(), array( 'rt-movie', 'rt-person' ), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'movie-library-image-upload-handler',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/image-upload-handler.js',
			array( 'jquery', 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/image-upload-handler.js' ),
			true
		);
	}

	/**
	 * Renders images meta box.
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_image_meta_box( \WP_Post $post ) : void {
		// Get metadata for images.
		$images = get_post_meta( $post->ID, 'rt-media-meta-images', true );

		// If images is not an array, make it an array.
		$images = is_array( $images ) ? $images : array();

		// Remove images which are not present in media library.
		$images = self::remove_unlinked_images( $images );

		$input_value = implode( ',', $images );

		// Add nonce for security and authentication.
		wp_nonce_field( 'rt-upload-images', 'rt-upload-images-nonce' );

		// Add the HTML in Meta Box.
		?>
		<div id='movie-library-image-upload-handler'>
			<div id='image-preview-container'>
				<?php
				// Build the image tags.
				self::build_image_tags_list( $images );
				?>
			</div>
			<button id='add-images-custom-btn' class='button button-primary widefat'>
				<?php esc_html_e( 'Add Images', 'movie-library' ); ?>
			</button>
			<input
				type='hidden'
				name='rt-upload-images'
				id='rt-upload-images'
				value='<?php echo esc_attr( $input_value ); ?>'
			/>
		<?php
	}

	/**
	 * Saves images meta box data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function save_post_images( int $post_id ) : void {
		// Check whether request type is post and if rt-upload-images is set.
		if ( ! isset( $_POST ) || ! isset( $_POST['rt-upload-images'] ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-upload-images-nonce', FILTER_DEFAULT );

		// Check whether nonce is set and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-upload-images' ) ) {
			return;
		}

		// Sanitize and explode the data.
		$images = filter_input(
			INPUT_POST,
			'rt-upload-images',
			FILTER_DEFAULT,
			FILTER_REQUIRE_SCALAR
		);
		$images = explode( ',', $images );

		// sanitize the values by remove not integer values.
		$image_ids = array();
		foreach ( $images as $image ) {
			if ( (int) $image ) {
				$image_ids[] = (int) $image;
			}
		}

		// Update the post meta.
		update_post_meta( $post_id, 'rt-media-meta-images', $image_ids );
	}

	/**
	 * Removes images which are not present in media library.
	 *
	 * @param array $images Array of image ids.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function remove_unlinked_images( array $images ) : array {
		return array_filter(
			$images,
			function( $image ) {
				return wp_get_attachment_url( $image ) !== false;
			}
		);
	}

	/**
	 * Builds image tags list.
	 *
	 * @param array $images Array of image ids.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function build_image_tags_list( array $images ) : void {
		foreach ( $images as $image ) {
			?>
			<div
				class='image-item widefat'
				data-image-id='<?php echo esc_attr( $image ); ?>'
			>
				<img
					src='<?php echo esc_url( wp_get_attachment_url( $image ) ); ?>'
					alt='<?php echo esc_attr( get_the_title( $image ) ); ?>'
					class='widefat'
				/>

				<button
					class='button rt-remove-image-btn widefat'
					data-image-id='<?php echo esc_attr( $image ); ?>'
				>
					<?php esc_html_e( 'Remove', 'movie-library' ); ?>
				</button>
			</div>
			<?php
		}
	}
}

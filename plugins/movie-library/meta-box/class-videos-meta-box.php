<?php
/**
 * Videos Meta Box
 * This class is responsible for adding video meta box to movie and person post type.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

/**
 * Class Videos_Meta_Box
 * Add video meta box to movie and person post type.
 */
class Videos_Meta_Box {
	/**
	 * Initialize the class
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @action add_meta_boxes
	 * @action admin_enqueue_scripts
	 * @action save_post_rt-movie
	 * @action save_post_rt-person
	 */
	public static function init() : void {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_video_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_video_media_script' ) );
		add_action( 'save_post_rt-movie', array( __CLASS__, 'save_post_videos' ) );
		add_action( 'save_post_rt-person', array( __CLASS__, 'save_post_videos' ) );
	}

	/**
	 * Add video meta box to movie and person post type.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_video_meta_box() : void {
		// Add video meta box to movie post type.
		add_meta_box(
			'rt-media-meta-videos',
			__( 'Videos', 'movie-library' ),
			array( __CLASS__, 'render_video_meta_box' ),
			'rt-movie',
			'side',
		);

		// Add video meta box to person post type.
		add_meta_box(
			'rt-media-meta-videos',
			__( 'Videos', 'movie-library' ),
			array( __CLASS__, 'render_video_meta_box' ),
			'rt-person',
			'side',
		);
	}

	/**
	 * Add video media script.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_video_media_script() : void {
		wp_enqueue_media();
		wp_enqueue_script(
			'movie-library-video-upload-handler',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/video-upload-handler.js',
			array( 'jquery', 'wp-i18n' ),
			MOVIE_LIBRARY_VERSION,
			true
		);
	}

	/**
	 * Render video meta box.
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_video_meta_box( \WP_Post $post ) : void {
		// Get the videos metadata.
		$videos = get_post_meta( $post->ID, 'rt-media-meta-videos', true );

		// If videos is not an array, make it an array.
		$videos = is_array( $videos ) ? $videos : array();

		// Remove videos which are removed from the media library.
		$videos = self::remove_unlinked_videos( $videos );

		// Build the video tags list.
		$video_list = self::build_video_tags_list( $videos );

		$input_value = implode( ',', $videos );

		// Render the video meta box.
		?>
		<div id='movie-library-video-upload-handler'>
			<div id='video-preview-container'>
				<?php echo $video_list; ?>
			</div>
			<button id='add-videos-custom-btn' class='button button-primary widefat'>
				<?php esc_html_e( 'Add Videos', 'movie-library' ); ?>
			</button>
			<input
				type='hidden'
				name='rt-upload-videos'
				id='rt-upload-videos'
				value='<?php echo esc_attr( $input_value ); ?>'
			/>
		<?php
	}

	/**
	 * Save videos meta data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function save_post_videos( int $post_id ) : void {
		// Check whether the request type is post and rt-upload-videos is set.
		if ( ! isset($_POST) || ! isset( $_POST['rt-upload-videos'] ) ) {
			return;
		}

		// Sanitize the input.
		$videos = filter_input(
			INPUT_POST,
			'rt-upload-videos',
			FILTER_SANITIZE_STRING,
			FILTER_REQUIRE_SCALAR
		);

		// Explode the input.
		$videos = explode( ',', $videos );

		$video_ids = [];

		// Remove the non-integer values.
		foreach ( $videos as $video ) {
			if( (int) $video ) {
				$video_ids[] = (int)$video;
			}
		}

		// Update the post meta.
		update_post_meta( $post_id, 'rt-media-meta-videos', $video_ids );
	}

	/**
	 * Remove unlinked videos.
	 *
	 * @param array $videos Video IDs.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function remove_unlinked_videos( array $videos ) : array {
		return array_filter(
			$videos,
			function( $video ) {
				return wp_get_attachment_url( $video ) !== false;
			}
		);
	}

	/**
	 * Build video tags list.
	 *
	 * @param array $videos Video IDs.
	 *
	 * @return string
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function build_video_tags_list( array $videos ) : string {
		$video_list = '';
		foreach ( $videos as $video ) {
			// Build the video source tag.
			$video_source = sprintf(
				'<source src="%s">',
				esc_attr( wp_get_attachment_url( $video ) ),
			);

			// Build the video tag.
			$video_tag = sprintf(
				'<video class="widefat" controls>%s %s</video>',
				$video_source,
				esc_attr__( get_the_title( $video ) )
			);

			// Build the remove button.
			$remove_btn = sprintf(
				'<button class="button rt-remove-video-btn widefat" data-video-id="%d"> Remove </button>',
				$video
			);

			// Build the video item.
			$video_list .= sprintf(
				'<div class="video-item" data-video-id="%d">%s%s</div>',
				$video,
				$video_tag,
				$remove_btn
			);
		}

		return $video_list;
	}
}
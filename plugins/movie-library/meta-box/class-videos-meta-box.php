<?php
/**
 * Videos Meta Box
 * This class is responsible for adding video meta box to movie and person post type.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

use Movie_Library\APIs\Movie_Library_Metadata_API;
use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

/**
 * Class Videos_Meta_Box
 * Add video meta box to movie and person post type.
 */
abstract class Videos_Meta_Box {
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
			Movie::SLUG,
			'side',
		);

		// Add video meta box to person post type.
		add_meta_box(
			'rt-media-meta-videos',
			__( 'Videos', 'movie-library' ),
			array( __CLASS__, 'render_video_meta_box' ),
			Person::SLUG,
			'side',
		);
	}

	/**
	 * Add video media and video upload handler script.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_video_media_script() : void {
		// Check if post type is not rt-movie or rt-person, then return.
		if ( ! in_array( get_post_type(), array( Movie::SLUG, Person::SLUG ), true ) ) {
			return;
		}

		wp_enqueue_media();

		if ( is_admin() ) {
			wp_enqueue_script(
				'movie-library-video-upload-handler',
				MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/video-upload-handler.js',
				array( 'jquery', 'wp-i18n' ),
				filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/video-upload-handler.js' ),
				true
			);
		}
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
		$videos = array();

		// Check if post type is movie or person, and based on that fetch the metadata.
		if ( Movie::SLUG === get_post_type( $post->ID ) ) {
			$videos = Movie_Library_Metadata_API::get_movie_meta( $post->ID, 'rt-media-meta-videos', true );
		} else {
			$videos = Movie_Library_Metadata_API::get_person_meta( $post->ID, 'rt-media-meta-videos', true );
		}

		// If videos is not an array, make it an array.
		$videos = is_array( $videos ) ? $videos : array();

		// Remove videos which are removed from the media library.
		$videos = self::remove_unlinked_videos( $videos );

		$input_value = implode( ',', $videos );

		// Add nonce for security and authentication.
		wp_nonce_field( 'rt-video-upload-nonce-action', 'rt-video-upload-nonce' );

		// Render the video meta box.
		?>
		<div id='movie-library-video-upload-handler'>
			<div id='video-preview-container'>
				<?php
				// Render the video tags.
				self::build_video_tags_list( $videos );
				?>
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
		if ( ! isset( $_POST ) || ! isset( $_POST['rt-upload-videos'] ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-video-upload-nonce', FILTER_DEFAULT );

		// Check whether the nonce is set and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-video-upload-nonce-action' ) ) {
			return;
		}

		// Sanitize the input.
		$videos = filter_input(
			INPUT_POST,
			'rt-upload-videos',
			FILTER_DEFAULT,
			FILTER_REQUIRE_SCALAR
		);

		// Explode the input.
		$videos = explode( ',', $videos );

		$video_ids = array();

		// Remove the non-integer values.
		foreach ( $videos as $video ) {
			if ( (int) $video ) {
				$video_ids[] = (int) $video;
			}
		}

		// Check if post type is movie or person, and based on that update the metadata.
		if ( Movie::SLUG === get_post_type( $post_id ) ) {
			Movie_Library_Metadata_API::update_movie_meta( $post_id, 'rt-media-meta-videos', $video_ids );
		} else {
			Movie_Library_Metadata_API::update_person_meta( $post_id, 'rt-media-meta-videos', $video_ids );
		}
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
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function build_video_tags_list( array $videos ) : void {
		foreach ( $videos as $video ) {
			?>
			<div class='video-item' >
				<video class='widefat' controls>
					<source src='<?php echo esc_url( wp_get_attachment_url( $video ) ); ?>' >
					<?php echo esc_html( get_the_title( $video ) ); ?>
				</video>

				<button
					class='button rt-remove-video-btn widefat'
					data-video-id='<?php echo esc_attr( $video ); ?>'
				>
					<?php esc_html_e( 'Remove', 'movie-library' ); ?>
				</button>
			</div>
			<?php
		}
	}
}

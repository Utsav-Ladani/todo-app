<?php
/**
 * Movie Library Person Videos section.
 * It displays the person video gallery.
 *
 * @package Movie Library
 */

?>

<div class="section person-videos-section">
	<h3 class="section-title">
		<?php esc_html_e( 'Videos', 'movie-library' ); ?>
	</h3>
	<ul class="videos-list">
		<?php
		$videos = get_post_meta( get_the_ID(), 'rt-media-meta-videos', true );

		if ( ! is_array( $videos ) ) {
			$videos = array();
		}

		$videos = array_slice( $videos, 0, 3 );

		foreach ( $videos as $video_id ) {
			$video_poster_url = get_the_post_thumbnail_url( $video_id );
			$video_src        = wp_get_attachment_url( $video_id );

			?>
			<li class="video-item"  style="background-image: url('<?php echo esc_url( $video_poster_url ); ?>')">
				<button class="video-btn" video-src="<?php echo esc_url( $video_src ); ?>">
					<img class="video-svg" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/play.svg" />
				</button>
			</li>
			<?php
		}
		?>
	</ul>
</div>
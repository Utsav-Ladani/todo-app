<?php
/**
 * Movie Library Movie Trailer & Clips section.
 * It displays the movie trailer and clip gallery.
 *
 * @package Movie Library
 */

?>

<div id="videos" class="section  <?php echo esc_attr( $args['class'] ?? '' ); ?>">
	<h3 class="section-title">
		<?php echo esc_html( $args['Title'] ?? esc_html__( 'Videos', 'movie-library' ) ); ?>
	</h3>
	<ul class="videos-list">
		<?php
		$videos = get_post_meta( get_the_ID(), 'rt-media-meta-videos', true );

		if ( ! is_array( $videos ) ) {
			$videos = array();
		}

		$videos = array_slice( $videos, 0, 3 );

		foreach ( $videos as $video_id ) {
			?>
			<li class="video-item"  style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( $video_id ) ); ?>')">
				<button class="video-btn" video-src="<?php echo esc_url( wp_get_attachment_url( $video_id ) ); ?>">
					<img class="video-svg" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/play.svg" alt="<?php esc_html_e( 'Play Button', 'movie-library' ); ?>" />
				</button>
			</li>
			<?php
		}
		?>
	</ul>
</div>
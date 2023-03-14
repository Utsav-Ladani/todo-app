<?php
/**
 * Movie Library Snapshot section.
 * It shows the responsive snapshot gallery.
 *
 * @package Movie Library
 */

?>

<div class="section">
	<h3 class="section-title">
		<?php esc_html_e( 'Snapshots', 'movie-library' ); ?>
	</h3>
	<ul class="snapshots-list">
		<?php
		$images = get_post_meta( get_the_ID(), 'rt-media-meta-images', true );

		if ( ! is_array( $images ) ) {
			$images = array();
		}

		$images = array_slice( $images, 0, 6 );

		foreach ( $images as $image ) {
			$image = wp_get_attachment_url( $image );

			?>
			<li class="snapshot-item">
				<img class="snapshot-image" src="<?php echo esc_url( $image ); ?>" alt="">
			</li>
			<?php
		}
		?>
	</ul>
</div>
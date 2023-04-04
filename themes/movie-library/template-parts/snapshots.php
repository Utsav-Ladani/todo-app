<?php
/**
 * Movie Library Snapshot section.
 * It displays the movie responsive snapshot gallery for movie.
 *
 * @package Movie Library
 */

?>

<div id="snapshots" class="section">
	<h3 class="section-title">
		<?php echo esc_html( $args['Title'] ?? esc_html__( 'Snapshots', 'movie-library' ) ); ?>
	</h3>
	<ul class="snapshots-list">
		<?php
		if ( \Movie_Library\Custom_Post_Type\Movie::SLUG === get_post_type( get_the_ID() ) ) {
			$images = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( get_the_ID(), 'rt-media-meta-images', true );
		} else {
			$images = \Movie_Library\APIs\Movie_Library_Metadata_API::get_person_meta( get_the_ID(), 'rt-media-meta-images', true );
		}

		if ( ! is_array( $images ) ) {
			$images = array();
		}

		$images = array_slice( $images, 0, 6 );

		foreach ( $images as $image ) {
			?>
			<li class="snapshot-item">
				<img class="snapshot-image" src="<?php echo esc_url( wp_get_attachment_url( $image ) ); ?>" alt="<?php esc_html_e( 'Snapshot Image', 'movie-library' ); ?>">
			</li>
			<?php
		}
		?>
	</ul>
</div>

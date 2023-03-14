<?php
/**
 * Movie Library Upcoming Movies.
 * It displays the upcoming movies using the rt-movie-tag taxonomy.
 * It uses the upcoming tag to show the upcoming movies.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';
?>

<div class="section">
	<h3 class="section-title">
		<?php esc_html_e( 'Upcoming Movies', 'movie-library' ); ?>
	</h3>
	<div class="movie-card-list-wrapper">
		<ul class="movie-card-list">
			<?php

			// get upcoming movies.
			$movies = get_posts(
				array(
					'post_type'      => 'rt-movie',
					'posts_per_page' => 6,
                    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'tax_query'      => array(
						array(
							'taxonomy' => 'rt-movie-tag',
							'field'    => 'slug',
							'terms'    => 'upcoming',
						),
					),
				)
			);

			foreach ( $movies as $movie ) {
				// get the image and title.
				$src  = get_thumbnail_attachment_url( $movie->ID );
				$name = $movie->post_title;

				// get the release date and format it.
				$release_date = get_post_release_date( $movie->ID, 'jS M Y' );

				// get the terms.
				$term_names = get_terms_list( $movie->ID, 'rt-movie-genre' );
				$term_name  = $term_names[0] ?? '';

				?>
				<li class="movie-card-item">
					<img class="movie-image" src="<?php echo esc_url( $src ); ?>" alt="">
					<div class="movie-info">
						<h4 class="movie-title">
							<?php echo esc_html( $name ); ?>
						</h4>
						<span class="movie-genre-item"><?php echo esc_html( $term_name ); ?></span>
						<span class="movie-date">
							<?php esc_html_e( 'Release', 'movie-library' ); ?>:
							<?php echo wp_kses( $release_date, array( 'span' => array( 'class' ) ) ); ?>
						</span>
						<span class="movie-tag">PG-13</span>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>

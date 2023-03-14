<?php
/**
 * Movie Library Cover Section.
 * It contains slider with max 4 slides.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';
?>
<div class="movie-archive-slider">
	<div class="slider-container" >
		<div class="slides">
		<?php
		$args = array(
			'post_type'      => 'rt-movie',
			'posts_per_page' => 4,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => 'rt-movie-tag',
					'field'    => 'slug',
					'terms'    => 'slider',
				),
			),
		);

		$movies       = get_posts( $args );
		$total_movies = count( $movies );

		foreach ( $movies as $movie ) {
			$src = get_thumbnail_attachment_url( $movie->ID );

			// get the runtime and format it.
			$runtime = get_post_runtime( $movie->ID );

			// get the release date and format it.
			$release_date = get_post_release_date( $movie->ID );

			?>
			<div style='background-image: url("<?php echo esc_url( $src ); ?>")' class="slide">
				<div class="slide-content">
					<h2 class="movie-cover-title"><?php echo esc_html( $movie->post_title ); ?></h2>
					<div class="movie-cover-description">
						<?php echo wp_kses_post( get_the_excerpt( $movie->ID ) ); ?>
					</div>
					<div class="meta-info-wrapper">
						<span class="basic-meta-item">
							<?php echo esc_html( $release_date ); ?>
						</span>
						<span class="basic-meta-item">
							PG-13
						</span>
						<span class="basic-meta-item">
							<?php echo esc_html( $release_date ); ?>
						</span>
					</div>
					<ul class="movie-genre">
						<?php
						$term_names = get_terms_list( $movie->ID, 'rt-movie-genre' );

						foreach ( $term_names as $term_name ) :
							?>
							<li class="movie-genre-item"><?php esc_html( $term_name ); ?></li>
							<?php
						endforeach;
						?>
					</ul>
				</div>
			</div>
			<?php
		}
		?>
		</div>
	</div>
	<div class="dots-wrapper">
		<?php
		for ( $i = 0; $i < $total_movies; $i++ ) :
			echo '<span class="dot"></span>';
		endfor;
		?>
	</div>
</div>

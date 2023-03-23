<?php
/**
 * Movie Library archive page for rt-movie post type.
 * It displays the slider, upcoming movies and trending movies section.
 * Slider contains slider with max 4 slides.
 * It displays the upcoming movies using the rt-movie-tag taxonomy.
 * It uses the upcoming tag to show the upcoming movies.
 * It shows the trending movies using the rt-movie-tag taxonomy.
 * It uses the trending tag to show the trending movies.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;
use Movie_Library\Taxonomy\Hierarchical\Genre;

get_header();
?>

	<div class="movie-archive-slider">
		<div class="slider-container" >
			<div class="slides">
				<?php
				$args = array(
					'post_type'      => Movie::SLUG,
					'posts_per_page' => 4,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'tax_query'      => array(
						array(
							'taxonomy' => Tag::SLUG,
							'field'    => 'slug',
							'terms'    => 'slider',
						),
					),
				);

				$movies       = get_posts( $args );
				$total_movies = count( $movies );

				foreach ( $movies as $movie ) {
					?>
					<div
							style='background-image: url("<?php echo esc_url( get_thumbnail_attachment_url( $movie->ID ) ); ?>")'
							class="slide"
							onclick="window.location='<?php echo esc_url( get_permalink( $movie->ID ) ); ?>'"
					>
						<div class="slide-content-wrapper">
							<div class="slide-content">
								<h2 class="movie-cover-title"><?php echo esc_html( $movie->post_title ); ?></h2>
								<div class="movie-cover-description">
									<?php echo wp_kses_post( get_the_excerpt( $movie->ID ) ); ?>
								</div>
								<div class="meta-info-wrapper">
									<span class="basic-meta-item"><?php echo esc_html( get_post_release_date( $movie->ID ) ); ?></span>
									<span class="basic-meta-item"><?php echo esc_html( 'PG-13' ); ?></span>
									<span class="basic-meta-item"><?php echo esc_html( get_post_runtime( $movie->ID, 'H', 'M' ) ); ?></span>
								</div>
								<ul class="movie-genre">
									<?php
									$term_names = get_terms_list( $movie->ID, Genre::SLUG );

									foreach ( $term_names as $term_name ) :
										// create the archive page link of term.
										$term_link = get_term_link( $term_name, Genre::SLUG );

										?>
										<li class="movie-genre-item hover-btn">
											<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term_name ); ?></a>
										</li>
										<?php
									endforeach;
									?>
								</ul>
							</div>
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
						'post_type'      => Movie::SLUG,
						'posts_per_page' => 6,
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query'      => array(
							array(
								'taxonomy' => Tag::SLUG,
								'field'    => 'slug',
								'terms'    => 'upcoming',
							),
						),
					)
				);

				foreach ( $movies as $movie ) {
					// get the terms.
					$term_names = get_terms_list( $movie->ID, Genre::SLUG );
					$term_name  = $term_names[0] ?? '';
					?>
					<li class="movie-card-item">
						<a class="link-flex" href="<?php echo esc_url( get_permalink( $movie->ID ) ); ?>">
							<img class="movie-image" src="<?php echo esc_url( get_thumbnail_attachment_url( $movie->ID ) ); ?>" alt="" />
						</a>
						<div class="movie-info">
							<a href="<?php echo esc_url( get_permalink( $movie->ID ) ); ?>">
								<h4 class="movie-title">
									<?php echo esc_html( $movie->post_title ); ?>
								</h4>
							</a>
							<a class="movie-genre-item" href="<?php echo esc_url( get_term_link( $term_name, Genre::SLUG ) ); ?>">
								<?php echo esc_html( $term_name ); ?>
							</a>
							<span class="movie-date">
							<?php esc_html_e( 'Release', 'movie-library' ); ?>:
							<?php echo wp_kses( get_post_release_date( $movie->ID, 'jS M Y' ), array( 'span' => array( 'class' => array() ) ) ); ?>
						</span>
							<span class="movie-tag"><?php echo esc_html( 'PG-13' ); ?></span>
						</div>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</div>


<?php

get_template_part( 'template-parts/movie/trending-movies' );

get_footer();

<?php
/**
 * Movie Library Popular Movies section for an actor.
 * It contains the list of popular movie for an actor.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;
use Movie_Library\Taxonomy\Hierarchical\Genre;

?>
<div id="family" class="section">
	<h3 class="section-title">
		<?php esc_html_e( 'Popular Movies', 'movie-library' ); ?>
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
				$term_names = array_slice( $term_names, 0, 1 );

				?>
				<li class="movie-card-item">
					<img class="movie-image" src="<?php echo esc_url( get_thumbnail_attachment_url( $movie->ID ) ); ?>" alt="" />
					<div class="movie-info">
						<h4 class="movie-title">
							<?php echo esc_html( $movie->post_title ); ?>
						</h4>
						<span class="movie-runtime">
							<?php echo esc_html( get_post_runtime( $movie->ID ) ); ?>
						</span>
						<ul class="movie-genre-list">
							<?php
							foreach ( $term_names as $term_name ) {
								?>
								<li class="movie-genre-item">
									<?php echo esc_html( $term_name ); ?>
								</li>
								<?php
							}
							?>
						</ul>
						<span class="movie-date">
							<?php echo wp_kses( get_post_release_date( $movie->ID, 'jS M Y' ), array( 'span' => array( 'class' => array() ) ) ); ?>
						</span>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>

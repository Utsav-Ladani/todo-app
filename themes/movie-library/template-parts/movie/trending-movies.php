<?php
/**
 * Movie Library trending movie template.
 * It shows the trending movies using the rt-movie-tag taxonomy.
 * It uses the trending tag to show the trending movies.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';

use \Movie_Library\Custom_Post_Type\Movie;
use \Movie_Library\Taxonomy\Non_Hierarchical\Tag;
use \Movie_Library\Taxonomy\Hierarchical\Genre;
?>

<div class="section trending-movies section-padding-bottom">
	<h3 class="section-title">
		<?php esc_html_e( 'Trending Movies', 'movie-library' ); ?>
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
							'terms'    => 'trending',
						),
					),
				)
			);

			foreach ( $movies as $movie ) {
				// get the image and title.
				$src  = get_thumbnail_attachment_url( $movie->ID );
				$name = $movie->post_title;

				// get the runtime and format it.
				$runtime = get_post_runtime( $movie->ID );

				// get the release date and format it.
				$release_date = get_post_release_date( $movie->ID );

				// get the terms.
				$term_names = get_terms_list( $movie->ID, Genre::SLUG );

				?>
				<li class="movie-card-item">
					<img class="movie-image" src="<?php echo esc_url( $src ); ?>" alt="" />
					<div class="movie-info">
						<h4 class="movie-title">
							<?php echo esc_html( $name ); ?>
						</h4>
						<span class="movie-runtime">
							<?php echo esc_html( $runtime ); ?>
						</span>
						<ul class="movie-genre-list">
							<?php
							foreach ( $term_names as $term_name ) {
								?>
								<li class="movie-genre-item"><?php echo esc_html( $term_name ); ?></li>
								<?php
							}
							?>
						</ul>
						<span class="movie-date">
							<?php echo esc_html( $release_date ); ?>
						</span>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>

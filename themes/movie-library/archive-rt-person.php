<?php
/**
 * Movie Library archive page for rt-person post type.
 * It displays the cast and crew list.
 * If movie-id is not set, it will display the cast and crew of all movies.
 * If movie-id is set, it will display the cast and crew of the movie with the given id.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';
require_once get_stylesheet_directory() . '/includes/person-utility.php';

get_header();
?>

	<div class="section archive-section">
		<h3 class="section-title archive-section-title">
			<?php esc_html_e( 'Cast & Crew', 'movie-library' ); ?>
		</h3>
		<ul class="person-archive-list">
			<?php
			$movie_id = filter_input(
				INPUT_GET,
				'movie-id',
				FILTER_SANITIZE_NUMBER_INT
			);

			$persons = get_archive_cast_crew( $movie_id );

			foreach ( $persons as $person_id => $character_name ) :
				$birth_date = get_post_birth_date( $person_id, 'j F Y' );
				?>
				<li class="person-archive-item">
					<div class="person-archive-item-first">
						<a class="person-archive-image-link" href="<?php echo esc_url( get_permalink( $person_id ) ); ?>" >
							<img class="person-archive-image" src="<?php echo esc_url( get_thumbnail_attachment_url( $person_id ) ); ?>" alt="<?php echo esc_html( get_the_title( $person_id ) ); ?>" />
						</a>
						<div class="person-info-wrapper">
							<a href="<?php echo esc_url( get_permalink( $person_id ) ); ?>" >
								<h3 class="person-title">
									<span><?php echo esc_html( get_the_title( $person_id ) ); ?></span>
									<?php
									if ( $movie_id && $character_name ) {
										echo '<span class="character-name">(' . esc_html( $character_name ) . ')</span>';
									}
									?>
								</h3>
							</a>
							<div class="person-born">
								<?php echo esc_html__( 'Born', 'movie-library' ) . ' - ' . esc_html( $birth_date ); ?>
							</div>
							<div class="archive-person-excerpt hidden">
								<?php
								$excerpt = get_the_excerpt( $person_id );
								echo wp_kses_post( $excerpt );
								?>
							</div>
							<a href="<?php echo esc_url( get_permalink( $person_id ) ); ?>" class="archive-page-link hidden">
								<span><?php esc_html_e( 'Learn More', 'movie-library' ); ?></span>
								<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/arrow-red-small.svg" alt="Learn more" />
							</a>
						</div>
					</div>
					<div class="hidden-desktop person-archive-item-second">
						<div class="archive-person-excerpt">
							<?php echo esc_html( $excerpt ); ?>
						</div>
						<a href="<?php echo esc_url( get_permalink( $person_id ) ); ?>" class="archive-page-link">
							<span><?php esc_html_e( 'Learn More', 'movie-library' ); ?></span>
							<img src="<?php echo esc_html( get_stylesheet_directory_uri() ); ?>/assets/svg/arrow-red-small.svg" alt="<?php esc_html_e( 'Learn more arrow', 'movie-library' ); ?>" />
						</a>
					</div>
				</li>
				<?php
			endforeach;
			?>
		</ul>
		<div class="center-div hidden">
			<button class="load-more-btn"><?php esc_html_e( 'Load More', 'movie-library' ); ?></button>
		</div>
	</div>

<?php
get_footer();

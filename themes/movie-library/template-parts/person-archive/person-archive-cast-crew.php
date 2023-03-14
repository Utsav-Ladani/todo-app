<?php
/**
 * Movie Library Person Archive Cast Crew.
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
			$src        = get_thumbnail_attachment_url( $person_id );

			$person_name = get_the_title( $person_id );
			$person_link = get_permalink( $person_id );
			?>
			<li class="person-archive-item">
				<div class="person-archive-item-first">
					<img class="person-archive-image" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_html( $person_name ); ?>" />
					<div class="person-info-wrapper">
						<h3 class="person-title">
							<span><?php echo esc_html( $person_name ); ?></span>
							<?php
							if ( $movie_id && $character_name ) {
								echo '<span class="character-name">(' . esc_html( $character_name ) . ')</span>';
							}
							?>
						</h3>
						<div class="person-born">
							Born - <?php echo esc_html( $birth_date ); ?>
						</div>
						<div class="archive-person-excerpt hidden">
							<?php
							$excerpt = get_the_excerpt( $person_id );
							$excerpt = wp_html_excerpt( $excerpt, 113, '...' );
							echo esc_html( $excerpt );
							?>
						</div>
						<a href="<?php echo esc_url( $person_link ); ?>" class="archive-page-link hidden">
							<span><?php esc_html_e( 'Learn More', 'movie-library' ); ?></span>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/arrow-red-small.svg" alt="Learn more" />
						</a>
					</div>
				</div>
				<div class="hidden-desktop person-archive-item-second">
					<div class="archive-person-excerpt">
						<?php echo esc_html( $excerpt ); ?>
					</div>
					<a href="<?php echo esc_url( $person_link ); ?>" class="archive-page-link">
						<span><?php esc_html_e( 'Learn More', 'movie-library' ); ?></span>
						<img src="<?php echo esc_html( get_stylesheet_directory_uri() ); ?>/assets/svg/arrow-red-small.svg" alt="Learn more" />
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
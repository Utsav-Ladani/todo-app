<?php
/**
 * Movie Library Cover section.
 * It shows the poster and basic information about the movie like title, rating, runtime and release date, etc
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';

use Movie_Library\Taxonomy\Hierarchical\Genre;
?>

<div class="movie-cover">
	<?php $src = get_thumbnail_attachment_url( get_the_ID() ); ?>

	<img class="cover-image" src="<?php echo esc_url( $src ); ?>" alt="<?php get_the_title(); ?>" />

	<?php

	// get the rating.
	$rating = get_post_rating( get_the_ID() );

	// get the runtime and format it.
	$runtime = get_post_runtime( get_the_ID(), 'H', 'M' );

	// get the release date and format it.
	$release_date = get_post_release_date( get_the_ID() );

	?>
	<div class="info">
		<h1 class="movie-cover-title" >
			<?php the_title(); ?>
		</h1>
		<div class="basic-meta-info">
			<span class="basic-meta-item rating">
				<img class="rating-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/star.svg" alt="Rating" /><?php echo esc_html( $rating ) . '/10'; ?>
			</span>
			<span class="basic-meta-item"><?php echo wp_kses( $release_date, array( 'span' => array( 'class' => array() ) ) ); ?></span>
			<span class="basic-meta-item"><?php echo esc_html( 'PG-13' ); ?></span>
			<span class="basic-meta-item"><?php echo esc_html( $runtime ); ?></span>
		</div>
		<div class="movie-cover-description">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
		<ul class="movie-genre">
			<?php
			// get the terms.
			$term_names = get_terms_list( get_the_ID(), Genre::SLUG );

			foreach ( $term_names as $term_name ) :
				echo '<li class="movie-genre-item">' . esc_html( $term_name ) . '</li>';
			endforeach;
			?>
		</ul>
		<div class="movie-director">
			<span class="movie-director-label"><?php esc_html_e( 'Director', 'movie-library' ); ?>: </span>
			<ul class="movie-director-list">
				<?php
				$directors = get_post_meta( get_the_ID(), 'rt-movie-meta-crew-director', true );

				if ( ! $directors ) {
					$directors = array();
				}

				foreach ( $directors as $director ) :
					?>
					<li class="movie-director-item"><?php echo esc_html( get_the_title( $director ) ); ?></li>
					<?php
				endforeach;
				?>
			</ul>
		</div>
		<button class="movie-watch-btn">
			<span class="circle-in-btn">
				<img class="play-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/watch.svg" alt="Play" />
			</span>
			<?php esc_html_e( 'Watch Now', 'movie-library' ); ?>
		</button>
	</div>
</div>




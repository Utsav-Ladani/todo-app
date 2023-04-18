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
use Movie_Library\APIs\Movie_Library_Metadata_API;
?>

<div class="movie-cover max-container">
	<img class="cover-image" src="<?php echo esc_url( get_thumbnail_attachment_url( get_the_ID() ) ); ?>" alt="<?php get_the_title(); ?>" />

	<?php
	// get video link.
	$videos = Movie_Library_Metadata_API::get_movie_meta( get_the_ID(), 'rt-media-meta-videos', true );

	if ( ! is_array( $videos ) ) {
		$videos = array();
	}

	$video     = array_shift( $videos );
	$video_src = wp_get_attachment_url( $video );

	?>
	<div class="info">
		<h1 class="movie-cover-title" ><?php the_title(); ?></h1>
		<div class="basic-meta-info">
			<span class="basic-meta-item rating">
				<img class="rating-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/star.svg" alt="Rating" /><?php echo esc_html( get_post_rating( get_the_ID() ) ) . '/10'; ?>
			</span>
			<span class="basic-meta-item"><?php echo wp_kses( get_post_release_date( get_the_ID() ), array( 'span' => array( 'class' => array() ) ) ); ?></span>
			<span class="basic-meta-item"><?php echo esc_html( 'PG-13' ); ?></span>
			<span class="basic-meta-item"><?php echo esc_html( get_post_runtime( get_the_ID(), 'H', 'M' ) ); ?></span>
		</div>
		<div class="movie-cover-description">
			<?php echo wp_kses_post( get_the_excerpt() ); ?>
		</div>
		<ul class="movie-genre">
			<?php
			// get the terms.
			$term_names = get_terms_list( get_the_ID(), Genre::SLUG );

			foreach ( $term_names as $term_name ) :
				?>
				<li class="movie-genre-item hover-btn">
					<a href="<?php echo esc_url( get_term_link( $term_name, Genre::SLUG ) ); ?>">
						<?php echo esc_html( $term_name ); ?>
					</a>
				</li>
				<?php
			endforeach;
			?>
		</ul>
		<div class="movie-director">
			<span class="movie-director-label"><?php esc_html_e( 'Director', 'movie-library' ); ?>: </span>
			<ul class="movie-director-list">
				<?php
				$directors = Movie_Library_Metadata_API::get_movie_meta( get_the_ID(), 'rt-movie-meta-crew-director', true );

				if ( ! $directors ) {
					$directors = array();
				}

				foreach ( $directors as $director ) :
					?>
					<li class="movie-director-item">
						<a href="<?php echo esc_url( get_permalink( $director ) ); ?>"><?php echo esc_html( get_the_title( $director ) ); ?></a>
					</li>
					<?php
				endforeach;
				?>
			</ul>
		</div>
		<button class="video-btn movie-watch-btn" video-src="<?php echo esc_url( $video_src ); ?>" >
			<span class="circle-in-btn">
				<img class="play-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/watch.svg" alt="Play" />
			</span>
			<?php esc_html_e( 'Watch Now', 'movie-library' ); ?>
		</button>
	</div>
</div>

<?php
/**
 * Add movies widget to the WordPress dashboard.
 * It fetches data from the WordPress database.
 * Movie Library plugin is required for the widget to work.
 * It lists most recent movies and top-rated movies from the Movie Library plugin.
 *
 * @package Movie Library
 */

namespace Movie_Library\Widget;

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Taxonomy\Hierarchical\Genre;

/**
 * Class Movies_From_Plugin_Dashboard_Widget
 * It adds the most recent movies and top-rated movies dashboard widget to the WordPress dashboard.
 * It uses WordPress database to fetch the most recent movies and top-rated movies.
 */
class Movies_From_Plugin_Dashboard_Widget {

	/**
	 * Widget SLUG.
	 *
	 * @var string
	 */
	const UPCOMING_MOVIE_SLUG = 'movies_from_plugin_dashboard_widget';

	/**
	 * Initialize Upcoming_Movies_Dashboard_Widget.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'add_movies_from_plugin_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'movies_from_plugin_enqueue_scripts_and_styles' ) );
	}

	/**
	 * Add the upcoming movies widget to the WordPress dashboard.
	 * It shows the list of most recent movies and top-rated movies.
	 *
	 * @return void
	 */
	public static function add_movies_from_plugin_dashboard_widget() : void {
		wp_add_dashboard_widget(
			self::UPCOMING_MOVIE_SLUG,
			__( 'Movies Recent and Top-Rated Movies', 'movie-library' ),
			array( __CLASS__, 'render_movies_from_plugin_dashboard_widget' ),
		);
	}

	/**
	 * Enqueue the scripts and styles for the most recent and top-rated movies widget.
	 *
	 * @return void
	 */
	public static function movies_from_plugin_enqueue_scripts_and_styles() : void {
		// enqueue the styles for dashboard widget.
		if ( is_admin() ) {
			wp_enqueue_style(
				'dashboard-widget-movie-styles',
				MOVIE_LIBRARY_PLUGIN_URL . '/assets/css/dashboard-style.css',
				array(),
				filemtime( MOVIE_LIBRARY_PLUGIN_DIR . '/assets/css/dashboard-style.css' )
			);
		}
	}

	/**
	 * Render the upcoming movies widget for the most recent and top-rated movies.
	 *
	 * @return void
	 */
	public static function render_movies_from_plugin_dashboard_widget() : void {
		?>
		<div class="movies-wrapper" >
			<h2 class="movies-wrapper__title-h2" >
				<?php esc_html_e( 'Most Recent Movies', 'movie-library' ); ?>
			</h2>
			<?php self::render_movie_card_in_dashboard_widget( self::get_most_recent_movies( 3 ) ); ?>
			<h2 class="movies-wrapper__title-h2" >
				<?php esc_html_e( 'Top Rated Movies', 'movie-library' ); ?>
			</h2>
			<?php self::render_movie_card_in_dashboard_widget( self::get_top_rated_movies( 3 ) ); ?>
		</div>
		<?php
	}

	/**
	 * Render the movie card in the dashboard widget forgiven movie list.
	 *
	 * @param \WP_Post[] $movies list of all movie posts.
	 *
	 * @return void
	 */
	public static function render_movie_card_in_dashboard_widget( array $movies ) : void {
		?>
		<ul class="movie-card-list">
			<?php foreach ( $movies as $movie ) : ?>
				<li class="movie-card-item">
					<?php
					// add placeholder image if image not found in API response.
					$src = wp_get_attachment_url( get_post_thumbnail_id( $movie->ID ) );
					if ( empty( $src ) ) {
						$src = MOVIE_LIBRARY_PLUGIN_URL . '/assets/images/placeholder.png';
					}
					?>
					<a href="<?php the_permalink( $movie ); ?>" class="movie-card__link" >
					<img class="movie-card__image" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $movie->post_title ); ?>">
						<div class="movie-card-item__info-wrapper">
							<h1 class="movie-card-item__info__title--h1">
								<?php echo esc_attr( $movie->post_title ); ?>
							</h1>
							<?php
							// get the release date and format it.
							$release_date = get_post_meta( $movie->ID, 'rt-movie-meta-basic-release-date', true );
							$release_date = gmdate( 'M d, Y', strtotime( $release_date ) );

							// get the genres and implode them.
							$genres = get_the_terms( $movie->ID, Genre::SLUG );
							$genres = implode( ', ', wp_list_pluck( $genres, 'name' ) );

							// get the actors from DB.
							$actors = get_post_meta( $movie->ID, 'rt-movie-meta-crew-actor', true );

							// ensure $actor is an array.
							if ( ! is_array( $actors ) ) {
								$actors = array();
							}

							// extract ID of actors.
							$actors = array_keys( $actors );

							// get the actor name from DB.
							$actors = array_map(
								function( $id ) {
									return get_the_title( $id );
								},
								$actors
							);

							// remove empty values.
							$actors = array_filter( $actors );

							// implode the actors.
							$actors = implode( ', ', $actors );

							?>
							<div class="movie-card-item__info movie-card-item__show-on-hover">
								<?php if ( $release_date ) : ?>
									<div class="movie-card-item__info__release-date">
										<?php
										/* translators: 1: Release date */
										printf( esc_html__( 'Release on: %s' ), esc_attr( $release_date ?? '' ) );
										?>
									</div>
								<?php endif; ?>
								<?php if ( $genres ) : ?>
									<div class="movie-card-item__info__genres">
										<?php
										/* translators: 1: Genres */
										printf( esc_html__( 'Genres: %s' ), esc_attr( $genres ?? '' ) );
										?>
									</div>
								<?php endif; ?>
								<?php if ( $actors ) : ?>
									<div class="movie-card-item__info__actors">
										<?php
										/* translators: 1: Actors */
										printf( esc_html__( 'Actors: %s' ), esc_attr( $actors ?? '' ) );
										?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Get the most recent movies from the WordPress database.
	 *
	 * @param int $movies_count number of movies to fetch.
	 *
	 * @return array
	 */
	public static function get_most_recent_movies( int $movies_count = 6 ) : array {
		// WP_Query arguments.
		$args = array(
			'post_type'      => Movie::SLUG,
			'post_status'    => 'publish',
			'posts_per_page' => absint( $movies_count ),
			// order by release date in meta key.
			'meta_key'       => 'rt-movie-meta-basic-release-date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
			// meta_query to get movie whose release date is less than today.
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'rt-movie-meta-basic-release-date',
					'value'   => gmdate( 'Y-m-d' ),
					'compare' => '<=',
					'type'    => 'DATE',
				),
			),
		);

		// fire the WP Query.
		$result = new \WP_Query( $args );

		// return posts in array.
		return $result->posts ?? array();
	}

	/**
	 * Get the top-rated movies from the WordPress database.
	 *
	 * @param int $movies_count number of movies to fetch.
	 *
	 * @return array
	 */
	public static function get_top_rated_movies( int $movies_count = 6 ) : array {
		// WP_Query arguments.
		$args = array(
			'post_type'      => Movie::SLUG,
			'post_status'    => 'publish',
			'posts_per_page' => absint( $movies_count ),
			// order by rating in meta key.
			'meta_key'       => 'rt-movie-meta-basic-rating', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
		);

		// fire the WP Query.
		$result = new \WP_Query( $args );

		// return posts in array.
		return $result->posts ?? array();
	}
}

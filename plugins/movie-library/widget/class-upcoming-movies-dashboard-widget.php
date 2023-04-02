<?php
/**
 * Add upcoming movies widget to the WordPress dashboard.
 * This widget shows the upcoming and top-rated movies from IMDB APIs.
 *
 * @package Movie Library
 */

namespace Movie_Library\Widget;

/**
 * Class Upcoming_Movies_Dashboard_Widget
 * It adds the upcoming movies dashboard widget to the WordPress dashboard.
 * It uses IMDB APIs to fetch the upcoming movies.
 */
class Upcoming_Movies_Dashboard_Widget {

	/**
	 * IMDB upcoming movie widget SLUG.
	 *
	 * @var string
	 */
	const UPCOMING_MOVIE_SLUG = 'upcoming_movies_dashboard_widget';

	/**
	 * API data expiration time for transient.
	 *
	 * @var int
	 */
	const EXPIRATION_TIME = 4 * HOUR_IN_SECONDS;

	/**
	 * Initialize Upcoming_Movies_Dashboard_Widget.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'add_upcoming_movies_dashboard_widget' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'upcoming_movie_enqueue_scripts_and_styles' ) );
	}

	/**
	 * Add the upcoming movies widget to the WordPress dashboard.
	 * It shows the list of upcoming movies.
	 *
	 * @return void
	 */
	public static function add_upcoming_movies_dashboard_widget() : void {
		wp_add_dashboard_widget(
			self::UPCOMING_MOVIE_SLUG,
			__( 'IMDB Upcoming Movies', 'movie-library' ),
			array( __CLASS__, 'render_upcoming_movies_dashboard_widget' ),
		);
	}

	/**
	 * Enqueue the scripts and styles for the upcoming movie widget.
	 *
	 * @return void
	 */
	public static function upcoming_movie_enqueue_scripts_and_styles() : void {
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
	 * Render the upcoming movies widget for upcoming movies.
	 *
	 * @return void
	 */
	public static function render_upcoming_movies_dashboard_widget() : void {
		// get imdb upcoming movies list.
		$upcoming_movies = self::get_imdb_upcoming_movies();

		?>
		<div class="upcoming-movies">
			<ul class="movie-card-list">
				<?php foreach ( $upcoming_movies as $upcoming_movie ) : ?>
					<li class="movie-card-item">
						<?php
						// add placeholder image if image not found in API response.
						$src = $upcoming_movie['image'] ?? '';
						if ( empty( $src ) ) {
							$src = MOVIE_LIBRARY_PLUGIN_URL . '/assets/images/placeholder.png';
						}
						?>
						<img class="movie-card__image" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $upcoming_movie['title'] ?? '' ); ?>">
						<div class="movie-card-item__info-wrapper">
							<h1 class="movie-card-item__info__title--h1">
								<?php echo esc_attr( $upcoming_movie['title'] ?? '' ); ?>
							</h1>
							<div class="movie-card-item__info movie-card-item__show-on-hover">
								<div class="movie-card-item__info__release-date">
									<?php
									/* translators: %s: release date */
									printf( esc_html__( 'Release on: %s' ), esc_attr( $upcoming_movie['releaseState'] ?? '' ) );
									?>
								</div>
								<div class="movie-card-item__info__genres">
									<?php
									/* translators: %s: genres */
									printf( esc_html__( 'Genres: %s' ), esc_attr( $upcoming_movie['genres'] ?? '' ) );
									?>
								</div>
								<div class="movie-card-item__info__actors">
									<?php
									/* translators: %s: actors */
									printf( esc_html__( 'Actors: %s' ), esc_attr( $upcoming_movie['stars'] ?? '' ) );
									?>
								</div>
							</div>

						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Fetch the IMDB upcoming movies.
	 *
	 * @param int $max_items Maximum number of items to fetch. Default is 6.
	 * @return array
	 */
	public static function get_imdb_upcoming_movies( int $max_items = 6 ) : array {
		// add API key to the URL and sanitize it.
		$url = MOVIE_LIBRARY_IMDB_API_URL . MOVIE_LIBRARY_IMDB_API_KEY;
		$url = esc_url_raw( $url );

		// set the arguments for the wp_remote_request.
		$args = array(
			'method'      => 'GET',
			'timeout'     => 5,
			'redirection' => 1,
		);

		// key to store the result in transient.
		$key = 'imdb_upcoming_movies';

		// set the expiration time for the transient.
		$expiration = self::EXPIRATION_TIME;

		$result = self::enhanced_wp_remote_request( $url, $args, $key, $expiration );

		// slice the result to get the maximum number of items.
		return array_slice( $result, 0, $max_items );
	}

	/**
	 * Enhanced wp_remote_request function.
	 * It will first check if the result is already stored in transient.
	 * It also stored the result in transient if the transient key is given.
	 *
	 * @param string $url URL to fetch.
	 * @param array  $args Arguments for wp_remote_request.
	 * @param string $key Key to store the result in transient.
	 * @param int    $expiration Expiration time for the transient.
	 * @return array
	 */
	public static function enhanced_wp_remote_request( string $url, array $args = array(), string $key = '', int $expiration = 0 ) : array {
		$result = false;

		// If key is given, check if the result is already stored in transient.
		if ( ! empty( $key ) ) {
			$result = get_transient( $key );
		}

		// If result is not found in transient, fetch the result from the URL.
		if ( ! $result ) {
			$result = wp_remote_request( $url, $args );

			// if key is given, store the result in transient.
			if ( ! empty( $key ) ) {
				set_transient( $key, $result, $expiration );
			}
		}

		// fetch the body from the result and decode it.
		$body        = wp_remote_retrieve_body( $result );
		$json_result = json_decode( $body, true );

		// return the items from the result.
		return $json_result['items'] ?? array();
	}
}

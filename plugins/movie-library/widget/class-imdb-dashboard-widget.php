<?php
/**
 * Add IMDB widget to the WordPress dashboard.
 * This widget shows the upcoming and top-rated movies from IMDB APIs.
 *
 * @package Movie Library
 */

namespace Movie_Library\Widget;

/**
 * Class IMDB_Dashboard_Widget
 * It adds the IMDB widget to the WordPress dashboard.
 * It uses IMDB APIs to fetch the upcoming and top-rated movies.
 */
class IMDB_Dashboard_Widget {

	/**
	 * IMDB upcoming movie widget SLUG.
	 *
	 * @var string
	 */
	const UPCOMING_MOVIE_SLUG = 'imdb_upcoming_dashboard_widget';

	/**
	 * Initialize IMDB_Dashboard_Widget.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'add_imdb_upcoming_dashboard_widget' ) );
	}

	/**
	 * Add the IMDB widget to the WordPress dashboard.
	 * It shows the list of upcoming movies.
	 *
	 * @return void
	 */
	public static function add_imdb_upcoming_dashboard_widget() : void {
		wp_add_dashboard_widget(
			self::UPCOMING_MOVIE_SLUG,
			__( 'IMDB Upcoming Movies', 'movie-library' ),
			array( __CLASS__, 'render_imdb_upcoming_dashboard_widget' ),
		);
	}

	/**
	 * Render the IMDB widget for upcoming movies.
	 *
	 * @return void
	 */
	public static function render_imdb_upcoming_dashboard_widget() : void {
		// get imdb upcoming movies list.
		$upcoming_movies = self::get_imdb_upcoming_movies();

		print_r($upcoming_movies);
	}

	/**
	 * Fetch the IMDB upcoming movies.
	 *
	 * @return array
	 */
	public static function get_imdb_upcoming_movies() : array {

		return enhanced_wp_remote_request( $url, $args,  );
	}

	public static function enhanced_wp_remote_request( string $url, array $args = array(), string $key = '', int $expiration = 0 ) : array {
		$result = false;

		if( ! empty( $key ) ) {
			$result = get_transient( $key );
		}

		if( $result ) return $result;

		$result = wp_remote_request( $url, $args );
		set_transient( $key, $result, $expiration );

		$result = $result['items'] ?? array();

		$result = array_slice( $result, 8 );

		return $result;
	}
}

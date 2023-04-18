<?php
/**
 * Movie Library Metadata API
 * It defines the custom metadata API for movie library plugin.
 *
 * @package Movie Library
 */

namespace Movie_Library\APIs;

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

/**
 * Class Movie_Library_Metadata_API
 * It defines the custom metadata API for movie library plugin.
 * It contains the methods to add, update, delete and get the metadata.
 */
class Movie_Library_Metadata_API {

	/**
	 * Initialize the metadata API.
	 * It adds the necessary callbacks to hooks to set up the metadata API.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'plugins_loaded', array( __CLASS__, 'setup_metadata_api' ) );

		// disable the suppression filter.
		add_action( 'pre_get_posts', array( __CLASS__, 'add_suppress_filter' ) );

		// add the filter to change the SQL query and update it for new table.
		add_filter( 'posts_request', array( __CLASS__, 'change_meta_query' ) );
	}

	/**
	 * Set up the metadata API.
	 * It sets up the metadata API by adding the necessary global variables.
	 *
	 * @return void
	 */
	public static function setup_metadata_api() : void {
		global $wpdb;

		// Set up the metadata table names.
		$wpdb->moviemeta  = $wpdb->prefix . 'moviemeta';
		$wpdb->personmeta = $wpdb->prefix . 'personmeta';
	}

	/**
	 * Add movie meta.
	 * It adds the metadata for the movie.
	 *
	 * @param int    $movie_id The movie ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 * @param bool   $unique Whether the meta key should be unique or not.
	 *
	 * @return mixed
	 */
	public static function add_movie_meta( int $movie_id, string $meta_key, $meta_value, bool $unique = false ) {
		// If the movie ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		return add_metadata( 'movie', $movie_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Get movie meta.
	 * It gets the metadata for the movie.
	 *
	 * @param int    $movie_id The movie ID.
	 * @param string $meta_key The meta key.
	 * @param bool   $single Whether to return a single value or an array.
	 *
	 * @return mixed
	 */
	public static function get_movie_meta( int $movie_id, string $meta_key = '', bool $single = false ) {
		// store the requested result.
		$requested_result = get_post_meta( $movie_id, $meta_key, $single );

		// fetch all data on that key.
		$old_db_result = get_post_meta( $movie_id, $meta_key );

		// Add all the values to the new table based on occurrence of key in database.
		if ( is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_movie_meta( $movie_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_movie_meta( $movie_id, $meta_key, $old_db_result, true );
		}

		// delete the old data.
		delete_post_meta( $movie_id, $meta_key );

		// return the requested result.
		if ( ! empty( $requested_result ) ) {
			return $requested_result;
		}

		// find the data in the new table.
		return get_metadata( 'movie', $movie_id, $meta_key, $single );
	}

	/**
	 * Delete movie meta.
	 * It deletes the metadata for the movie.
	 *
	 * @param int    $movie_id The movie ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 *
	 * @return bool
	 */
	public static function delete_movie_meta( int $movie_id, string $meta_key, $meta_value = '' ) : bool {
		// If the movie ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		// delete from both database.
		$result  = delete_post_meta( $movie_id, $meta_key, $meta_value );
		$result &= delete_metadata( 'movie', $movie_id, $meta_key, $meta_value );

		return $result;
	}

	/**
	 * Update movie meta.
	 * It updates the metadata for the movie.
	 *
	 * @param int    $movie_id The movie ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 * @param mixed  $prev_value The previous meta value.
	 *
	 * @return mixed
	 */
	public static function update_movie_meta( int $movie_id, string $meta_key, $meta_value, $prev_value = '' ) {
		// If the movie ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		// fetch the all data on that key.
		$old_db_result = get_post_meta( $movie_id, $meta_key );

		// Add all the values to the new table based on occurrence of key in database.
		if ( ! is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_movie_meta( $movie_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_movie_meta( $movie_id, $meta_key, $old_db_result, true );
		}

		// delete the old data.
		delete_post_meta( $movie_id, $meta_key );

		// update the data into new table.
		return update_metadata( 'movie', $movie_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Add person meta.
	 * It adds the metadata for the person.
	 *
	 * @param int    $person_id The person ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 * @param bool   $unique Whether the meta key should be unique for the object.
	 *
	 * @return mixed
	 */
	public static function add_person_meta( int $person_id, string $meta_key, $meta_value, bool $unique = false ) {
		// If the person ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		return add_metadata( 'person', $person_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Get person meta.
	 * It gets the metadata for the person.
	 *
	 * @param int    $person_id The person ID.
	 * @param string $meta_key The meta key.
	 * @param bool   $single Whether to return a single value.
	 *
	 * @return mixed
	 */
	public static function get_person_meta( int $person_id, string $meta_key = '', bool $single = false ) {
		// stored the requested result.
		$requested_result = get_post_meta( $person_id, $meta_key, $single );

		// fetch the all data on that key.
		$old_db_result = get_post_meta( $person_id, $meta_key );

		// Add all the values to the new table based on occurrence of key in database.
		if ( is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_person_meta( $person_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_person_meta( $person_id, $meta_key, $old_db_result, true );
		}

		// delete the old data.
		delete_post_meta( $person_id, $meta_key );

		// return the requested result.
		if ( ! empty( $requested_result ) ) {
			return $requested_result;
		}

		// return the new data.
		return get_metadata( 'person', $person_id, $meta_key, $single );
	}

	/**
	 * Delete person meta.
	 * It deletes the metadata for the person.
	 *
	 * @param int    $person_id The person ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 *
	 * @return bool
	 */
	public static function delete_person_meta( int $person_id, string $meta_key, $meta_value = '' ) : bool {
		// If the person ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		// delete from both table.
		$result  = delete_post_meta( $person_id, $meta_key, $meta_value );
		$result &= delete_metadata( 'person', $person_id, $meta_key, $meta_value );

		return $result;
	}

	/**
	 * Update person meta.
	 * It updates the metadata for the person.
	 *
	 * @param int    $person_id The person ID.
	 * @param string $meta_key The meta key.
	 * @param mixed  $meta_value The meta value.
	 * @param mixed  $prev_value The previous meta value.
	 *
	 * @return mixed
	 */
	public static function update_person_meta( int $person_id, string $meta_key, $meta_value, $prev_value = '' ) {
		// If the person ID is a revision ID, then get the parent post ID.
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		// fetch the all data on that key.
		$old_db_result = get_post_meta( $person_id, $meta_key );

		// Add all the values to the new table based on occurrence of key in database.
		if ( ! is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_person_meta( $person_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_person_meta( $person_id, $meta_key, $old_db_result, true );
		}

		// delete the old data.
		delete_post_meta( $person_id, $meta_key );

		// update the data into new table.
		return update_metadata( 'person', $person_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Disable the suppression filter.
	 * It used to manipulate the every WP Query.
	 *
	 * @param object $args The query args.
	 * @see \WP_Query
	 *
	 * @return void
	 */
	public static function add_suppress_filter( $args ) {
		// custom post type slug list.
		$post_type = array( Movie::SLUG, Person::SLUG );

		// check whether the query is for the custom post type.
		if ( in_array( $args->query_vars['post_type'], $post_type, true ) ) {
			// disable the suppression filter.
			$args->query_vars['suppress_filters'] = false;
		}
	}

	/**
	 * Change the meta query.
	 * It used to change the SQL query for the custom post type.
	 * Replace the postmeta table with the custom table.
	 *
	 * @param string $sql The SQL query.
	 *
	 * @return string
	 */
	public function change_meta_query( string $sql ): string {
		// Check if the query is for the rt-movie post type.
		if ( str_contains( $sql, 'wp_posts.post_type = \'rt-movie\'' ) ) {
			$sql = str_replace( 'wp_postmeta', 'wp_moviemeta', $sql );
			$sql = str_replace( 'post_id', 'movie_id', $sql );

			// Check if the query is for the rt-person post type.
		} elseif ( str_contains( $sql, 'wp_posts.post_type = \'rt-person\'' ) ) {
			$sql = str_replace( 'wp_postmeta', 'wp_personmeta', $sql );
			$sql = str_replace( 'post_id', 'person_id', $sql );
		}

		return $sql;
	}
}

<?php
/**
 * Movie Library Metadata API
 * It defines the custom metadata API for movie library plugin.
 *
 * @package Movie Library
 */

namespace Movie_Library\APIs;

/**
 * Class Movie_Library_Metadata_API
 * It defines the custom metadata API for movie library plugin.
 * It contains the methods to add, update, delete and get the metadata.
 */
class Movie_Library_Metadata_API {

	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'setup_metadata_api' ) );
	}

	public static function setup_metadata_api() : void {
		global $wpdb;

		$wpdb->moviemeta  = $wpdb->prefix . 'moviemeta';
		$wpdb->personmeta = $wpdb->prefix . 'personmeta';
	}

	public static function add_movie_meta( int $movie_id, string $meta_key, $meta_value, bool $unique = false ) : int|false {
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		return add_metadata( 'movie', $movie_id, $meta_key, $meta_value, $unique );
	}

	public static function get_movie_meta( int $movie_id, string $meta_key = '', bool $single = false ) {
		$requested_result = get_post_meta( $movie_id, $meta_key, $single );

		$old_db_result = get_post_meta( $movie_id, $meta_key );

		if ( is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_movie_meta( $movie_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_movie_meta( $movie_id, $meta_key, $old_db_result, true );
		}

		delete_post_meta( $movie_id, $meta_key );

		if ( ! empty( $requested_result ) ) {
			return $requested_result;
		}

		return get_metadata( 'movie', $movie_id, $meta_key, $single );
	}

	public static function delete_movie_meta( int $movie_id, string $meta_key, $meta_value = '' ) : bool {
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		$result  = delete_post_meta( $movie_id, $meta_key, $meta_value );
		$result &= delete_metadata( 'movie', $movie_id, $meta_key, $meta_value );

		return $result;
	}

	public static function update_movie_meta( int $movie_id, string $meta_key, $meta_value, $prev_value = '' ) : int|false {
		$the_post = wp_is_post_revision( $movie_id );

		if ( $the_post ) {
			$movie_id = $the_post;
		}

		$old_db_result = get_post_meta( $movie_id, $meta_key );

		if ( ! is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_movie_meta( $movie_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_movie_meta( $movie_id, $meta_key, $old_db_result, true );
		}

		delete_post_meta( $movie_id, $meta_key );

		return update_metadata( 'movie', $movie_id, $meta_key, $meta_value, $prev_value );
	}

	public static function add_person_meta( int $person_id, string $meta_key, $meta_value, bool $unique = false ) : int|false {
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		return add_metadata( 'person', $person_id, $meta_key, $meta_value, $unique );
	}

	public static function get_person_meta( int $person_id, string $meta_key = '', bool $single = false ) {
		$requested_result = get_post_meta( $person_id, $meta_key, $single );

		$old_db_result = get_post_meta( $person_id, $meta_key );

		if ( is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_person_meta( $person_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_person_meta( $person_id, $meta_key, $old_db_result, true );
		}

		delete_post_meta( $person_id, $meta_key );

		if ( ! empty( $requested_result ) ) {
			return $requested_result;
		}

		return get_metadata( 'person', $person_id, $meta_key, $single );
	}

	public static function delete_person_meta( int $person_id, string $meta_key, $meta_value = '' ) : bool {
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		$result  = delete_post_meta( $person_id, $meta_key, $meta_value );
		$result &= delete_metadata( 'person', $person_id, $meta_key, $meta_value );

		return $result;
	}

	public static function update_person_meta( int $person_id, string $meta_key, $meta_value, $prev_value = '' ) : int|false {
		$the_post = wp_is_post_revision( $person_id );

		if ( $the_post ) {
			$person_id = $the_post;
		}

		$old_db_result = get_post_meta( $person_id, $meta_key );

		if ( ! is_array( $old_db_result ) ) {
			foreach ( $old_db_result as $old_db_meta_value ) {
				self::add_person_meta( $person_id, $meta_key, $old_db_meta_value );
			}
		} elseif ( ! empty( $old_db_result ) ) {
			self::add_person_meta( $person_id, $meta_key, $old_db_result, true );
		}

		delete_post_meta( $person_id, $meta_key );

		return update_metadata( 'person', $person_id, $meta_key, $meta_value, $prev_value );
	}
}

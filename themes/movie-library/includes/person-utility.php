<?php
/**
 * Movie Library Person Utility.
 * It contains the utility functions to retrieve the data for a particular person.
 *
 * @package Movie Library
 */

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;
use Movie_Library\Shadow_Taxonomy\Non_Hierarchical\Shadow_Person;
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;

// check if the function exists.
if ( ! function_exists( 'get_cast_crew' ) ) {
	/**
	 * Get the cast and crew.
	 *
	 * @param int $id post id.
	 * @param int $limit limit.
	 *
	 * @return array
	 */
	function get_cast_crew( int $id, int $limit = 4 ): array {
		// get all meta data.
		$directors = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( $id, 'rt-movie-meta-crew-director', true );
		$writers   = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( $id, 'rt-movie-meta-crew-writer', true );
		$producers = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( $id, 'rt-movie-meta-crew-producer', true );
		$actors    = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( $id, 'rt-movie-meta-crew-actor', true );

		// validate the data.
		if ( ! is_array( $actors ) ) {
			$actors = array();
		}
		$actors = array_keys( $actors );

		if ( ! is_array( $directors ) ) {
			$directors = array();
		}

		if ( ! is_array( $writers ) ) {
			$writers = array();
		}

		if ( ! is_array( $producers ) ) {
			$producers = array();
		}

		// remove the duplicate values.
		$persons = array_merge( $actors, $directors, $writers, $producers );
		$persons = array_unique( $persons );

		// limit the data.
		return array_slice( $persons, 0, $limit );
	}
}

// check if the function exists.
if ( ! function_exists( 'get_post_birth_date' ) ) {
	/**
	 * Get the post birthdate.
	 *
	 * @param int    $id post id.
	 * @param string $format date format.
	 * @param string $post_type post type.
	 *
	 * @return string
	 */
	function get_post_birth_date( int $id, string $format = 'Y', string $post_type = Person::SLUG ) : string {
		$meta_keys = array(
			Person::SLUG => 'rt-person-meta-basic-birth-date',
		);

		if ( ! isset( $meta_keys[ $post_type ] ) ) {
			return '';
		}

		// get the meta value and format it.
		$birth_date = \Movie_Library\APIs\Movie_Library_Metadata_API::get_person_meta( $id, $meta_keys[ $post_type ], true );
		$birth_date = $birth_date ?? '';
		$birth_date = gmdate( $format, strtotime( $birth_date ) );
		return preg_replace( '/(\d+)(th|st|nd|rd)/', '$1<span class="date-th">$2</span>', $birth_date );
	}
}

// check if the function exists.
if ( ! function_exists( 'get_first_movie' ) ) {
	/**
	 * Get the first movie.
	 *
	 * @param int $id post id.
	 *
	 * @return WP_Post|null
	 */
	function get_first_movie( int $id ) {
		$args = array(
			'post_type'      => Movie::SLUG,
			'posts_per_page' => 1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'       => 'rt-movie-meta-basic-release-date',
			'meta_type'      => 'DATE',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => Shadow_Person::SLUG,
					'field'    => 'slug',
					'terms'    => sprintf( 'person-%d', $id ),
				),
			),
		);

		return get_posts( $args )[0] ?? null;
	}
}

// check if the function exists.
if ( ! function_exists( 'get_last_movie' ) ) {
	/**
	 * Get the last movie.
	 *
	 * @param int $id post id.
	 *
	 * @return WP_Post|null
	 */
	function get_last_movie( int $id ) {
		$args = array(
			'post_type'      => Movie::SLUG,
			'posts_per_page' => 1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'       => 'rt-movie-meta-basic-release-date',
			'meta_type'      => 'DATE',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				array(
					'taxonomy' => Shadow_Person::SLUG,
					'field'    => 'slug',
					'terms'    => sprintf( 'person-%d', $id ),
				),
			),
		);

		return get_posts( $args )[0] ?? null;
	}
}

// check if the function exists.
if ( ! function_exists( 'get_upcoming_movies_of_person' ) ) {
	/**
	 * Get the upcoming movies of person.
	 *
	 * @param int $id post id.
	 *
	 * @return array|null
	 */
	function get_upcoming_movies_of_person( int $id ) {
		$args = array(
			'post_type'      => Movie::SLUG,
			'posts_per_page' => 1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => Shadow_Person::SLUG,
					'field'    => 'slug',
					'terms'    => sprintf( 'person-%d', $id ),
				),
				array(
					'taxonomy' => Tag::SLUG,
					'field'    => 'slug',
					'terms'    => 'upcoming',
				),
			),
		);

		return get_posts( $args ) ?? null;
	}
}

// check if the function exists.
if ( ! function_exists( 'get_archive_cast_crew' ) ) {
	/**
	 * Get the archive cast crew.
	 *
	 * @param mixed $movie_id movie id.
	 * @param int   $limit limit.
	 *
	 * @return array
	 */
	function get_archive_cast_crew( $movie_id, int $limit = 12 ) : array {
		// if movie id is not empty.
		if ( $movie_id ) {
			$persons = \Movie_Library\APIs\Movie_Library_Metadata_API::get_movie_meta( $movie_id, 'rt-movie-meta-crew-actor', true );

			if ( ! is_array( $persons ) ) {
				$persons = array();
			}

			return array_slice( $persons, 0, $limit, true );
		}

		// else get the latest persons.
		$posts = get_posts(
			array(
				'post_type'      => Person::SLUG,
				'posts_per_page' => $limit,
				'fields'         => 'ids',
			)
		);

		// assign empty string to character name.
		$persons = array();
		foreach ( $posts as $post ) {
			$persons[ $post ] = '';
		}

		return $persons;
	}
}

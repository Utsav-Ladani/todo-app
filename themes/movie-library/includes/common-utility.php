<?php
/**
 * Movie Library Common Utility.
 * It contains the common utility functions to retrieve the data and format it.
 *
 * @package Movie Library
 */

// check if the function exists.
if ( ! function_exists( 'get_thumbnail_attachment_url' ) ) {
	/**
	 * Get the thumbnail attachment url.
	 *
	 * @param int $attachment_id attachment id.
	 *
	 * @return string
	 */
	function get_thumbnail_attachment_url( $attachment_id ) {
		$thumbnail_id = get_post_thumbnail_id( $attachment_id );

		if ( $thumbnail_id ) {
			return wp_get_attachment_url( $thumbnail_id );
		}

		return '';
	}
}

// check if the function exists.
if ( ! function_exists( 'get_post_rating' ) ) {
	/**
	 * Get the post rating.
	 *
	 * @param int    $id post id.
	 * @param string $post_type post type.
	 *
	 * @return string
	 */
	function get_post_rating( int $id, string $post_type = 'rt-movie' ) : string {
		$meta_keys = array(
			'rt-movie'  => 'rt-movie-meta-basic-rating',
			'rt-person' => 'rt-person-meta-basic-rating',
		);

		if ( ! array_key_exists( $post_type, $meta_keys ) ) {
			return '';
		}

		// get the meta value and format it.
		$rating = get_post_meta( $id, $meta_keys[ $post_type ], true );
		$rating = $rating ?? 0;
		$rating = (float) $rating;
		return number_format_i18n( $rating, 1 );
	}
}

// check if the function exists.
if ( ! function_exists( 'get_post_runtime' ) ) {
	/**
	 * Get the post runtime.
	 *
	 * @param int    $id post id.
	 * @param string $hour_tag hour tag.
	 * @param string $minute_tag minute tag.
	 * @param string $post_type post type.
	 *
	 * @return string
	 */
	function get_post_runtime( int $id, string $hour_tag = ' hr', string $minute_tag = ' min', string $post_type = 'rt-movie' ) : string {
		$meta_keys = array(
			'rt-movie'  => 'rt-movie-meta-basic-runtime',
			'rt-person' => 'rt-person-meta-basic-runtime',
		);

		if ( ! array_key_exists( $post_type, $meta_keys ) ) {
			return '';
		}

		// get the meta value and format it.
		$runtime = get_post_meta( $id, $meta_keys[ $post_type ], true );
		$runtime = $runtime ?? 0;
		$runtime = (int) $runtime;
		$runtime = number_format_i18n( $runtime, 1 );

		// convert the runtime to hours and minutes.
		$time = '';

		// add the hours.
		if ( $runtime >= 60 ) {
			$time .= (int) floor( $runtime / 60 ) . $hour_tag . ' ';
		}

		// get the minutes.
		$minute = $runtime % 60;

		// add the minutes.
		if ( $minute > 0 ) {
			$time .= $minute . $minute_tag . ' ';
		}

		return $time;
	}
}

// check if the function exists.
if ( ! function_exists( 'get_post_release_date' ) ) {
	/**
	 * Get the post release date.
	 *
	 * @param int    $id post id.
	 * @param string $format date format.
	 * @param string $post_type post type.
	 *
	 * @return string
	 */
	function get_post_release_date( int $id, string $format = 'Y', string $post_type = 'rt-movie' ) : string {
		$meta_keys = array(
			'rt-movie' => 'rt-movie-meta-basic-release-date',
		);

		if ( ! array_key_exists( $post_type, $meta_keys ) ) {
			return '';
		}

		// get the meta value and format it.
		$release_date = get_post_meta( $id, $meta_keys[ $post_type ], true );
		$release_date = $release_date ?? '';
		$release_date = gmdate( $format, strtotime( $release_date ) );

		// add the span tag around of the th, st, nd, rd.
		return preg_replace( '/(\d+)(th|st|nd|rd)/', '$1<span class="date-th">$2</span>', $release_date );
	}
}

// check if the function exists.
if ( ! function_exists( 'get_terms_list' ) ) {
	/**
	 * Get the terms list.
	 *
	 * @param int    $id post id.
	 * @param string $taxonomy taxonomy name.
	 *
	 * @return array
	 */
	function get_terms_list( int $id, string $taxonomy ) : array {
		$terms = get_the_terms( $id, $taxonomy );
		return wp_list_pluck( $terms, 'name' );
	}
}

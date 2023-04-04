<?php
/**
 * Movie Library Update
 * It update the database and other plugin related data of older version plugin to make plugin compatible with new version.
 *
 * @package Movie Library
 * @since 1.0.0
 */

namespace Movie_Library;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;

/**
 * Class Movie_Library_Update
 * It responsible for updating the database and other plugin related data of older version plugin to make plugin compatible with new version.
 */
class Movie_Library_Update {
	public static function update() : void {
		delete_option( 'movie_library_version' );
		$version = get_option( 'movie_library_version' );

		if ( version_compare( $version, MOVIE_LIBRARY_VERSION, '<' ) ) {
			$result = self::update_1_1_0();

			if ( $result ) {
				echo 'Database updated successfully.';
				update_option( 'movie_library_version', MOVIE_LIBRARY_VERSION );
			}
		}
	}

	public static function update_1_1_0() : bool {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$result  = self::generate_sql_for_post_type( 'movie' );
		$result &= self::generate_sql_for_post_type( 'person' );

		return $result;
	}

	public static function generate_sql_for_post_type( string $post_type ) : string {
		global $wpdb;

		str_replace( '-', '_', $post_type );

		$post_type = esc_sql( $post_type );
		if ( empty( $post_type ) ) {
			return '';
		}

		$table_name = $wpdb->prefix . $post_type . 'meta';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			`{$post_type}_id` bigint(20) NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext NOT NULL,
			PRIMARY KEY  (meta_id),
			KEY `{$post_type}_id` (`{$post_type}_id`),
			KEY meta_key (meta_key)
		) $charset_collate;";

		dbDelta( $sql );

		return empty( $wpdb->last_error );
	}
}

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

/**
 * Class Movie_Library_Update
 * It responsible for updating the database and other plugin related data of older version plugin to make plugin compatible with new version.
 */
class Movie_Library_Update {
	/**
	 * Initialize update functionality.
	 * It adds action to upgrade_process_complete hook to upgrade the database.
	 *
	 * @return void
	 */
	public static function init() : void {
		add_action( 'upgrader_process_complete', array( __CLASS__, 'update' ) );
	}

	/**
	 * Update the plugin.
	 * It checks the current version of plugin and update the database and other if required.
	 *
	 * @return void
	 */
	public static function update() : void {
		// get version of plugin form DB.
		$version = get_option( 'movie_library_version' );

		// if version is newer, then run update process.
		if ( version_compare( $version, MOVIE_LIBRARY_VERSION, '<' ) ) {
			$result = self::update_1_1_0();

			// if update is successful, then update the version in DB.
			if ( $result ) {
				update_option( 'movie_library_version', MOVIE_LIBRARY_VERSION );
			}
		}
	}

	/**
	 * Update the database to version 1.1.0.
	 * It creates the new tables for movie and person metadata.
	 *
	 * @return bool True if update is successful, false otherwise.
	 */
	public static function update_1_1_0() : bool {
		global $wpdb;

		// require to run dbDelta function.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// create metadata tables.
		$result  = self::generate_sql_for_post_type( 'movie' );
		$result &= self::generate_sql_for_post_type( 'person' );

		return $result;
	}

	/**
	 * Generate SQL for post type.
	 * It generates the SQL for creating the metadata table for the given post type.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return bool True if SQL query run successfully, false otherwise.
	 */
	public static function generate_sql_for_post_type( string $post_type ) : bool {
		global $wpdb;

		// - is not allowed into the table name, so convert it into the _.
		str_replace( '-', '_', $post_type );

		// escape and validate.
		$post_type = esc_sql( $post_type );
		if ( empty( $post_type ) ) {
			return '';
		}

		// generate SQL.
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

		// run SQL Query.
		dbDelta( $sql );

		// return true if no error occurred.
		return empty( $wpdb->last_error );
	}
}

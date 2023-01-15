<?php
/**
 * Plugin Name: Plugin Update Notification
 * Author: rtCamp, Chandra Patel
 */

/**
 * Display plugin update notification when DISALLOW_FILE_MODS constat set to true.
 */
function rt_wp_plugin_update_rows() {

	if ( ! defined( 'DISALLOW_FILE_MODS' ) || false === DISALLOW_FILE_MODS ) {
		return;
	}

	$plugins = get_site_transient( 'update_plugins' );

	if ( isset( $plugins->response ) && is_array( $plugins->response ) ) {

		$plugins = array_keys( $plugins->response );

		foreach ( $plugins as $plugin_file ) {
			add_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
		}
	}

}
add_action( 'load-plugins.php', 'rt_wp_plugin_update_rows', 30 );

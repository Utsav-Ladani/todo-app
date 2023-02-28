<?php
/**
 * Movie Library uninstall
 * Uninstalling Movie Library deletes user roles, pages, tables, and options according to the settings.
 *
 * @package MovieLibrary
 * @author  rtCamp
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

die( 'Uninstalling plugin...' );

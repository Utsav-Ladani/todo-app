<?php
/**
 * Movie Library Setting
 * Add options for movie library plugin into the Settings menu.
 *
 * @package Movie_Library\Setting
 */

namespace Movie_Library\Setting;

/**
 * Class Setting
 * Add options for movie library plugin into the Settings menu.
 * Option is to delete the data on plugin delete.
 */
class Setting {

	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @hooked admin_menu
	 * @hooked admin_init
	 */
	public static function init() : void {
		// Add the option page to the Settings menu.
		add_action( 'admin_menu', array( __CLASS__, 'init_plugin_options' ) );

		// Register the settings.
		add_action( 'admin_init', array( __CLASS__, 'init_plugin_settings' ) );
	}

	/**
	 * Sanitize the settings.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init_plugin_options() : void {
		// Add the options page to the Settings menu.
		add_options_page(
			__( 'Movie Library', 'movie-library' ),
			__( 'Movie Library', 'movie-library' ),
			'manage_options',
			'movie-library-settings',
			array( __CLASS__, 'render_plugin_settings_page' ),
		);
	}

	/**
	 * Register the settings for the plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init_plugin_settings() : void {
		// Register the settings for deleting data on plugin delete.
		register_setting(
			'movie-library-settings',
			'movie-library-setting-delete-data-on-delete',
			array( __CLASS__, 'sanitize_plugin_settings' ),
		);
	}

	/**
	 * Render the movie library settings page.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_plugin_settings_page() : void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Movie Library Settings', 'movie-library' ); ?></h1>
			<form method="POST" action="options.php">
				<?php
				settings_fields( 'movie-library-settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Delete all content on plugin delete', 'movie-library' ); ?>
						</th>
						<td>
							<input
								type="checkbox"
								name="movie-library-setting-delete-data-on-plugin-delete"
								value="1"
								<?php checked( get_option( 'movie-library-setting-delete-data-on-plugin-delete' ), 1 ); ?>
							/>
							<label for="movie-library-setting-delete-data-on-plugin-delete">
								<?php esc_html_e( 'Delete all data when the plugin is deleted.', 'movie-library' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

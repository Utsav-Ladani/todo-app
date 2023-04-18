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
abstract class Setting {
	const SETTINGS_CAPABILITY = 'manage_options';

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
		add_options_page(
			__( 'Movie Library', 'movie-library' ),
			__( 'Movie Library', 'movie-library' ),
			self::SETTINGS_CAPABILITY,
			'movie-library',
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
		register_setting( 'movie-library', 'rt-delete-data-on-delete-plugin' );

		add_settings_section(
			'movie-library-section',
			__( 'Plugin Settings', 'movie-library' ),
			array( __CLASS__, 'render_setting_section' ),
			'movie-library',
		);

		add_settings_field(
			'rt-delete-data-on-delete-plugin',
			__( 'Delete all content on plugin delete', 'movie-library' ),
			array( __CLASS__, 'render_delete_data_on_delete_field' ),
			'movie-library',
			'movie-library-section',
		);
	}

	/**
	 * Render the setting section.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_setting_section() : void {
		?>
		<p>
			<?php esc_html_e( 'Movie Library plugin related settings.', 'movie-library' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the option field.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_delete_data_on_delete_field() : void {
		$option = get_option( 'rt-delete-data-on-delete-plugin' );

		$is_checked = 0;
		if ( isset( $option ) && 'on' === $option ) {
			$is_checked = 1;
		}

		?>
		<input
			type='checkbox'
			name='rt-delete-data-on-delete-plugin'
			id='rt-delete-data-on-delete-plugin'
			<?php checked( $is_checked, 1 ); ?>
		/>
		<label
			for='rt-delete-data-on-delete-plugin'
		>
			<?php esc_html_e( 'Delete all content on plugin delete', 'movie-library' ); ?>
		</label>
		<p class="description notice notice-warning">
			<?php esc_html_e( 'If this option is checked, all your data will be deleted when the plugin is deleted.', 'movie-library' ); ?>
		</p>
		<?php
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
		if ( ! current_user_can( self::SETTINGS_CAPABILITY ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Movie Library', 'movie-library' ); ?>
			</h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'movie-library' );
				do_settings_sections( 'movie-library' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

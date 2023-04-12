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

	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 * @since 2.0.0 Add the API URL and Key section in the movie library settings page.
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
			'manage_options',
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

		// Register the settings for API URL.
		register_setting(
			'movie-library',
			'rt-movie-library-api-url',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_url' ),
			)
		);

		// Register the settings for API Key.
		register_setting(
			'movie-library',
			'rt-movie-library-api-key',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_key' ),
			)
		);

		// Add the settings section for delete option.
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

		/**
		 * Add the settings section for delete option.
		 * It contains the input box for API URL and API Key.
		 */
		add_settings_section(
			'movie-library-api-url-and-key-section',
			__( 'API URL and Key', 'movie-library' ),
			array( __CLASS__, 'render_api_url_and_key_section' ),
			'movie-library',
		);

		add_settings_field(
			'rt-movie-library-api-url',
			__( 'API URL', 'movie-library' ),
			array( __CLASS__, 'render_api_url_field' ),
			'movie-library',
			'movie-library-api-url-and-key-section',
		);

		add_settings_field(
			'rt-movie-library-api-key',
			__( 'API Key', 'movie-library' ),
			array( __CLASS__, 'render_api_key_field' ),
			'movie-library',
			'movie-library-api-url-and-key-section',
		);
	}

	/**
	 * Sanitize the API URL.
	 * It also adds the error message to the settings page, if the URL is invalid.
	 *
	 * @param string $url The API URL.
	 *
	 * @return string The sanitized API URL.
	 */
	public static function sanitize_api_url( string $url ) : string {
		// Trim and sanitize the URL.
		$url = trim( $url );
		$url = esc_url_raw( $url, array( 'https', 'http' ) );

		// If the URL is invalid, add the error message to the settings page.
		if ( empty( $url ) ) {
			// Add error to the settings page.
			add_settings_error(
				'rt-movie-library-api-url',
				'rt-movie-library-api-url',
				__( 'API URL is required.', 'movie-library' ),
				'error'
			);
		}

		return $url;
	}

	/**
	 * Sanitize the API Key.
	 * It also adds the error message to the settings page, if the Key is invalid.
	 *
	 * @param string $key The API Key.
	 *
	 * @return string The sanitized API key.
	 */
	public static function sanitize_api_key( string $key ) : string {
		// trim and sanitize the key.
		$key = trim( $key );
		$key = sanitize_text_field( $key );

		// If the key is invalid, add the error message to the settings page.
		if ( empty( $key ) ) {
			// add error to the settings page.
			add_settings_error(
				'rt-movie-library-api-key',
				'rt-movie-library-api-key',
				__( 'API Key is required.', 'movie-library' ),
				'error'
			);
		}

		return $key;
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
	 * Render the API URL and Key section in settings page.
	 *
	 * @return void
	 */
	public static function render_api_url_and_key_section() : void {
		?>
		<p>
			<?php esc_html_e( 'This URL and key are used to fetch the data into the Dashboard IMDB Widget', 'movie-library' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the API URL field in section.
	 *
	 * @return void
	 */
	public static function render_api_url_field() : void {
		?>
		<input
			type='url'
			name='rt-movie-library-api-url'
			id='rt-movie-library-api-url'
			class='regular-text'
			placeholder='<?php esc_html_e( 'Enter API URL here', 'movie-library' ); ?>'
			value='<?php echo esc_attr( get_option( 'rt-movie-library-api-url' ) ); ?>'
		/>
		<?php
	}

	/**
	 * Render the API Key field in section.
	 *
	 * @return void
	 */
	public static function render_api_key_field() : void {
		?>
		<input
			type='text'
			name='rt-movie-library-api-key'
			id='rt-movie-library-api-key'
			placeholder='<?php esc_html_e( 'Enter API key here', 'movie-library' ); ?>'
			value='<?php echo esc_attr( get_option( 'rt-movie-library-api-key' ) ); ?>'
		/>
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
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Movie Library', 'movie-library' ); ?>
			</h1>

			<?php

			/**
			 * Delete the transient on setting page form submit successfully.
			 * Ignore the nonce verification because nonce verification is already handled by options.php, so there is no need to verify it again.
			 */
             // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
				// merge the all errors.
				$errors = array_merge( get_settings_errors( 'rt-movie-library-api-url' ), get_settings_errors( 'rt-movie-library-api-key' ) );

				// check if there is any error.
				$is_error = false;

				foreach ( $errors as $error ) {
					if ( 'error' === $error['type'] ) {
						$is_error = true;
						break;
					}
				}

				// if there is no error, delete the transient that stored the API response.
				if ( ! $is_error ) {
					delete_transient( 'imdb_upcoming_movies' );
				}
			}
			?>

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

<?php
/**
 * Autoload the plugin classes.
 *
 * Autoload the plugin classes when needed. We don't want to load all the classes in each file.
 *
 * @package MovieLibrary
 */

namespace Movie_Library;

/**
 * Autoloader class.
 *
 * It loads the classes automatically when needed.
 */
abstract class Autoloader {
	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public static function register() : void {
		spl_autoload_register( array( __CLASS__, 'loader' ) );
	}

	/**
	 * Autoload the classes.
	 *
	 * @param string $classpath Class path.
	 * @return void
	 */
	private static function loader( string $classpath ) : void {
		// trim the leading backslash.
		$classpath = ltrim( $classpath, '\\' );

		// remove the MOVIE_LIBRARY_NAMESPACE from the classpath.
		if ( ! self::remove_main_namespace( $classpath ) ) {
			return;
		}

		// get the parent directory path of class file.
		$dir_path_without_dir_separator = self::extract_dir_path( $classpath );
		$dir_path                       = self::get_dir_path( $dir_path_without_dir_separator );

		// get file name with .php extension.
		$classname      = self::extract_classname( $classpath );
		$class_filename = self::classname_to_filename( $classname );

		// combine the path and file name.
		$class_filepath = $dir_path . $class_filename;

		// add if exists.
		if ( file_exists( $class_filepath ) ) {
			require_once $class_filepath;
		}

	}

	/**
	 * Remove the main namespace from the classpath.
	 *
	 * @param string $classpath class path used with 'use' keyword.
	 * @return string
	 */
	private static function remove_main_namespace( string &$classpath ) : string {
		// separate the path.
		$classpath_arr = explode( '\\', $classpath );

		// disable autoload for a class which is not from this plugin.
		if ( count( $classpath_arr ) === 0 || MOVIE_LIBRARY_NAMESPACE !== $classpath_arr[0] ) {
			return false;
		}

		// remove 'Movie_Library' element from the array because it will be included in the $root_path.
		array_shift( $classpath_arr );

		// combine the path again.
		$classpath = implode( '\\', $classpath_arr );

		return true;
	}

	/**
	 * Extract the directory path from the classpath.
	 *
	 * @param string $classpath class path used with 'use' keyword.
	 * @return string
	 */
	private static function extract_dir_path( string $classpath ) : string {
		// get the last position of the backslash in classpath.
		$last_backslash_position = strrpos( $classpath, '\\' );

		/*
		 * If the backslash is present in the classpath, then separate the namespace.
		 * Else return the empty path.
		 */
		if ( $last_backslash_position ) {
			return substr( $classpath, 0, $last_backslash_position );
		} else {
			return '';
		}
	}

	/**
	 * Convert the directory path to the directory path with directory separator.
	 *
	 * @param string $dir_path_without_dir_separator directory path without directory separator.
	 * @return string
	 */
	private static function get_dir_path( string $dir_path_without_dir_separator ) : string {
		// replace the backslash with the directory separator.
		$path = str_replace( '_', '-', $dir_path_without_dir_separator );
		$path = strtolower( $path );

		// don't append the DIRECTORY_SEPARATOR if the path is empty.
		if ( '' === $path ) {
			return MOVIE_LIBRARY_PLUGIN_DIR;
		}

		$path = MOVIE_LIBRARY_PLUGIN_DIR . $path;
		return str_replace(
			'\\',
			DIRECTORY_SEPARATOR,
			$path
		) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Extract the class name from the classpath.
	 *
	 * @param string $classpath class path used with 'use' keyword.
	 * @return string
	 */
	private static function extract_classname( string $classpath ) : string {
		// get the last position of the backslash in classpath.
		$last_backslash_position = strrpos( $classpath, '\\' );

		/*
		 * If the backslash is present in the classpath, then separate the classname.
		 * Else return the classpath as classname.
		 */
		if ( $last_backslash_position ) {
			return substr( $classpath, $last_backslash_position + 1 );
		} else {
			return $classpath;
		}
	}

	/**
	 * Get the file name form the class name.
	 *
	 * @param string $classname class name.
	 * @return string
	 */
	private static function classname_to_filename( string $classname ) : string {
		$filename = str_replace( '_', '-', $classname );
		$filename = strtolower( $filename );
		return 'class-' . $filename . '.php';
	}
}


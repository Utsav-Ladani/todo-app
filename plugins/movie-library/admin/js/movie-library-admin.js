/**
 * Admin javascript for movie library plugin
 *
 * @package
 */

/**
 * Change the excerpt label to synopsis.
 * Use anonymous function to prevent client from accessing the function
 */
(function () {
	const { __ } = wp.i18n;

	wp.i18n.setLocaleData({
		Excerpt: [__('Synopsis', 'movie-library')],
		'Write an excerpt (optional)': [
			__('Write a synopsis', 'movie-library'),
		],
	});
})();

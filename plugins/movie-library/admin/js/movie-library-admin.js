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
	wp.i18n.setLocaleData({
		Excerpt: ['Synopsis'],
		'Write an excerpt (optional)': ['Write a synopsis'],
	});
})();

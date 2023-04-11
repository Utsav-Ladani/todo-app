<?php
/**
 * 404 Template
 * It renders if nothing is found for requested URL.
 *
 * @package SmartDroid
 */

get_header();

?>
<div>
	<h1 class="title-h1" ><?php esc_html_e( '404', 'smartdroid' ); ?></h1>
	<h2 class="title-h3"><?php esc_html_e( 'Page not found', 'smartdroid' ); ?></h2>
	<p class="description" ><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'smartdroid' ); ?></p>
</div>
<?php

// get search form.
get_search_form();
get_footer();

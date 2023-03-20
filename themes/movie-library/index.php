<?php
/**
 * Movie Library Index page.
 * If WP not found any template, then it will load this file.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/movie/trending-movies' );

get_footer();

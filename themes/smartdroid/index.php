<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme.
 *
 * @package WordPress
 * @subpackage SmartDroid
 */

get_header();

printf( '<main id="home-page" class="home-page">' );

// The Loop
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'templates/post-card' );
	}
} else {
	// no posts found
}

printf( '</main>' );

get_footer();
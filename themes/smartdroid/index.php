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

printf(
	'<div class="pagination__wrapper">%s</div>',
	get_the_posts_pagination(
		array(
			'prev_text' => '<i class="fa-sharp fa-solid fa-chevron-left"></i>',
			'next_text' => '<i class="fa-sharp fa-solid fa-chevron-right"></i>',
		)
	)
);

get_footer();
<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme.
 *
 * @package WordPress
 * @subpackage SmartDroid
 */

get_header();

echo '<main id="home-page" class="home-page">';

// The Loop.
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'templates/post-card' );
	}
} else {
	printf( '<p class="description">%s</p>', esc_html__( 'Sorry, nothing to show.', 'smartdroid' ) );
}

echo '</main>';

?>
	<div class="pagination__wrapper">
		<?php
		the_posts_pagination(
			array(
				'prev_text' => '<i class="fa-sharp fa-solid fa-chevron-left"></i>',
				'next_text' => '<i class="fa-sharp fa-solid fa-chevron-right"></i>',
			)
		);
		?>
	</div>

<?php

get_footer();

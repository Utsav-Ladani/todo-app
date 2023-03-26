<?php
/**
 * Category archive template
 * This shows list of post for perticular category
 */

get_header();

printf( '<main id="category-archive-page" class="category-archive-page">' );

?>

<div class="category-meta">
    <h3 class="title-h3"><?php esc_html_e( 'Category:', 'movie-library' ); ?></h3>
    <h2 class="title-h1"><?php single_cat_title(); ?></h2>
    <div class="description"><?php echo category_description(); ?></div>
</div>

<?php

// The Loop
if ( have_posts() ) {

	if( have_posts() ) {
		the_post();
		get_template_part(
			'templates/post-card',
			null,
			array(
				'classes' => array( 'post-card--large', 'post-card--large--first' ),
				'show_category' => false,
			) );
	}

	while ( have_posts() ) {
		the_post();
		get_template_part(
			'templates/post-card',
			null,
			array(
				'show_excerpt' => false,
				'show_category' => false,
			)  );
	}
} else {
	// no posts found
}

printf( '</main>' );

get_footer();

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

    // Load the first post and style it differently
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

    // load other posts
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
	printf( '<p class="description">%s</p>', esc_html__( 'Sorry, nothing to show.', 'movie-library' ) );
}

printf( '</main>' );


printf(
	'<div class="pagination__wrapper">%s</div>',
    // pagination
	get_the_posts_pagination(
		array(
			'prev_text' => '<i class="fa-sharp fa-solid fa-chevron-left"></i>',
			'next_text' => '<i class="fa-sharp fa-solid fa-chevron-right"></i>',
		)
	)
);

get_footer();

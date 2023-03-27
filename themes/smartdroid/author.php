<?php
/**
 * Author archive template
 * This shows list of post posted particular author
 */

get_header();

printf( '<main id="author-archive-page" class="author-archive-page">' );

?>

    <div class="author-meta">
        <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" class="author__image" />
        <div class="author__info">
            <h3 class="title-h3"><?php esc_html_e( 'Author-archive:', 'movie-library' ); ?></h3>
            <h2 class="title-h1"><?php the_author_meta( 'display_name' ); ?></h2>
            <div class="description"><?php the_author_meta( 'description' ); ?></div>
            <ul class="author__info__socials">
				<?php
				$social_arr = array( 'facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin' );
				foreach( $social_arr as $social ) :
					?>
                    <li>
                        <a href="#" target="_blank" class="author__info__socials__item">
                            <i class="fab fa-<?php echo esc_attr( $social ) ?>"></i>
                        </a>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
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


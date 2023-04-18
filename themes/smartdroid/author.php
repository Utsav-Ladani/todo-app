<?php
/**
 * Author archive template
 * This shows list of post posted particular author.
 *
 * @package Smartdroid
 */

get_header();

?>

<main id="author-archive-page" class="author-archive-page">

	<div class="author-meta">
		<img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" class="author__image" alt="<?php esc_html_e( 'Author Image', 'smartdroid' ); ?>" />
		<div class="author__info">
			<h3 class="title-h3"><?php esc_html_e( 'Author-archive:', 'smartdroid' ); ?></h3>
			<h2 class="title-h1"><?php the_author_meta( 'display_name' ); ?></h2>
			<div class="description"><?php the_author_meta( 'description' ); ?></div>
			<ul class="author__info__socials">
				<?php
				$social_arr = array( 'facebook', 'twitter', 'instagram', 'youtube', 'pinterest', 'linkedin' );
				foreach ( $social_arr as $social ) :
					?>
					<li>
						<a href="#" target="_blank" class="author__info__socials__item">
							<i class="fab fa-<?php echo esc_attr( $social ); ?>"></i>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

<?php

// The Loop.
if ( have_posts() ) {

	// Load the first post and style it differently.
	if ( have_posts() ) {
		the_post();
		get_template_part(
			'templates/post-card',
			null,
			array(
				'classes'       => array( 'post-card--large', 'post-card--large--first' ),
				'show_category' => false,
			)
		);
	}

	// load other posts.
	while ( have_posts() ) {
		the_post();
		get_template_part(
			'templates/post-card',
			null,
			array(
				'show_excerpt'  => false,
				'show_category' => false,
			)
		);
	}
} else {
	printf( '<p class="description">%s</p>', esc_html__( 'Sorry, nothing to show.', 'smartdroid' ) );
}

?>

</main>

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


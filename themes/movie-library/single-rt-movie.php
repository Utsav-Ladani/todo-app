<?php
/**
 * Movie Library single page for rt-movie post type.
 * It displays the cover section, plot, cast and crew, snapshots, trailer and clips, reviews.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/movie/movie-cover' );

?>
	<div class="section section-with-sidebar">
		<div>
			<h3 class="section-title"><?php esc_html_e( 'Synopsis', 'movie-library' ); ?></h3>
			<div class="the-content">
				<?php echo wp_kses_post( get_the_content() ); ?>
			</div>
		</div>
		<div class="hidden">
			<?php
			if ( has_nav_menu( 'quick-link-menu-movie' ) ) :
				?>
				<div class="sidebar">
					<h3 class="sidebar-title"><?php esc_html_e( 'Quick Links', 'movie-library' ); ?></h3>
					<?php
					// navigation menu for quick links.
					wp_nav_menu(
						array(
							'theme_location' => 'quick-link-menu-movie',
							'container'      => 'nav',
							'menu_class'     => 'quick-links__menu',
						)
					);
					?>
				</div>
				<?php
			endif;
			?>
		</div>
	</div>
<?php

get_template_part( 'template-parts/movie/movie-cast-crew' );

get_template_part( 'template-parts/snapshots' );

get_template_part( 'template-parts/video-gallery', '', array( 'Title' => esc_html__( 'Trailer & Clips', 'movie-library' ) ) );

get_template_part( 'template-parts/movie/movie-reviews' );

get_footer();


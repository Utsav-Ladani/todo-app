<?php
/**
 * Movie Library single page for rt-person post type.
 * It displays the cover section, about section, popular movies, snapshots and videos.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/person/person-cover' );
?>


	<div class="section section-with-sidebar">
		<div>
			<h3 class="section-title"> <?php esc_html_e( 'About Us', 'movie-library' ); ?> </h3>
			<div class="the-content">
				<?php echo wp_kses_post( get_the_content( 'Read more...' ) ); ?>
			</div>
		</div>
		<div class="hidden">
			<?php
			if ( has_nav_menu( 'quick-link-menu-person' ) ) :
				?>
				<div class="sidebar">
					<h3 class="sidebar-title"><?php esc_html_e( 'Quick Links', 'movie-library' ); ?></h3>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'quick-link-menu-person',
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

get_template_part( 'template-parts/person/person-popular-movie' );

get_template_part( 'template-parts/snapshots' );

get_template_part(
	'template-parts/video-gallery',
	'',
	array(
		'Title' => esc_html__( 'Videos', 'movie-library' ),
		'class' => 'person-videos-section',
	)
);


get_footer();


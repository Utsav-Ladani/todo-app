<?php
/**
 * Movie Library Person About section.
 * It displays the person about and sidebar.
 *
 * @package Movie Library
 */

?>

<div class="section section-with-sidebar">
	<div>
		<h3 class="section-title"> <?php esc_html_e( 'About Us', 'movie-library' ); ?> </h3>
		<div class="the-content">
			<?php the_content( 'Read more...' ); ?>
		</div>
	</div>
	<div class="hidden">
		<?php get_template_part( 'template-parts/person/person-sidebar' ); ?>
	</div>
</div>

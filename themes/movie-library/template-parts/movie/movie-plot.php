<?php
/**
 * Movie Library Movie Plot section.
 * It displays the movie plot and sidebar.
 *
 * @package Movie Library
 */

?>

<div class="plot-with-sidebar section">
	<div>
		<h3 class="section-title"><?php esc_html_e( 'Synopsis' ); ?></h3>
		<div class="the-content">
			<?php the_content(); ?>
		</div>
	</div>
	<div class="hidden">
		<?php get_template_part( 'template-parts/movie/movie-sidebar' ); ?>
	</div>
</div>

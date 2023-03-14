<?php
/**
 * Movie Library single page for rt-movie post type.
 * It displays the cover section, plot, cast and crew, snapshots, trailer and clips, reviews.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/movie/movie-cover' );
get_template_part( 'template-parts/movie/movie-plot' );
get_template_part( 'template-parts/movie/movie-cast-crew' );
get_template_part( 'template-parts/movie/movie-snapshots' );
get_template_part( 'template-parts/movie/movie-trailer-clips' );
get_template_part( 'template-parts/movie/movie-reviews' );

get_footer();


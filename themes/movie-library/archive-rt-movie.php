<?php
/**
 * Movie Library archive page for rt-movie post type.
 * It displays the cover section, upcoming movies and trending movies.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/movie-archive/movie-archive-cover' );
get_template_part( 'template-parts/movie-archive/movie-archive-upcoming-movies' );
get_template_part( 'template-parts/movie-archive/movie-archive-trending-movies' );

get_footer();


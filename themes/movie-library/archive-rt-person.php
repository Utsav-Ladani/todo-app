<?php
/**
 * Movie Library archive page for rt-person post type.
 * It displays the list of cast and crew if movie-id present in query string.
 * Otherwise, it displays the list of movies.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/person-archive/person-archive-cast-crew' );

get_footer();


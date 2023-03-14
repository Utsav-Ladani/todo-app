<?php
/**
 * Movie Library single page for rt-person post type.
 * It displays the cover section, about section, popular movies, snapshots and videos.
 *
 * @package Movie Library
 */

get_header();

get_template_part( 'template-parts/person/person-cover' );
get_template_part( 'template-parts/person/person-about' );
get_template_part( 'template-parts/person/person-popular-movie' );
get_template_part( 'template-parts/person/person-snapshots' );
get_template_part( 'template-parts/person/person-videos' );


get_footer();


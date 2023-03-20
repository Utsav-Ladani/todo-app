<?php
/**
 * Movie Library Cast and Crew section.
 * It shows the cast and crew of the movie on single movie page.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';
require_once get_stylesheet_directory() . '/includes/person-utility.php';

use Movie_Library\Custom_Post_Type\Person;

?>

<div class="section movie-cast-crew">
	<div class="section-title-wrap">
		<h3 class="section-title"><?php esc_html_e( 'Cast & Crew', 'movie-library' ); ?></h3>
		<?php
		$archive_link = get_post_type_archive_link( Person::SLUG );
		$archive_link = add_query_arg( 'movie-id', get_the_ID(), $archive_link );
		?>
		<a href="<?php echo esc_url( $archive_link ); ?>" class="view-all hidden"><?php esc_html_e( 'View All', 'movie-library' ); ?></a>
	</div>
	<ul class="movie-cast-crew-list">
	<?php
		$persons = get_cast_crew( get_the_ID(), 8 );

	foreach ( $persons as $person_id ) :
		$src         = get_thumbnail_attachment_url( $person_id );
		$person_name = get_the_title( $person_id );
		$person_link = get_permalink( $person_id );
		?>
				<li class="movie-cast-crew-item">
					<img class="movie-cast-crew-image" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $person_name ); ?>">
					<div class="movie-cast-crew-name">
						<?php echo esc_html( $person_name ); ?>
					</div>
				</li>
			<?php
		endforeach;
	?>
	</ul>
	<div class="view-all-wrapper">
		<a href="<?php echo esc_url( $archive_link ); ?>" class="view-all hidden-desktop"><?php esc_html_e( 'View All', 'movie-library' ); ?></a>
	</div>
</div>

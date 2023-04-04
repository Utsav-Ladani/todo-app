<?php
/**
 * Movie Library Person Cover section.
 * It displays the person cover image and info like name, age, birthplace, debut movie, last movie, etc.
 *
 * @package Movie Library
 */

?>

<?php
require_once get_stylesheet_directory() . '/includes/common-utility.php';
require_once get_stylesheet_directory() . '/includes/person-utility.php';
?>

<div class="person-cover max-container">
	<img class="person-image" src="<?php echo esc_url( get_thumbnail_attachment_url( get_the_ID() ) ); ?>" alt="<?php esc_html_e( 'Person Image', 'movie-library' ); ?>">
	<div class="person-info">
		<div class="person-name-wrap">
			<?php the_title( '<h1 class="person-name">', '</h1>' ); ?>
			<?php
			$full_name = \Movie_Library\APIs\Movie_Library_Metadata_API::get_person_meta( get_the_ID(), 'rt-person-meta-basic-full-name', true );
			$full_name = $full_name ?? '';
			printf( '<span class="person-full-name">%s</span>', esc_html( $full_name ) );
			?>
		</div>
		<?php

		// get the date and format it.
		$date = get_post_birth_date( get_the_ID(), 'j F Y' );

		// get the age.
		$age = gmdate( 'Y' ) - gmdate( 'Y', strtotime( $date ) );

		// get the birthplace.
		$birth_place = \Movie_Library\APIs\Movie_Library_Metadata_API::get_person_meta( get_the_ID(), 'rt-person-meta-basic-birth-place', true );
		$birth_place = $birth_place ?? '';

		$first_movie = get_first_movie( get_the_ID() );
		$last_movie  = get_last_movie( get_the_ID() );

		$active_years = 'NA';
		$debut_movie  = 'NA';

		if ( $last_movie && $first_movie ) {
			$first_movie_release_year = get_post_release_date( $first_movie->ID );
			$last_movie_release_year  = get_post_release_date( $last_movie->ID );

			if ( gmdate( 'Y' ) === $last_movie_release_year ) {
				$last_movie_release_year = esc_html__( 'Present', 'movie-library' );
			}

			$active_years = sprintf( '%s-%s', $first_movie_release_year, $last_movie_release_year );

			$debut_movie = sprintf( '%s (%s)', $first_movie->post_title, $first_movie_release_year );
		}

		$upcoming_movies = get_upcoming_movies_of_person( get_the_ID() );

		?>
		<table class="person-table">
			<tr>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Occupation', 'movie-library' ); ?>:</td>
				<td>
					<?php
					$occupations = get_terms_list( get_the_ID(), 'rt-person-career' );
					echo esc_html( implode( ', ', $occupations ) );
					?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Born', 'movie-library' ); ?>:</td>
				<td>
					<?php echo esc_html( $date ); ?> (
					<?php
					/* translators: %d: age in integer */
					printf( esc_html__( 'age %d years', 'movie-library' ), (int) $age );
					?>
					)
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Birthplace', 'movie-library' ); ?>:</td>
				<td>
					<?php echo esc_html( $birth_place ); ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Years Active', 'movie-library' ); ?>:</td>
				<td><?php echo esc_html( $active_years ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Debut Movie', 'movie-library' ); ?>:</td>
				<td><?php echo esc_html( $debut_movie ); ?></td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Upcoming Movie', 'movie-library' ); ?>:</td>
				<td>
					<?php
					$count = count( $upcoming_movies );
					foreach ( $upcoming_movies as $upcoming_movie ) {
						echo esc_html( $upcoming_movie->post_title );

						// get the date of movie.
						$upcoming_movie_release_date = get_post_release_date( $upcoming_movie->ID );
						echo ' (' . esc_html( $upcoming_movie_release_date ) . ')';

						if ( $count > 1 ) {
							echo ', ';
						}
						$count--;
					}
					?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Socials', 'movie-library' ); ?>:</td>
				<td class="person-social-list">
					<?php
					$social_arr = array(
						array(
							'type' => 'instagram',
							'id'   => 'rt-person-meta-social-instagram',
						),
						array(
							'type' => 'twitter',
							'id'   => 'rt-person-meta-social-twitter',
						),
						array(
							'type' => 'facebook',
							'id'   => 'rt-person-meta-social-facebook',
						),
						array(
							'type' => 'web',
							'id'   => 'rt-person-meta-social-web',
						),
					);

					$social_meta = \Movie_Library\APIs\Movie_Library_Metadata_API::get_person_meta( get_the_ID(), 'rt-person-meta-social', true );

					if ( ! is_array( $social_meta ) ) {
						$social_meta = array();
					}

					foreach ( $social_arr as $social ) {
						$url = $social_meta[ $social['id'] ] ?? '';
						if ( ! empty( $url ) ) {
							$src = get_stylesheet_directory_uri() . '/assets/svg/' . $social['type'] . '-small.svg';
							?>
							<a class="person-social-item" href="<?php echo esc_url( $url ); ?>" target="_blank">
								<img src="<?php echo esc_attr( $src ); ?>" alt="<?php esc_html_e( 'Social Icon', 'movie-library' ); ?>" />
							</a>
							<?php
						}
					}
					?>
				</td>
			</tr>
		</table>
	</div>
</div>

<?php
/**
 * Movie Library Movie Reviews section.
 * It displays the movie reviews section.
 *
 * @package Movie Library
 */

?>

<div id="reviews" class="section movie-review">
	<h3 class="section-title"><?php esc_html_e( 'Reviews', 'movie-library' ); ?></h3>
	<ul class="review-list">
	<?php

	$reviews = get_comments(
		array(
			'post_id' => get_the_ID(),
			'status'  => 'approve',
			'number'  => 4,
		)
	);

	foreach ( $reviews as $review ) {
		?>
		<li class="review-item">
			<div class="review-header">
				<a href="<?php echo esc_url( get_comment_author_url( $review->comment_ID ) ); ?>" >
					<img class="review-avatar" src="<?php echo esc_url( get_avatar_url( $review->comment_author_email ) ); ?>" alt="<?php esc_html_e( 'Avatar', 'movie-library' ); ?>">
				</a>
				<a href="<?php echo esc_url( get_comment_author_url( $review->comment_ID ) ); ?>" >
					<div class="review-author-name"><?php echo esc_html( $review->comment_author ); ?></div>
				</a>
				<span class="review-rating">
					<img class="star-icon" src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/star.svg" alt="<?php esc_html_e( 'Star', 'movie-library' ); ?>" />
					<?php echo esc_html( '8.4/10' ); ?>
				</span>
			</div>
			<div class="review-content">
				<?php echo esc_html( $review->comment_content ); ?>
			</div>
			<div class="review-publish-date">
				<?php
					$date = gmdate( 'jS M Y', strtotime( $review->comment_date ) );
					$date = preg_replace( '/(\d+)(th|st|nd|rd)/', '$1<span class="date-th">$2</span>', $date );
					echo wp_kses( $date, array( 'span' => array( 'class' => array() ) ) );
				?>
			</div>
		</li>
		<?php
	}
	?>
	</ul>
</div>

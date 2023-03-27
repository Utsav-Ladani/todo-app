<?php
/**
 * Comment reply form
 * It contains message, name, email, site url, and submit button
 */

if ( comments_open() ) {
	?>

	<form action="/wp-comments-post.php" method="post" id="commentform" class="comment-form">
		<h2 class="comment-form__title">
			<?php esc_html_e( 'Please leave a comment', 'smartdroid' ); ?>
		</h2>
		<p class="comment-form__title">
            <span><?php esc_html_e( 'Observe the usual rules for comment columns and be nice to one another. We do not store IP addresses of commenting users.', 'smartdroid' ); ?></span>
			<a href="#">
				<?php echo esc_html__( 'This way to the telegram group of ', 'smartdroid' ) . ' ' . esc_html( get_bloginfo( 'name' ) ); ?>
			</a>
		</p>
	</form>

	<?php
}

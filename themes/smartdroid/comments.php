<?php
/**
 * The template for displaying Comments.
 * The area of the page that contains comments and the comment form.
 */

if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
        <div class="comments__header">
            <h2 class="comments__header__title"><?php esc_html_e( 'Join the conversation', 'smartdroid' ); ?></h2>
            <h2 class="comments__header__number-of-comments">
                <i class="fa-sharp fa-solid fa-message"></i>
				<?php
				printf(
					_nx(
						'%s comment',
						'%s comments',
						get_comments_number(),
						'comments title',
						'smartdroid'
					),
					number_format_i18n( get_comments_number() ),
				);
				?>
            </h2>
        </div>

        <ul class="comment__list">
			<?php
			if( have_comments() ) {
				while( have_comments() ) {
					the_comment();
					?>

                    <div class="single-comment">
                        <div class="single-comment__author">
                            <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" alt="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="single-comment__author__avatar" />
                            <div class="single-comment__author__meta" >

                            </div>
                        </div>
                    </div>

					<?php
				}
			} else {
				// no comments
			}
			?>
        </ul>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => '<i class="fa-sharp fa-solid fa-chevron-left"></i>',
				'next_text' => '<i class="fa-sharp fa-solid fa-chevron-right"></i>',
			)
		);

        paginate_comments_links();
		?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
            <p class="no-comments"><?php _e( 'Comments are closed.', 'smartdroid' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php comment_form(); ?>

</div>

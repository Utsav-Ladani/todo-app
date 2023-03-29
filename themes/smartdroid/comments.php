<?php
/**
 * The template for displaying Comments.
 * The area of the page that contains comments and the comment form.
 */

// return if password is required
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
                // print the number of comments
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
			function smartdroid_comment() : void {
				the_comment();
				?>

                <li class="single-comment">
                    <div class="single-comment__author">
                        <img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" alt="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="single-comment__author__avatar" />
                        <div class="single-comment__author__meta" >
                            <div class="single-comment__author__meta__name__wrapper">
                                    <span class="single-comment__author__meta__name">
                                        <?php
                                        // show the name of the comment author, if the author is anonymous, show Anonymous
                                        $comment_author = get_comment_author();
                                        if( empty( $comment_author ) ) {
	                                        echo esc_html__( 'Anonymous', 'smartdroid' );
                                        } else {
	                                        echo esc_html( $comment_author );
                                        }
                                        ?>
                                    </span>
								<?php esc_html_e( 'says:', 'smartdroid' ); ?>
                            </div>
                            <a href="<?php echo esc_url( get_comment_link() ); ?>" class="single-comment__author__meta__timestamp">
								<?php printf( esc_html__( '%s at %s', 'smartdroid' ), get_comment_date(), get_comment_time() ); ?>
                            </a>
                        </div>
                    </div>
                    <div class="single-comment__content">
						<?php comment_text(); ?>
                    </div>
					<?php
                    // get the link to like the comment
					$like_link = get_comment_link();
					$like_link = add_query_arg(
						array(
							'post_id' => get_the_ID(),
							'comment_id' => get_comment_ID(),
							'like' => '1',
							'nonce' => wp_create_nonce( 'like-comment' )
						), $like_link );
					?>
                    <a href="<?php echo esc_url( $like_link ); ?>" class="single-comment__like">
                        <i class="fa fa-star single-comment__like__star"></i>
                        <span class="single-comment__like__text" ><?php esc_html_e( 'Like', 'smartdroid' ); ?></span>
                    </a>
                    <a href="#respond" class="single-comment__reply-link"><?php esc_html_e( 'Reply', 'smartdroid' ); ?></a>
                </li>

				<?php
			}

			// list the comments
			wp_list_comments( array(
				                  'style'      => 'ul',
				                  'short_ping' => true,
				                  'avatar_size'=> 60,
				                  'callback'   => 'smartdroid_comment',
			                  ) );
			?>
        </ul>

		<?php
		//paginate comments
		the_comments_pagination(
			array(
				'prev_text' => '<i class="fa-sharp fa-solid fa-chevron-left"></i>',
				'next_text' => '<i class="fa-sharp fa-solid fa-chevron-right"></i>',
			)
		);
		?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
            <p class="no-comments"><?php _e( 'Comments are closed.', 'smartdroid' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php
    // add the title and notes to the comment form
	if ( comments_open() ) {
		$args = array(
			'title_reply' => '<h2 class="comment-form__title" >' . esc_html__( 'Please leave a comment', 'smartdroid' ) . '</h2>',
			'comment_notes_before' => sprintf(
				'<div class="comment__notes comment__grid--span-2" >
                                <span>%s</span>
                                <a href="#">%s %s</a>
                            </div>
                            <div class="comment__required comment__grid--span-2" >%s</div>',
				esc_html__( 'Observe the usual rules for comment columns and be nice to one another. We do not store IP addresses of commenting users.', 'smartdroid' ),
				esc_html__( 'This way to the telegram group of', 'smartdroid' ),
				esc_html( get_bloginfo( 'name' ) ),
				esc_html__( 'Your email address will not be published. Required fields are marked *', 'smartdroid' )
			),
			'logged_in_as' => sprintf(
				'<div class="comment__notes comment__grid--span-2" >
                                <span>%s</span>
                                <a href="#">%s %s</a>
                            </div>
                            <div class="comment__required comment__grid--span-2" >%s</div>',
				esc_html__( 'Observe the usual rules for comment columns and be nice to one another. We do not store IP addresses of commenting users.', 'smartdroid' ),
				esc_html__( 'This way to the telegram group of', 'smartdroid' ),
				esc_html( get_bloginfo( 'name' ) ),
				esc_html__( 'Your email address will not be published. Required fields are marked *', 'smartdroid' )
			),
		);

		comment_form( $args );
	}
	?>

</div>

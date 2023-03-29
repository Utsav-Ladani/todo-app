<?php
/**
 * Single post template
 * It shows single post
 *
 * @package Smartdroid
 */

get_header();

?>

	<main id="single-post-page" class="single-post-page">
		<?php
		if ( have_posts() ) :
			the_post();
			?>
			<article class="article">
				<div class="article__categories">
					<?php
					// get post categories.
					$categories = get_the_category();
					foreach ( $categories as $category ) {
						printf( '<a href="%s" class="article__category__link">%s</a>', esc_url( get_category_link( $category->term_id ) ), esc_html( $category->name ) );
					}
					?>
				</div>
				<h1 class="article__title"><?php the_title(); ?></h1>
				<div class="article__excerpt"><?php has_excerpt() && the_excerpt(); ?></div>
				<div class="article__author">
					<img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" alt="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="article__author__avatar" />
					<div class="article__author__meta">
						<div class="article__author__link__wrapper">
							<?php esc_html_e( 'by', 'smartdroid' ); ?>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="article__author__link"><?php the_author(); ?></a>
						</div>
						<div class="article__author__timestamp">
							<?php
							// get post date and time on single post page.
							printf( '%s - %s', esc_html( get_the_date() ), esc_html( get_the_time() ) );
							?>
						</div>
					</div>
				</div>
				<?php
				// get post thumbnail.
				if ( has_post_thumbnail() ) {
					the_post_thumbnail(
						'full',
						array(
							'class' => 'article__thumbnail-image',
							'alt'   => esc_attr( get_the_title() ),
						)
					);
				}
				?>
				<div class="article__content container-small"><?php the_content(); ?></div>
				<div class="article__note container-small" >
					<?php esc_html_e( 'Follow us', 'smartdroid' ); ?>
					<a href="#" ><?php esc_html_e( 'on Google News', 'smartdroid' ); ?></a>
					<?php esc_html_e( 'and talk to us in', 'smartdroid' ); ?>
					<a href="#" ><?php esc_html_e( 'Smartdroid Chat on Telegram', 'smartdroid' ); ?></a>.
					<?php esc_html_e( 'Links marked with * are affiliate links. We earn a commission on purchases made through this.', 'smartdroid' ); ?>
				</div>
				<div class="article__about-author container-small">
					<img src="<?php echo esc_url( get_avatar_url( get_the_author_meta( 'ID' ) ) ); ?>" alt="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="article__about-author__avatar" />
					<div class="article__about-author__meta" >
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="article__about-author__meta__name"><?php the_author(); ?></a>
						<hr class="article__about-author__horizontal_line" />
						<div class="article__about-author__meta__social" >
							<?php
							// get social links.
							$socials = array( 'facebook', 'linkedin', 'twitter', 'instagram', 'youtube' );
							foreach ( $socials as $social_name ) :
								?>
								<a href="#" class="article__about-author__meta__social__link" target="_blank">
									<i class="fab fa-lg fa-<?php echo esc_html( $social_name ); ?>"></i>
								</a>
							<?php endforeach; ?>
						</div>
						<div class="article__about-author__meta__description">
							<?php echo esc_html( get_the_author_meta( 'description' ) ); ?>
						</div>
					</div>
				</div>
			</article>
		<?php endif; ?>
		<div class="container-small">
			<?php
			// pagination.
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Return', 'smartdroid' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Further', 'smartdroid' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			// comments.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>
		</div>
	</main>

<?php

get_footer();

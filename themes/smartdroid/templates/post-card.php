<?php
/**
 * The template for displaying post card
 *
 * @package Smartdroid
 */

/**
 * It renders the post card for the given post on archive pages.
 *
 * Available variables:
 * $args['classes'] - array of classes
 * $args['show_excerpt'] - boolean
 * $args['show_category'] - boolean
 */

// set classes to default value.
if ( ! isset( $args['classes'] ) || ! is_array( $args['classes'] ) ) {
	$args['classes'] = array();
}

// add default class.
$args['classes'][] = 'post-card';

// sanitize classes.
foreach ( $args['classes'] as &$class ) {
	$class = sanitize_html_class( $class );
}

// convert classes array to string.
$args['classes'] = implode( ' ', $args['classes'] );

// set default values for show_excerpt and show_category.
if ( ! isset( $args['show_excerpt'] ) ) {
	$args['show_excerpt'] = true;
}

if ( ! isset( $args['show_category'] ) ) {
	$args['show_category'] = true;
}

?>

<article class="<?php echo esc_attr( $args['classes'] ); ?>">
	<a class="post-card__image-wrapper" href="<?php the_permalink(); ?>">
		<?php
		// get the post thumbnail url.
		$src = get_the_post_thumbnail_url();
		if ( ! $src ) {
			$src = get_template_directory_uri() . '/assets/images/placeholder.png';
		}
		?>
		<img src="<?php echo esc_url( $src ); ?>" alt="<?php the_title(); ?>" class="post-card__image">
	</a>
	<div class="post-card__info" >
		<?php if ( $args['show_category'] ) : ?>
			<ul class="post-card__category-list">
				<?php
				// get the first 4 categories.
				$categories = get_the_category();
				$categories = array_slice( $categories, 0, 4 );
				foreach ( $categories as $category ) :
					?>
					<li class="post-card__category-item">
						<a class="post-card__category-item--link" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" >
							<?php echo esc_html( $category->name ); ?>
						</a>
					</li>
					<?php
				endforeach;
				?>
			</ul>
		<?php endif; ?>
		<a class="post-card__title--link" href="<?php the_permalink(); ?>" >
			<h2 class="post-card__title"><?php the_title(); ?></h2>
		</a>
		<div class="post-card__post-meta" >
			<span>
				<span class="post-card__author-label"><?php esc_html_e( 'by', 'smartdroid' ); ?></span>
				<a class="post-card__author" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" ><?php the_author(); ?></a>
			</span>
			<a class="post-card__date" href="<?php the_permalink(); ?>" ><?php the_date(); ?></a>
		</div>
		<?php if ( $args['show_excerpt'] ) : ?>
			<p class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</article>

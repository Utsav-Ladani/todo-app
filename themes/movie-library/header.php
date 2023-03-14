<?php
/**
 * The header.
 * This is the template that displays all the <head> section and everything up until main.
 *
 * @package rtCamp
 * @subpackage Movie_Library
 * @since 1.0.0
 */

?>

<!doctype html>
<html  <?php language_attributes(); ?> >
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> >
<?php wp_body_open(); ?>
<div id="page" class="site">
<div class="header-nav">
	<?php get_template_part( 'template-parts/header/site-header' ); ?>
	<?php get_template_part( 'template-parts/header/site-nav' ); ?>
</div>


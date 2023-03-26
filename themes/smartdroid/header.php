<?php
/**
 * The header.
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @package WordPress
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site" >
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'smartdroid' ); ?></a>
    <div id="page" class="page">

        <header class="desktop">
            <div class="container" >
				<?php
				if( has_nav_menu( 'primary-menu' ) ) {
					wp_nav_menu( array(
						             'theme_location' => 'primary-menu',
						             'container'      => 'nav',
						             'container_class'=> 'primary-menu-container',
						             'menu_class'     => 'primary-menu',
					             ) );
				}
				?>
				<?php echo get_custom_logo(); ?>
                <div class="search-form-wrapper">
                    <span class="search-icon">
                        <i class="fa fa-lg fa-search"></i>
                    </span>
                    <form class="search-form hide">
                        <input type="text" name="s" id="s" class="search-box" placeholder="<?php esc_html_e('Seek...', 'smartdroid'); ?>" />
                        <button class="search-btn" type="submit">
                            <i class="fa fa-lg fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <header class="mobile" >
            <div class="container" >
				<?php echo get_custom_logo(); ?>
                <span class="menu-icon">
                    <i class="fa fa-lg fa-bars"></i>
                    <?php esc_html_e( 'Menu', 'smartdroid' ); ?>
                </span>
            </div>
            <div class="mobile-sidebar-wrapper mobile-overlay">
                <aside class="mobile-sidebar slide-out">
                    <span class="close-icon">
                        <i class="fa fa-close"></i>
                        <?php esc_html_e( 'close', 'smartdroid' ); ?>

                    </span>
                    <form class="mobile-search-form">
                        <input type="text" name="s" id="s" class="mobile-search-box" placeholder="<?php esc_html_e('Seek...', 'smartdroid'); ?>" />
                        <button class="mobile-search-btn" type="submit">
                            <i class="fa fa-lg fa-search"></i>
                        </button>
                    </form>
					<?php
					if( has_nav_menu( 'primary-menu' ) ) {
						wp_nav_menu( array(
							             'theme_location' => 'primary-menu',
							             'container'      => 'nav',
							             'container_class'=> 'primary-mobile-menu-container',
							             'menu_class'     => 'primary-mobile-menu',
						             ) );
					}
					?>
                </aside>
            </div>
        </header>

        <div id="content" class="container content">

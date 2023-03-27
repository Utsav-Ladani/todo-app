<?php
/**
 * The template for displaying the footer
 * Contains the closing of the #content div and all content after.
 *
 * @package WordPress
 */

?>
</div><!-- content -->

<footer>
    <div class="container">
        <div class="footer-site-breadcrumbs">
			<a href="<?php echo esc_url( home_url() ); ?>" rel="nofollow">
                <?php bloginfo( 'name' ); ?>
            </a>
            <?php
			if ( is_category() || is_single() ) {
				echo '<span class="breadcrumbs__separator">&#187;</span>';
				the_category(', ');
				if ( is_single() ) {
					echo ' <span class="breadcrumbs__separator">&#187;</span> ';
					the_title();
				}
			} elseif (is_page()) {
				echo '<span class="breadcrumbs__separator">&#187;</span>';
				echo the_title();
			} elseif (is_search()) {
				echo '<span class="breadcrumbs__separator">&#187;</span>';
                esc_html_e( 'Search results for', 'smartdroid' );
                echo ' ... ';
				printf( '"<em>%s</em>"', get_search_query() );
			}
			?>
        </div>
        <hr class="footer-hr" />
        <div class="footer-menu">
			<?php
			if( has_nav_menu( 'footer-menu' ) ) {
				wp_nav_menu( array(
					             'theme_location' => 'footer-menu',
					             'container'      => 'nav',
					             'container_class'=> 'footer-menu-container',
					             'menu_class'     => 'footer-menu',
				             ) );
			}
			?>
        </div>
        <div class="footer-bottom-wrapper" >
            <div class="footer-company-name">
				<?php
				$company_name = get_theme_mod( 'footer_company_name', esc_html__( 'SmartDroid', 'smartdroid' ) );
				echo '&copy; ';
				printf( ' %d ', date( 'Y' ) );
				echo esc_html( $company_name );
				?>
            </div>
            <a class="privacy-page-link" href="#" >
				<?php esc_html_e( 'Data protection', 'smartdroid' ); ?>
            </a>
            <div class="footer-social">
				<?php
				$arr = array( 'twitter', 'facebook', 'google' );

				foreach ( $arr as $item ) {
					?>
                    <a href="#" class="footer-social-link">
                        <i class="fab fa-lg fa-<?php echo esc_attr( $item ); ?>"></i>
                    </a>
					<?php
				}
				?>
            </div>
        </div>
    </div>
</footer>

</div><!-- page -->
</div><!-- site -->

<?php wp_footer(); ?>

</body>
</html>
<?php
/**
 * Movie Library Footer template.
 * It shows logo and social links on left side and company and explore menus on right side.
 * It encloses the body tag and shows the lightbox.
 *
 * @package Movie Library
 */

?>
<div id="lightbox" class="lightbox display-none">
	<button id="lightbox-close-btn" class="close-btn">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/close.svg" alt="close" />
	</button>
	<div id="lightbox-video">

	</div>
</div>

<footer>
	<div class="widgets">
		<div class="widget-left">
			<h2 class="main-title" >
				<span><?php esc_html_e( 'Screen', 'movie-library' ); ?></span>
				<span class="text-color-accent" ><?php esc_html_e( 'Time', 'movie-library' ); ?></span>
			</h2>
			<h4 class="social-title" ><?php esc_html_e( 'Follow Us', 'movie-library' ); ?></h4>
			<ul class="social">
				<li class="social-item">
					<a href="#" class="social-link" >
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/facebook.svg" alt="facebook" />
					</a>
				</li>
				<li class="social-item">
					<a href="#" class="social-link" >
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/twitter.svg" alt="twitter" />
					</a>
				</li>
				<li class="social-item">
					<a href="#" class="social-link" >
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/youtube.svg" alt="youtube" />
					</a>
				</li>
				<li class="social-item">
					<a href="#" class="social-link" >
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/instagram.svg" alt="instagram" />
					</a>
				</li>
				<li class="social-item">
					<a href="#" class="social-link" >
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/svg/rss.svg" alt="rss" />
					</a>
				</li>
			</ul>
		</div>
		<div class="widget-company">
			<h3 class="widget-title"><?php esc_html_e( 'Company', 'movie-library' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-menu-company',
					'container'      => 'nav',
					'menu_class'     => 'footer-nav__menu',
				)
			);
			?>
		</div>
		<div class="widget-company">
			<h3 class="widget-title"><?php esc_html_e( 'Explore', 'movie-library' ); ?></h3>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-menu-explore',
					'container'      => 'nav',
					'menu_class'     => 'footer-nav__menu',
				)
			);
			?>
		</div>
	</div>
	<hr class="footer-hr" />
	<p class="footer-text" >&#169;
		<?php
		/* translators: %s: current year. */
		printf( esc_html__( '%s ScreenTime. All Rights Reserved. Terms of Service  |  Privacy Policy', 'movie-library' ), esc_html( date_i18n( 'Y' ) ) );
		?>
	</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>

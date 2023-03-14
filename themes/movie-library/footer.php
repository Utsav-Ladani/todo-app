<?php
/**
 * Movie Library Footer template.
 * It shows logo and social links on left side and company and explore menus on right side.
 * It encloses the body tag and shows the lightbox.
 *
 * @package Movie Library
 */

?>
<div id="lightbox" class="display-none">
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
				<span>Screen</span>
				<span class="text-color-accent" >Time</span>
			</h2>
			<h4 class="social-title" >Follow Us</h4>
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
			<h3 class="widget-title">Company</h3>
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
			<h3 class="widget-title">Explore</h3>
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
	<p class="footer-text" >&#169; 2022 ScreenTime. All Rights Reserved.
		Terms of Service  |  Privacy Policy
	</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>

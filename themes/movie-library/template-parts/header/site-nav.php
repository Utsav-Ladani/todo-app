<?php
/**
 * Movie Library site navigation.
 * It displays the navigation menu for desktop and mobile views.
 *
 * @package Movie Library
 */

?>
<div class="mobile-menu position-hover mobile-toggle close">
	<hr class="mobile-menu-line" />
	<div class="explore-menu drop-down-menu">
		<h1 class="mobile-menu-title-wrapper">
		<span class="mobile-menu-title">
			<?php esc_html_e( 'Explore', 'movie-library' ); ?>
		</span>
			<span class="drop-down-arrow-svg"></span>
		</h1>
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'primary-menu-movie',
				'container'       => 'nav',
				'container_class' => 'menu close-drop-down-menu',
				'menu_class'      => 'mobile-nav-menu',
			)
		);
		?>
	</div>
	<hr class="mobile-menu-line" />
	<div class="settings-menu drop-down-menu">
		<h1 class="mobile-menu-title-wrapper">
		<span class="mobile-menu-title">
			<?php esc_html_e( 'Settings', 'movie-library' ); ?>
		</span>
			<span class="drop-down-arrow-svg"></span>
		</h1>
		<nav class="menu close-drop-down-menu">
			<ul class="mobile-nav-menu">
				<li class="menu-item">
					<a href="#">
						<?php esc_html_e( 'Language:', 'movie-library' ); ?>
						<span class="language-value">ENG</span>
					</a>
				</li>
				<li class="menu-item">
					<a href="#">
						<?php esc_html_e( 'Settings', 'movie-library' ); ?>
					</a>
				</li>
				<li class="menu-item">
					<a href="#">
						<?php esc_html_e( 'About', 'movie-library' ); ?>
					</a>
				</li>
			</ul>
		</nav>
	</div>
	<hr class="mobile-menu-line" />
	<div class="mobile-version">
		<?php esc_html_e( 'Version: ', 'movie-library' ); ?>
		<span class="version-value">
			<?php echo esc_html( wp_get_theme()->Version ); ?>
		</span>
	</div>
</div>
<div class="desktop-menu">
	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'primary-menu-movie',
			'container'      => 'nav',
			'menu_class'     => 'desktop-nav-menu',
		)
	);
	?>
</div>

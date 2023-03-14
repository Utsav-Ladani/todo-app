<?php
/**
 * Movie Library Header section template.
 * It shows the logo, search, sign in and language buttons.
 *
 * @package Movie Library
 */

?>

<header class="desktop-header" >
	<div class="header-btn" >
		<span class="search-svg"></span>
		<?php esc_html_e( 'Search', 'movie-library' ); ?>
	</div>
	<h2 class="main-title" >
		<span><?php esc_html_e( 'Screen', 'movie-library' ); ?></span>
		<span class="text-color-accent" ><?php esc_html_e( 'Time', 'movie-library' ); ?></span>
	</h2>
	<div class="header-span-gap">
		<span class="header-btn" >
			<span class="user-svg"></span>
			<?php esc_html_e( 'Sign In', 'movie-library' ); ?>
		</span>
		<span class="header-btn" ><?php esc_html( 'ENG ▼' ); ?></span>
	</div>
</header>

<header class="mobile-header" >
	<div class="mobile-header-main">
		<span class="search-svg" ></span>
		<h2 class="main-title" >
			<span><?php esc_html_e( 'Screen', 'movie-library' ); ?></span>
			<span class="text-color-accent" ><?php esc_html_e( 'Time', 'movie-library' ); ?></span>
		</h2>
		<span id="mobile-toggle-btn" class="bar-svg"></span>
	</div>
	<div class="mobile-header-btns position-hover mobile-toggle close">
		<button class="mobile-header-btn">
			<?php esc_html_e( 'Sign In', 'movie-library' ); ?>
		</button>
		<button class="mobile-header-btn mobile-btn-register">
			<?php esc_html_e( 'Register for FREE', 'movie-library' ); ?>
		</button>
	</div>
</header>

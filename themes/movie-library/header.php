<?php
/**
 * The header.
 * This is the template that displays all the <head> section and everything up until main.
 * It shows the logo, search, sign in and language buttons.
 * It also displays the navigation menu for desktop and mobile views.
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
		<div class="max-container">
		<header class="desktop-header" >
			<div class="header-btn" >
				<span class="search-svg"></span>
				<?php esc_html_e( 'Search', 'movie-library' ); ?>
			</div>
			<h2 class="main-title" >
				<a href="/">
					<span><?php esc_html_e( 'Screen', 'movie-library' ); ?></span>
					<span class="text-color-accent" ><?php esc_html_e( 'Time', 'movie-library' ); ?></span>
				</a>
			</h2>
			<div class="header-span-gap">
		<span class="header-btn" >
			<span class="user-svg"></span>
			<?php esc_html_e( 'Sign In', 'movie-library' ); ?>
		</span>
				<div class="drop-down-menu">
					<span class="header-btn" >
						<?php echo esc_html( 'ENG' ); ?>
						<span class="negative-triangle-svg drop-down-btn"></span>
					</span>
					<nav class="header-triangle-menu">
						<span><?php esc_html_e( 'Settings', 'movie-library' ); ?></span>
						<span><?php esc_html_e( 'Location', 'movie-library' ); ?></span>
						<span><?php esc_html_e( 'Preference', 'movie-library' ); ?></span>
					</nav>
				</div>
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
		</div>
		</header>

		<div class="mobile-menu position-hover mobile-toggle close">
			<hr class="mobile-menu-line" />
			<div class="explore-menu drop-down-menu">
				<h1 class="mobile-menu-title-wrapper">
				<span class="mobile-menu-title">
					<?php esc_html_e( 'Explore', 'movie-library' ); ?>
				</span>
					<span class="drop-down-arrow-svg drop-down-btn"></span>
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
					<span class="drop-down-arrow-svg drop-down-btn"></span>
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
	</div>
	<div class="container" >

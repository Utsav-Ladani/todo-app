<?php
/**
 * Movie Library Movie Sidebar.
 * It displays the quick links menu in the sidebar.
 *
 * @package Movie Library
 */

if ( has_nav_menu( 'quick-link-menu-movie' ) ) :
	?>
<div class="sidebar">
	<h3 class="sidebar-title"> Quick Links </h3>
	<?php
	// navigation menu for quick links.
	wp_nav_menu(
		array(
			'theme_location' => 'quick-link-menu-movie',
			'container'      => 'nav',
			'menu_class'     => 'quick-links__menu',
		)
	);
	?>
</div>
	<?php
	endif;

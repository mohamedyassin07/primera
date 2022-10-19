<?php
/**
 * Primera
 *
 * @package       PRIMERA
 * @author        Mohamed Yassin
 * @version       1.0.1
 *
 * @wordpress-plugin
 * Plugin Name:   Primera
 * Plugin URI:    https://primera.app/
 * Description:   Primera.app cutom plugin
 * Version:       1.0.1
 * Author:        Mohamed Yassin
 * Author URI:    https://github.com/mohamedyassin07/
 * Text Domain:   primera
 * Domain Path:   /languages
 */

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'PRIMERA_NAME',			'Primera' );

// Plugin version
define( 'PRIMERA_VERSION',		'1.0.1' );

// Plugin Root File
define( 'PRIMERA_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'PRIMERA_PLUGIN_BASE',	plugin_basename( PRIMERA_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'PRIMERA_PLUGIN_DIR',	plugin_dir_path( PRIMERA_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'PRIMERA_PLUGIN_URL',	plugin_dir_url( PRIMERA_PLUGIN_FILE ) );

/**
 * Set Plugin Debug status
 * if not defined in wp-config
 * check it's dashboard option value
 */
if ( ! defined( 'PRIMERA_DEBUG' ) ) {
	$primera_options = get_option( 'primera_option' );
	$debug = isset( $primera_options[ 'debug' ] ) &&  $primera_options[ 'debug' ] ==='debug' ? true :  false;
	define( 'PRIMERA_DEBUG', $debug );
}

/**
 * Load the main class for the core functionality
 */
require_once PRIMERA_PLUGIN_DIR . 'includes/class-primera.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Mohamed Yassin
 * @since   1.0.0
 * @return  object|Primera
 */
function PRIMERA() {
	return Primera::instance();
}

PRIMERA();

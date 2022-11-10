<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Primera_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		PRIMERA
 * @subpackage	Classes/Primera_Run
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Primera_Run{

	/**
	 * Our Primera_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
		add_action( 'plugin_action_links_' . PRIMERA_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );

		$this->includes();
		$this->include_modules();
	}


	/**
	 * Includes required Classes
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	private function includes()
	{
		require_once PRIMERA_PLUGIN_DIR . 'includes/helpers/basics.php';
	}

	/**
	 * Include required Modules
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	private function include_modules()
	{
		require_once PRIMERA_PLUGIN_DIR . 'modules/profit-tax/profit-tax-woocommerce.php';
		require_once PRIMERA_PLUGIN_DIR . 'modules/admitad/admitad-woocommerce.php';
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['Settings'] = sprintf( '<a href="%s" title="Settings" style="font-weight:700;">%s</a>', 'http://circlepay/wp-admin/admin.php?page=primera', __( 'Settings', 'primera' ) );

		return $links;
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_style( 'primera-backend-styles', PRIMERA_PLUGIN_URL . 'assets/css/backend-styles.css', array(), PRIMERA_VERSION, 'all' );
		wp_enqueue_script( 'primera-backend-scripts', PRIMERA_PLUGIN_URL . 'assets/js/backend-scripts.js', array( 'jquery' ), PRIMERA_VERSION, true );
		wp_localize_script( 'primera-backend-scripts', 'primera', array(
			'plugin_name'   	=> __( PRIMERA_NAME, 'primera' ),
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "primera_ajax_nonce" ),
		));
	}


	/**
	 * Enqueue the frontend related scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles() {
		wp_enqueue_style( 'primera-frontend-styles', PRIMERA_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), PRIMERA_VERSION, 'all' );
		wp_enqueue_script( 'primera-frontend-scripts', PRIMERA_PLUGIN_URL . 'assets/js/frontend-scripts.js', array( 'jquery' ), PRIMERA_VERSION, true );
		wp_localize_script( 'primera-frontend-scripts', 'primera', array(
			'demo_var'   		=> __( 'This is some demo text coming from the backend through a variable within javascript.', 'primera' ),
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "primera_ajax_nonce" ),
		));
	}
}

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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 1000 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 1000 );

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
		require_once PRIMERA_THEME_DIR . 'includes/helpers/basics.php';
	}

	/**
	 * Include required Modules
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	private function include_modules()
	{
		require_once PRIMERA_THEME_DIR . 'includes/modules/profit-tax/profit-tax-woocommerce.php';
		require_once PRIMERA_THEME_DIR . 'includes/modules/admitad/admitad-woocommerce.php';
		require_once PRIMERA_THEME_DIR . 'includes/modules/coupons_max_val/coupons_max_val.php';
		require_once PRIMERA_THEME_DIR . 'includes/modules/bulk_mailing/bulk_mailing.php';
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

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
		wp_enqueue_style( 'primera-backend-styles', PRIMERA_THEME_URL . 'assets/css/backend-styles.css', array(), PRIMERA_VERSION, 'all' );
		wp_enqueue_script( 'primera-backend-scripts', PRIMERA_THEME_URL . 'assets/js/backend-scripts.js', array( 'jquery' ), PRIMERA_VERSION, true );
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
		if ( is_rtl() ) {
			// Theme styles: Grid (enqueue before shop styles)
			wp_dequeue_style( 'nm-grid' );
			wp_enqueue_style( 'nm-grid-rtl', PRIMERA_THEME_URL . '/assets/css/grid.rtl.css', array(), NM_THEME_VERSION_CHILD, 'all');
	
			wp_dequeue_style( 'nm-shop' );
			wp_enqueue_style( 'nm-shop-rtl', PRIMERA_THEME_URL . '/assets/css/shop.rtl.css', array(), NM_THEME_VERSION_CHILD, 'all');
	
			// Parent theme main style.css
			wp_dequeue_style( 'nm-core' );
			wp_enqueue_style( 'nm-core-rtl', PRIMERA_THEME_URL . '/assets/css/style.rtl.css', array(), NM_THEME_VERSION_CHILD, 'all');
		}
	
		 // Enqueue child theme styles
		 wp_enqueue_style( 'nm-child-theme', PRIMERA_THEME_URL . '/style.css', array(), NM_THEME_VERSION_CHILD, 'all');
	
		 wp_dequeue_style( 'wp-block-library' );
		 wp_dequeue_style( 'wc-block-style' );
	
	
		if ( is_checkout() ) {
			wp_enqueue_script('nm-shop-checkout-extend', PRIMERA_THEME_URL . '/assets/js/nm-shop-checkout-extend.js', array('jquery', 'nm-shop', 'nm-shop-checkout'), NM_THEME_VERSION, true);
			// wpexperts tap payment customization
			wp_enqueue_script('wpex-tap-payment-js', PRIMERA_THEME_URL . '/assets/js/tap-payment.js', array('jquery'), NM_THEME_VERSION_CHILD, true);
			wp_localize_script('wpex-tap-payment-js', 'variables', array(
				'knet_logo' => PRIMERA_THEME_URL . '/assets/images/knet-logo.png',
				'next' => __('التالي', 'alkhdmah'),
				'place_order' => __('تأكيد الطلب', 'alkhdmah'),
			));
		}
		
		// wpexperts order pay page (hyperpay) customization
		if(is_wc_endpoint_url('order-pay')){
			wp_enqueue_script('wpex-order-pay-js', PRIMERA_THEME_URL . '/assets/js/order-pay.js', array('jquery'), NM_THEME_VERSION_CHILD, true);
			wp_localize_script('wpex-order-pay-js', 'variables', array(
				'checkout_url' => wc_get_checkout_url(),
				'back' => __('عودة', 'alkhdmah'),
				'add' => __('إضافة بطاقة جديدة', 'alkhdmah'),
			));
		}
	
		// if ( is_front_page() ) {
		//     wp_register_script( 'alk-home', PRIMERA_THEME_URL . '/assets/js/home.js', array( 'jquery' ), NM_THEME_VERSION_CHILD, false );
		//     wp_enqueue_script( 'alk-home' );
		// }
		wp_enqueue_style( 'primera-frontend-styles', PRIMERA_THEME_URL . 'assets/css/frontend-styles.css', array(), PRIMERA_VERSION, 'all' );
		wp_enqueue_script( 'primera-frontend-scripts', PRIMERA_THEME_URL . 'assets/js/frontend-scripts.js', array( 'jquery' ), PRIMERA_VERSION, true );
		wp_localize_script( 'primera-frontend-scripts', 'primera', array(
			'demo_var'   		=> __( 'This is some demo text coming from the backend through a variable within javascript.', 'primera' ),
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "primera_ajax_nonce" ),
		));
	}
}

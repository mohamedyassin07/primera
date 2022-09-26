<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Primera' ) ) :

	/**
	 * Main Primera Class.
	 *
	 * @package		PRIMERA
	 * @subpackage	Classes/Primera
	 * @since		1.0.0
	 * @author		Mohamed Yassin
	 */
	final class Primera {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Primera
		 */
		private static $instance;

		/**
		 * PRIMERA helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Primera_Helpers
		 */
		public $helpers;

		/**
		 * PRIMERA settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Primera_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'primera' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'primera' ), '1.0.0' );
		}

		/**
		 * Main Primera Instance.
		 *
		 * Insures that only one instance of Primera exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Primera	The one true Primera
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Primera ) ) {
				self::$instance					= new Primera;
				self::$instance->base_hooks();
				self::$instance->includes();

				//Fire the plugin logic
				new Primera_Run();
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once PRIMERA_PLUGIN_DIR . 'includes/classes/class-primera-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'primera', FALSE, dirname( plugin_basename( PRIMERA_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.
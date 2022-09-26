<?php

if (!defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('PRIMERA_Profit_Tax_WooCommerce') ) {

    class PRIMERA_Profit_Tax_WooCommerce {
        /**
         *
         * @var instance object
         */
        protected static $_instance = null;

        /**
         * @see PRIMERA_Profit_Tax_WooCommerce()
         * @return object
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct()
        {
            add_action('wp', array($this, 'include_files'));
            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        }

        public function include_files()
        {
            include('includes/class-product-settings.php');
            include('includes/class-api.php');
            include('includes/class-tax.php');
            
            include('includes/class-prices-with-tax.php');
            // include('includes/class-point-price-compatibility.php');
        }

        public function load_plugin_textdomain()
        {
            $domain = 'profit-tax-woocommerce';
            $dir = untrailingslashit( WP_LANG_DIR );
            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            if ($exists = load_textdomain($domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo')) {
                return $exists;
            } else {
                load_plugin_textdomain( $domain, FALSE, basename(dirname(__FILE__)) . '/languages/' );
            }
        }

    }

    function PRIMERA_Profit_Tax_WooCommerce()
    {
        return PRIMERA_Profit_Tax_WooCommerce::instance();
    }

    PRIMERA_Profit_Tax_WooCommerce();
}
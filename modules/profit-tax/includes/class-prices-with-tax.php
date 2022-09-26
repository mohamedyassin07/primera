<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PRIMERA_PRICES_WITH_TAX')) {

    class PRIMERA_PRICES_WITH_TAX {
        public function __construct() {
            if( ! is_product() ){
                return;
            }

            // Simple, grouped and external products
            add_filter('woocommerce_product_get_price', array( $this, 'custom_price' ), 99, 2 );
            add_filter('woocommerce_product_get_regular_price', array( $this, 'custom_price' ), 99, 2 );
            // Variations 
            add_filter('woocommerce_product_variation_get_regular_price', array( $this, 'custom_price' ), 99, 2 );
            add_filter('woocommerce_product_variation_get_price', array( $this, 'custom_price' ), 99, 2 );

            // Variable (price range)
            add_filter('woocommerce_variation_prices_price', array( $this, 'custom_variable_price' ), 99, 3 );
            add_filter('woocommerce_variation_prices_regular_price', array( $this, 'custom_variable_price' ), 99, 3 );

            // Handling price caching (see explanations at the end)
            add_filter( 'woocommerce_get_variation_prices_hash', array( $this, 'add_price_multiplier_to_variation_prices_hash' ), 99, 3 );
        }

        // Utility function to change the prices with a multiplier (number)
        public function get_price_multiplier() {
            return 2; // x2 for testing
        }

        public function custom_price( $price, $product ) {
            return (float) $price * $this->get_price_multiplier();
        }

        public function custom_variable_price( $price, $variation, $product ) {
            return (float) $price * $this->get_price_multiplier();
        }

        public function add_price_multiplier_to_variation_prices_hash( $price_hash, $product, $for_display ) {
            $price_hash[] = $this->get_price_multiplier();
            return $price_hash;
        }

    }
    new PRIMERA_PRICES_WITH_TAX();
}
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

        public function get_price_multiplier($product_id = 0, $variation_id = 0 ) {
            $api = new PRIMERA_Profit_Tax_API( $product_id, $variation_id, 1, 1);
            $tax = $api->get_tax();
            return 1 + $tax;
        }

        public function custom_price( $price, $product ) {
            return (float) $price * $this->get_price_multiplier( $product->get_id() );
        }

        public function custom_variable_price( $price, $variation, $product ) {
            // echo " this var id  {$variation->get_id()}";
            return (float) $price * $this->get_price_multiplier( $product->get_id() , $variation->get_id()  );
        }

        public function add_price_multiplier_to_variation_prices_hash( $price_hash, $product, $for_display ) {
            $price_hash[] = $this->get_price_multiplier( $product->get_id() );
            return $price_hash;
        }

    }
    new PRIMERA_PRICES_WITH_TAX();
}
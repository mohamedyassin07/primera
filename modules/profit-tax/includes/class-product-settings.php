<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PRIMERAPTax_Product_Settings')) {

    class PRIMERAPTax_Product_Settings {

        public function __construct() {
            //for simple
            add_action('woocommerce_product_options_general_product_data', array($this, 'add_custom_field_data_simple_product'));
            add_action('woocommerce_process_product_meta', array($this, 'process_simple_product_meta'));
            //for variable
            add_action('woocommerce_product_after_variable_attributes', array($this, 'add_custom_field_data_variable_product'), 10, 3);
            add_action('woocommerce_save_product_variation', array($this, 'process_variable_product_meta'), 10, 2);
        }

        public function add_custom_field_data_simple_product() {
            echo '<div class="options_group show_if_simple">';
            woocommerce_wp_text_input(
                    array(
                        'id' => '_cwg_profit_tax_raw_price',
                        'label' => __('Raw Price', 'profit-tax-woocommerce'),
                        'placeholder' => __('Enter Raw Price for this product', 'profit-tax-woocommerce'),
                        'desc_tip' => 'true'
                    )
            );
            echo '</div>';
        }

        public function process_simple_product_meta($post_id) {
            $raw_price_data = $_POST['_cwg_profit_tax_raw_price'];
            update_post_meta($post_id, '_cwg_profit_tax_raw_price', esc_attr($raw_price_data));
        }

        //for variable product each variation
        public function add_custom_field_data_variable_product($loop, $variation_data, $variation) {
            echo '<div class="options_group form-row form-row-full">';
            woocommerce_wp_text_input(
                    array(
                        'id' => '_cwg_profit_tax_raw_price[' . $variation->ID . ']',
                        'label' => __('Raw Price', 'profit-tax-woocommerce'),
                        'placeholder' => __('Enter Raw Price for this Variation', 'profit-tax-woocommerce'),
                        'desc_tip' => true,
                        'description' => __("Please Enter Raw Price of this Variation to calculate profit tax correctly", "profit-tax-woocommerce"),
                        'value' => get_post_meta($variation->ID, '_cwg_profit_tax_raw_price', true)
                    )
            );
            echo '</div>';
        }

        //process the variation form data 

        public function process_variable_product_meta($post_id, $i) {
            $raw_price_data = $_POST['_cwg_profit_tax_raw_price'][$post_id];
            update_post_meta($post_id, '_cwg_profit_tax_raw_price', esc_attr($raw_price_data));
        }

    }

    new PRIMERAPTax_Product_Settings();
}
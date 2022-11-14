<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PRIMERA_Profit_Point_Price_Compatible')) {

    class PRIMERA_Profit_Point_Price_Compatible {

        public function __construct() {
            add_filter('cwgprofit_tax_validation', array($this, 'add_compatibility_point_price'), 10, 5);
        }

        public function add_compatibility_point_price($passed, $product_id, $variation_id, $quantity, $line_total) {
            $pp_api = new PRIMERA_myCred_API($product_id);
            if ($pp_api->get_points()) {
                return false; //additional check to avoid tax for point purchase products
            }
            return $passed;
        }

    }

    new PRIMERA_Profit_Point_Price_Compatible();
}

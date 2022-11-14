<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PRIMERA_Profit_Tax_API')) {

    class PRIMERA_Profit_Tax_API {

        public $tax_percentage = 15; //tax percentage to calculate on top of profit

        public function __construct($product_id = 0, $variation_id = 0, $quantity = 0, $line_total = 0, $is_coupon = false, $is_total_coupon = false) {
            $this->product_id = $product_id;
            $this->variation_id = $variation_id;
            $this->quantity = $quantity;
            $this->line_total = $line_total;
            $this->is_coupon = $is_coupon;
            $this->is_total_coupon = $is_total_coupon;
        }

        public function get_raw_price() {
            if ($this->variation_id > 0) {
                $raw_price = get_post_meta($this->variation_id, '_cwg_profit_tax_raw_price', true);
            } else {
                $raw_price = get_post_meta($this->product_id, '_cwg_profit_tax_raw_price', true);
            }
            return apply_filters('alk_profit_tax_raw_price', $raw_price != '' ? $raw_price : 0, $this->variation_id, $this->product_id);
        }

        public function get_profit_amount() {
            $profit = 0;
            $get_raw_price = $this->get_raw_price();
            $get_raw_price = $get_raw_price >  0 ? $get_raw_price : 0 ;

                $get_quantity = $this->quantity;
                $total_raw_cost = $get_raw_price * $get_quantity;
                $get_line_total = $this->line_total;
                //below condition check only when the total cost is higher than raw cost for to get the profit
                if ($get_line_total > $total_raw_cost) {
                    $profit = $get_line_total - $total_raw_cost;
                }

                return $profit;
        }

        public function get_tax() {
            $calculation = 0;
            $profit_amount = $this->get_profit_amount();
            // add compatibility filter to validate point price product/specific category products
            $allow_tax = apply_filters('cwgprofit_tax_validation', true, $this->product_id, $this->variation_id, $this->quantity, $this->line_total);
            if ($profit_amount > 0 && $allow_tax) {
                $calculation = $this->tax_percentage / 100;
                
                if($this->is_coupon){
                    if($this->is_total_coupon){
                        $calculation = ($calculation * $profit_amount) * (1 - $this->is_coupon);
                    }else{
                        $calculation = ($calculation * $profit_amount) * $this->is_coupon;
                    }
                }else{
                    $calculation = ($calculation * $profit_amount);
                }
            }
            return $calculation;
        }

    }

}
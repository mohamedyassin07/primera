<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PRIMERA_Profit_Tax_Functionality')) {

    class PRIMERA_Profit_Tax_Functionality {

        public function __construct() {
            //add_action('woocommerce_cart_calculate_fees', array($this, 'calculate_profit_tax'));
            //Multi Currency Support based on payment gateway
            add_filter('alk_profit_tax_raw_price', array($this, 'alter_price_according_to_currency'), 10, 3);
            
            // change taxe value for cart item (if VAT)
            add_filter('woocommerce_calculate_item_totals_taxes', array($this, 'alter_tax_price'), 20, 3);
            
            // do_action( 'woocommerce_calculate_totals', $this->cart );
            add_action( 'woocommerce_calculate_totals', array($this, 'check_cart'), 20);
            
            // apply_filters( 'woocommerce_get_price_including_tax', $return_price, $qty, $product );
            add_filter( 'woocommerce_get_price_including_tax', array($this, 'change_cart_product_price'), 20, 3 );
            
            // do_action( 'woocommerce_order_after_calculate_totals', $and_taxes, $this );
            add_action( 'woocommerce_order_after_calculate_totals', array($this, 'change_order_tax'), 20, 2 );


        }
        
        public function change_order_tax($and_taxes, $order) {
            $check = false;
            $customer = null;
            WC()->initialize_session();
            if($order->get_user()){
                $user = $order->get_user();
                $customer = new WC_Customer($user->ID, false);

                require_once dirname( WC_PLUGIN_FILE ) . '/includes/wc-cart-functions.php';
            }elseif($order->get_user_id()){
                $user = get_userdata($order->get_user_id());
                $customer = new WC_Customer($order->get_user_id(), false);

                require_once dirname( WC_PLUGIN_FILE ) . '/includes/wc-cart-functions.php';
            }
            $items = $order->get_items();
            foreach($items as $item){
                $info_total['product_id'] = $item->get_product_id();
                $info_total['variation_id'] = $item->get_variation_id();
                $info_total['quantity'] = $item->get_quantity();
                $info_total['line_total'] = $item->get_total();
                $info_total['cent'] = 1;
                $info_total['custom_tax'] = $this->is_custom_tax($info_total['product_id'], $info_total['variation_id']);
                
                $info_subtotal['product_id'] = $item->get_product_id();
                $info_subtotal['variation_id'] = $item->get_variation_id();
                $info_subtotal['quantity'] = $item->get_quantity();
                $info_subtotal['line_total'] = $item->get_subtotal();
                $info_subtotal['cent'] = 1;
                $info_subtotal['custom_tax'] = $this->is_custom_tax($info_subtotal['product_id'], $info_subtotal['variation_id']);
                $price_includes_tax = wc_prices_include_tax();
                if($item->get_product()->is_taxable()){
                    $tax_rates = WC_Tax::get_rates( $item->get_product()->get_tax_class(), $customer );
                    $taxes     = $this->custom_calc_tax( $info_total['line_total'], $tax_rates, $price_includes_tax, $info_total );
                    $subtaxes     = $this->custom_calc_tax( $info_subtotal['line_total'], $tax_rates, $price_includes_tax, $info_subtotal );
                    
                    //if(count($taxes) && count($subtaxes) ){
                        $tax_data     = array(
                                'total'    => $taxes,
                                'subtotal' => $subtaxes
                        );
                        
                        $item->set_taxes($tax_data);
                        $check = true;
                    //}
                    
                }
                
                if($check){
                    $order->update_taxes();
                    remove_action('woocommerce_order_after_calculate_totals', array($this, 'change_order_tax'), 20);
                    $order->calculate_totals(false);
                    add_action( 'woocommerce_order_after_calculate_totals', array($this, 'change_order_tax'), 20, 2 );
                }
            }
        }
        
        public function change_cart_product_price($return_price, $qty, $product) {
            $info = array();
            if($product->is_type('simple')){
                $info['product_id'] = $product->get_id();
                $info['variation_id'] = 0;
            }elseif($product->is_type('variation') || $product->is_type('variation') ){
                $info['product_id'] = $product->get_parent_id() ;
                $info['variation_id'] = $product->get_id();
            }
            $price =  $product->get_price();

            if ( '' === $price ) {
                    return '';
            } elseif ( empty( $qty ) ) {
                    return 0.0;
            }

            $line_price   = $price * $qty;
            $return_price = $line_price;
            
            $info['quantity'] = $qty;
            $info['line_total'] = $line_price;
            
            $info['cent'] = 1;
            $info['custom_tax'] = $this->is_custom_tax($info['product_id'], $info['variation_id']);

            if ( $product->is_taxable() ) {
                    if ( ! wc_prices_include_tax() ) {
                            $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
                            $taxes     = $this->custom_calc_tax( $line_price, $tax_rates, false, $info );

                            if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                                    $taxes_total = array_sum( $taxes );
                            } else {
                                    $taxes_total = array_sum( array_map( 'wc_round_tax_total', $taxes ) );
                            }

                            $return_price = round( $line_price + $taxes_total, wc_get_price_decimals() );
                    } else {
                            $tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
                            $base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

                            /**
                             * If the customer is excempt from VAT, remove the taxes here.
                             * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
                             */
                            if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) { // @codingStandardsIgnoreLine.
                                    $remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );

                                    if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                                            $remove_taxes_total = array_sum( $remove_taxes );
                                    } else {
                                            $remove_taxes_total = array_sum( array_map( 'wc_round_tax_total', $remove_taxes ) );
                                    }

                                    $return_price = round( $line_price - $remove_taxes_total, wc_get_price_decimals() );

                                    /**
                             * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
                             * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
                             * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
                             */
                            } elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
                                    $base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
                                    $modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );

                                    if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                                            $base_taxes_total   = array_sum( $base_taxes );
                                            $modded_taxes_total = array_sum( $modded_taxes );
                                    } else {
                                            $base_taxes_total   = array_sum( array_map( 'wc_round_tax_total', $base_taxes ) );
                                            $modded_taxes_total = array_sum( array_map( 'wc_round_tax_total', $modded_taxes ) );
                                    }

                                    $return_price = round( $line_price - $base_taxes_total + $modded_taxes_total, wc_get_price_decimals() );
                            }
                    }
            }
        
            return $return_price;
        }
        
        public function check_cart($cart) {
            
            // fix coupon taxes
            $this->update_coupon_taxes($cart);
            
            $newItems = array();
            $merged_subtotal_taxes = array();
            $items = $cart->get_cart();
            foreach($items as $item_key => $item){
                $info['product_id'] = $item['product_id'];
                $info['variation_id'] = $item['variation_id'];
                $info['quantity'] = $item['quantity'];
                $info['line_total'] = $item['line_subtotal'];
                $info['cent'] = 100;
                $info['custom_tax'] = $this->is_custom_tax($info['product_id'], $info['variation_id']);
                $price_includes_tax = wc_prices_include_tax();
                if($item['data']->is_taxable()){
                    $tax_rates = WC_Tax::get_rates( $item['data']->get_tax_class(), $cart->get_customer() );

                    if($info['custom_tax']){
                        $taxes = $this->custom_calc_tax($info['line_total'], $tax_rates, $price_includes_tax, $info);
                    }else{
                        $taxes = WC_Tax::calc_tax($info['line_total'], $tax_rates, $price_includes_tax);
                        $taxes = array_map(function($el) { return $el * 100; }, $taxes);
                    }

                    foreach ( $taxes as $rate_id => $rate ) {
                        if ( ! isset( $merged_subtotal_taxes[ $rate_id ] ) ) {
                            $merged_subtotal_taxes[ $rate_id ] = 0;
                        }
                        $merged_subtotal_taxes[ $rate_id ] += $this->round_line_tax($rate);
                    }

                    $item['line_tax_data']['subtotal'] = wc_remove_number_precision_deep($taxes);//     = array( 'subtotal' => wc_remove_number_precision_deep($taxes) );
                    $item['line_subtotal_tax'] = wc_remove_number_precision(array_sum(array_map( array( $this, 'round_line_tax' ), $taxes )));
                }
                
                $newItems[$item_key] = $item;
            }

            $cart->set_cart_contents($newItems);
            $cart->set_subtotal_tax( (wc_round_tax_total( array_sum( $merged_subtotal_taxes ), 0 ))/100 );
        }

        public function calculate_profit_tax($cart) {
            if (is_admin() && !defined('DOING_AJAX')) {
                return;
            }
            $apply_tax = 0;
            $get_current_cart = WC()->cart->get_cart();

            if ($get_current_cart) {
                foreach ($get_current_cart as $each_cart) {
                    $get_product_id = $each_cart['product_id'];
                    $get_variation_id = $each_cart['variation_id'];
                    $quantity = $each_cart['quantity'];
                    $get_linetotal = $each_cart['line_total']; //contain value with discounted price along with quantity
                    $api = new PRIMERA_Profit_Tax_API($get_product_id, $get_variation_id, $quantity, $get_linetotal);
                    $apply_tax += $api->get_tax();
                }
            }
            if ($apply_tax > 0) {
                WC()->cart->add_fee(__('Special Tax:', 'profit-tax-woocommerce'), $apply_tax, false);
            }
        }

        public function alter_price_according_to_currency($raw_price, $variation_id, $product_id) {
            $current_gateway = WC()->session->chosen_payment_method;
            if ('' != $current_gateway) {
                $gateway_currency_exchange_rate = get_option('wcj_gateways_currency_exchange_rate_' . $current_gateway);
                if ('' != $gateway_currency_exchange_rate) {
                    $gateway_currency_exchange_rate = str_replace(',', '.', $gateway_currency_exchange_rate);
                    $raw_price = $raw_price * $gateway_currency_exchange_rate;
                }
            }
            return $raw_price;
        }
        
        public function alter_tax_price($tax_amount, $item, $cart) {
            
            $info['product_id'] = $item->object['product_id'];
            $info['variation_id'] = $item->object['variation_id'];
            $info['quantity'] = $item->object['quantity'];
            if(isset($item->object['line_total'])){
                $info['line_total'] = $item->object['line_total'];
                $info['line_subtotal'] = $item->object['line_subtotal'];
            }else{
                $info['line_total'] = $item->price;
            }
            
            $applied_coupons = WC()->cart->get_applied_coupons();
            
            if(count($applied_coupons) && $info['line_subtotal'] != $info['line_total']){
                $info['total_coupon'] = true;
                $info['coupon'] = (($info['line_subtotal'] - $info['line_total'])/$info['line_subtotal']);
                $info['line_total'] = $info['line_subtotal'];
            }
            
            $info['custom_tax'] = $this->is_custom_tax($info['product_id'], $info['variation_id']);
            $info['cent'] = 100;
            
            $taxes = $this->custom_calc_tax($item->total, $item->tax_rates, $item->price_includes_tax, $info);
            
            return $taxes;
        }
        
        private function custom_calc_tax($price, $rates, $price_includes_tax, $info) {
            if ( $price_includes_tax ) {
                $taxes = $this->custom_calc_inclusive_tax($price, $rates, $info);
            } else {
                $taxes = $this->custom_calc_exclusive_tax($price, $rates, $info);
            }
            
            return $taxes;
        }
        
        private function custom_calc_inclusive_tax($price, $rates, $info) {
            $taxes          = array();
            $compound_rates = array();
            $regular_rates  = array();
            $vat_rates      = array();

		// Index array so taxes are output in correct order and see what compound/regular rates we have to calculate.
		foreach ( $rates as $key => $rate ) {
			$taxes[ $key ] = 0;

			if ( 'yes' === $rate['compound'] ) {
				$compound_rates[ $key ] = $rate['rate'];
			} else {
				$regular_rates[ $key ] = $rate['rate'];
                                if($rate['label'] == 'VAT' && $info['custom_tax']){
                                    $vat_rates[ $key ] = $rate['rate'];
                                }
			}
		}

		$compound_rates = array_reverse( $compound_rates, true ); // Working backwards.

		$non_compound_price = $price;

		foreach ( $compound_rates as $key => $compound_rate ) {
			$tax_amount         = apply_filters( 'woocommerce_price_inc_tax_amount', $non_compound_price - ( $non_compound_price / ( 1 + ( $compound_rate / 100 ) ) ), $key, $rates[ $key ], $price );
			$taxes[ $key ]     += $tax_amount;
			$non_compound_price = $non_compound_price - $tax_amount;
		}

		// Regular taxes.
		$regular_tax_rate = 1 + ( array_sum( $regular_rates ) / 100 );

		foreach ( $regular_rates as $key => $regular_rate ) {
			$the_rate       = ( $regular_rate / 100 ) / $regular_tax_rate;
			$net_price      = $price - ( $the_rate * $non_compound_price );
			$tax_amount     = apply_filters( 'woocommerce_price_inc_tax_amount', $price - $net_price, $key, $rates[ $key ], $price );
                        if(isset($vat_rates[$key])){
                            $api = new PRIMERA_Profit_Tax_API($info['product_id'], $info['variation_id'], $info['quantity'], $info['line_total']);
                            $tax_amount = $api->get_tax()*100;
                        }
			$taxes[ $key ] += $tax_amount;
		}

		/**
		 * Round all taxes to precision (4DP) before passing them back. Note, this is not the same rounding
		 * as in the cart calculation class which, depending on settings, will round to 2DP when calculating
		 * final totals. Also unlike that class, this rounds .5 up for all cases.
		 */
		$taxes = array_map( array( __CLASS__, 'round' ), $taxes );

		return $taxes;
	}

	
	    private function custom_calc_exclusive_tax($price, $rates, $info) {
		$taxes = array();

		if ( ! empty( $rates ) ) {
			foreach ( $rates as $key => $rate ) {
				if ( 'yes' === $rate['compound'] ) {
					continue;
				}
                                
                                if($rate['label'] == 'VAT' && $info['custom_tax']){
                                    $is_coupon = isset($info['coupon']) ? $info['coupon'] : false;
                                    $is_total_coupon = isset($info['total_coupon']) ? true : false;
                                    $api = new PRIMERA_Profit_Tax_API($info['product_id'], $info['variation_id'], $info['quantity'], $info['line_total'], $is_coupon, $is_total_coupon);
                                    $tax_amount = $api->get_tax()*$info['cent'];
                                }else{
                                    $tax_amount = $price * ( $rate['rate'] / 100 );
                                }
				$tax_amount = apply_filters( 'woocommerce_price_ex_tax_amount', $tax_amount, $key, $rate, $price ); // ADVANCED: Allow third parties to modify this rate.

				if ( ! isset( $taxes[ $key ] ) ) {
					$taxes[ $key ] = $tax_amount;
				} else {
					$taxes[ $key ] += $tax_amount;
				}
			}

			$pre_compound_total = array_sum( $taxes );

			// Compound taxes.
			foreach ( $rates as $key => $rate ) {
				if ( 'no' === $rate['compound'] ) {
					continue;
				}
				$the_price_inc_tax = $price + ( $pre_compound_total );
				$tax_amount        = $the_price_inc_tax * ( $rate['rate'] / 100 );
				$tax_amount        = apply_filters( 'woocommerce_price_ex_tax_amount', $tax_amount, $key, $rate, $price, $the_price_inc_tax, $pre_compound_total ); // ADVANCED: Allow third parties to modify this rate.

				if ( ! isset( $taxes[ $key ] ) ) {
					$taxes[ $key ] = $tax_amount;
				} else {
					$taxes[ $key ] += $tax_amount;
				}

				$pre_compound_total = array_sum( $taxes );
			}
		}

		/**
		 * Round all taxes to precision (4DP) before passing them back. Note, this is not the same rounding
		 * as in the cart calculation class which, depending on settings, will round to 2DP when calculating
		 * final totals. Also unlike that class, this rounds .5 up for all cases.
		 */
		$taxes = array_map( array( __CLASS__, 'round' ), $taxes );

		return $taxes;
	}
        
        public function round( $in ) {
            return apply_filters( 'woocommerce_tax_round', round( $in, wc_get_rounding_precision() ), $in );
        }
        
        private function is_custom_tax($product_id, $variable_id) {
            if ($variable_id > 0) {
                $raw_price = get_post_meta($variable_id, '_cwg_profit_tax_raw_price', true);
            } else {
                $raw_price = get_post_meta($product_id, '_cwg_profit_tax_raw_price', true);
            }
            
            return $raw_price ? true : false;
        }
        
        public function round_line_tax( $value, $in_cents = true ) {
            if ( ! 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
                $value = wc_round_tax_total( $value, $in_cents ? 0 : null );
            }
            return $value;
        }
        
        private function update_coupon_taxes($cart) {
            $calculate_tax = wc_tax_enabled() && ! $cart->get_customer()->get_is_vat_exempt();
            $coupons = $cart->get_coupons();
            $items = array();
            foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
                    $item                          = $this->get_default_item_props();
                    $item->key                     = $cart_item_key;
                    $item->object                  = $cart_item;
                    $item->tax_class               = $cart_item['data']->get_tax_class();
                    $item->taxable                 = 'taxable' === $cart_item['data']->get_tax_status();
                    $item->price_includes_tax      = wc_prices_include_tax();
                    $item->quantity                = $cart_item['quantity'];
                    $item->price                   = wc_add_number_precision_deep( $cart_item['data']->get_price() * $cart_item['quantity'] );
                    $item->product                 = $cart_item['data'];
                    $item->tax_rates               = $this->get_item_tax_rates( $item, $cart->get_customer() );
                    $items[ $cart_item_key ] = $item;
            }

            foreach ( $coupons as $coupon ) {
                switch ( $coupon->get_discount_type() ) {
                    case 'fixed_product':
                            $coupon->sort = 1;
                            break;
                    case 'percent':
                            $coupon->sort = 2;
                            break;
                    case 'fixed_cart':
                            $coupon->sort = 3;
                            break;
                    default:
                            $coupon->sort = 0;
                            break;
                }
            }

            uasort( $coupons, array( $this, 'sort_coupons_callback' ) );
            
            $discounts = new WC_Discounts( $cart );

            // Set items directly so the discounts class can see any tax adjustments made thus far using subtotals.
            $discounts->set_items( $items );

            foreach ( $coupons as $coupon ) {
                $discounts->apply_coupon( $coupon );
            }

            $coupon_discount_amounts     = $discounts->get_discounts_by_coupon( true );
            $coupon_discount_tax_amounts = array();

		// See how much tax was 'discounted' per item and per coupon.
		if ($calculate_tax) {
                    foreach ( $discounts->get_discounts( true ) as $coupon_code => $coupon_discounts ) {
                        $coupon_discount_tax_amounts[ $coupon_code ] = 0;

                        foreach ( $coupon_discounts as $item_key => $coupon_discount ) {
                            $item = $items[ $item_key ];

                            if ( $item->product->is_taxable() ) {
                                // Item subtotals were sent, so set 3rd param.
                                //$item_tax = wc_round_tax_total( array_sum( WC_Tax::calc_tax( $coupon_discount, $item->tax_rates, $item->price_includes_tax ) ), 0 );
                                $info['product_id'] = $item->object['product_id'];
                                $info['variation_id'] = $item->object['variation_id'];
                                $info['quantity'] = $item->object['quantity'];
                                $info['line_total'] = $item->object['line_total'] + ($coupon_discount/100);

                                $info['custom_tax'] = $this->is_custom_tax($info['product_id'], $info['variation_id']);

                                $info['cent'] = 100;
                                $info['coupon'] = (($coupon_discount/100)/$info['line_total']);
                                $item_tax = wc_round_tax_total( array_sum( $this->custom_calc_tax($coupon_discount, $item->tax_rates, $item->price_includes_tax, $info)));

                                // Sum total tax.
                                $coupon_discount_tax_amounts[ $coupon_code ] += $item_tax;

                                // Remove tax from discount total.
                                if ( $item->price_includes_tax ) {
                                    $coupon_discount_amounts[ $coupon_code ] -= $item_tax;
                                }
                            }
                        }
                    }
		}

		$cart->set_coupon_discount_totals( wc_remove_number_precision_deep( $coupon_discount_amounts ) );
		$cart->set_coupon_discount_tax_totals( wc_remove_number_precision_deep( $coupon_discount_tax_amounts ) );
                
		// Add totals to cart object. Note: Discount total for cart is excl tax.
		$cart->set_discount_total( array_sum( (array) $discounts->get_discounts_by_item( false ) ) );
		$cart->set_discount_tax( array_sum( $coupon_discount_tax_amounts ) );
        }
        
        protected function sort_coupons_callback( $a, $b ) {
            if ( $a->sort === $b->sort ) {
                if ( $a->get_limit_usage_to_x_items() === $b->get_limit_usage_to_x_items() ) {
                    if ( $a->get_amount() === $b->get_amount() ) {
                        return $b->get_id() - $a->get_id();
                    }
                    return ( $a->get_amount() < $b->get_amount() ) ? -1 : 1;
                }
                return ( $a->get_limit_usage_to_x_items() < $b->get_limit_usage_to_x_items() ) ? -1 : 1;
            }
            return ( $a->sort < $b->sort ) ? -1 : 1;
        }
        
        protected function get_default_item_props() {
            return (object) array(
                'object'             => null,
                'tax_class'          => '',
                'taxable'            => false,
                'quantity'           => 0,
                'product'            => false,
                'price_includes_tax' => false,
                'subtotal'           => 0,
                'subtotal_tax'       => 0,
                'subtotal_taxes'     => array(),
                'total'              => 0,
                'total_tax'          => 0,
                'taxes'              => array(),
            );
        }
        
        protected function get_item_tax_rates( $item, $customer ) {
                if ( ! wc_tax_enabled() ) {
                        return array();
                }

                $tax_class      = $item->product->get_tax_class();
                $item_tax_rates = WC_Tax::get_rates( $tax_class, $customer );

                return $item_tax_rates;
        }

        

    }


    new PRIMERA_Profit_Tax_Functionality();
}
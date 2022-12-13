<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Primera_Coupons_Max_Val
 *
 * Set the Max value of orders applied coupons
 *
 * @package		PRIMERA
 */
if (!class_exists('Primera_Coupons_Max_Val')) {

    class Primera_Coupons_Max_Val {
        /**
         *
         * @var instance object
         */
        protected static $_instance = null;

        /**
         * @see Primera_Coupons_Max_Val()
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
            $this->include_files();
            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

            // add_action( 'woocommerce_calculate_totals', array( $this, 'coupon_discount_max_switch'), 10, 1);
            add_action( 'woocommerce_before_calculate_totals', array( $this, 'coupon_discount_max_switch'), 10, 1);
            add_filter( 'woocommerce_cart_totals_coupon_label', array($this, 'change_coupon_label'), 10, 2);
            add_action( 'woocommerce_order_status_changed', array( $this, 'remove_coupon' ), 10, 3 );
            add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
            add_action( 'woocommerce_settings_tabs_primera_woo', array( $this, 'settings_tab') );
            add_action( 'woocommerce_update_options_primera_woo', array( $this, 'update_settings' ) );
           // add_action( 'woocommerce_removed_coupon', array($this, 'remove_coupon_cart'),10,1 );
            add_filter( 'woocommerce_coupon_message', array($this,'filter_woocommerce_coupon_message'), 10, 3 );

        }

        public function include_files() {}
     
        function filter_woocommerce_coupon_message( $msg, $msg_code, $coupon ) {

            $applied_coupons = WC()->cart->get_applied_coupons(); // Get applied coupons
            // Set HERE the limit amount  <===  <===  <===  <===  <===  <===  <===  <===  <===  <===
            $max_discount  = get_option( 'primera_max_discount', true );
            $cart_total = WC()->cart->total;
            $coupon_amount = [];
            foreach(  $applied_coupons as $code ){

                // var_dump($coupon->get_discount_type());
                // Get the WC_Coupon object
                $coupon = new WC_Coupon($code);
                $discount_type = $coupon->get_discount_type(); // Get coupon discount type
                if( 'percent' === $discount_type ) {
                    $coupon_amount[] = ( $coupon->get_amount() * $cart_total ) / 100 ;  // Get coupon amount
                }
                if( 'fixed_cart' === $discount_type ){
                    $coupon_amount[] = $coupon->get_amount(); // Get coupon amount
                }
                
            }
       
            $total_discount = array_sum( $coupon_amount );
            
            if( $total_discount > (int) $max_discount ){
            
                if( $msg === __( 'Coupon code applied successfully.', 'woocommerce' ) ) {
                    // $remove_message = get_option( 'primera_cpoupon_remove', true );
                    // if( !empty ( $remove_message ) && is_string($remove_message) ){
                    //     $placeholders = array(
                    //         '[code]' => $coupon->get_code(),
                    //     );
                        
                    //     foreach ( $placeholders as $placeholder => $placeholder_value ) {
                    //         $message = str_replace( $placeholder , $placeholder_value , $remove_message );
                    //     }
                        
                    //     if( !wc_has_notice( $message, $notice_type = 'error', $data = array() ) ){
                    //         $msg = wc_add_notice( $message, $notice_type = 'error', $data = array() );
                    //     }
                        
                    // }else {
                    //     $msg = sprintf( 
                    //         __( "The %s Coupon code has Been Removed Try Another One", "woocommerce" ), 
                    //         '<strong>' . $coupon->get_code() . '</strong>' 
                    //     );
                    //     $msg = wc_add_notice( $msg, $notice_type = 'error', $data = array() );
                    // }
                    $msg = "";
                    
                }
            }
            return $msg;
        }

        public function coupon_discount_max_switch( $cart_obj ) {

            if ( is_admin() && ! defined( 'DOING_AJAX' ) ){
                return;
            }

            if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ){
                return;
            }
                
            if ( $cart_obj->get_applied_coupons()  == False ){
                return;
            }
                
            // Set HERE the limit amount  <===  <===  <===  <===  <===  <===  <===  <===  <===  <===
            $max_discount  = get_option( 'primera_max_discount', true );
            // $coupon_max_discount = get_option( 'primera_coupon_max_discount', true );
            $pri_coupon_max_discount_enable = get_option('pri_coupon_max_discount_enable', 'no');

            $prefix = 'pri-';
            $current_coupon = false;
            
            foreach ( $cart_obj->applied_coupons as $code ) {
                if( $cart_obj->has_discount( $code ) ) {
                    $current_coupon = true;
                }
            }
            
            $total_discount = array_sum( $cart_obj->coupon_discount_totals ); // Total cart discount

            // print_r($cart_obj);

            if( 'yes' === $pri_coupon_max_discount_enable ){
                // When 'coupon_max_discount' is set and the total discount is reached
                if( $current_coupon && $total_discount > (int) $max_discount ){

                   
                    
                    // Create Our custom coupon   <===  <===  <===  <===  <===
                    // $primera_code = $this->creat_coupon( $max_discount );
                    $pri_removed_coupons = [];
                    foreach ( $cart_obj->applied_coupons as $code ) {
                        if( $cart_obj->has_discount( $code ) || ( substr( $code, 0, 4 ) !== $prefix) ) {
                            // Remove Current coupon   <===  <===  <===  <===  <===
                                $cart_obj->remove_coupon( $code );
                                $pri_removed_coupons[] = $code;
                               
                        }
                    }

                    // Store Our Removed Coupons   <===  <===  <===  <===  <===
                    $user_id = get_current_user_id();
                    update_user_meta( $user_id, 'primera-remove-coupons', $pri_removed_coupons );
    
                    // <===  <===  <===  <===  <===
                    // Checking that the coupon_max_discount is not already set.<===  <===  <===  <===
                    // removed for bug .
                    // if( !$cart_obj->has_discount( $primera_code ) && (substr( $current_coupon, 0, 4 ) !== $prefix) ){
                    //     // prr( substr( $current_coupon, 0, 4 ) !== $prefix );
                        
                    //     // Add the 'coupon_max_discount' coupon <===  <===  <===  <===
                    //     $cart_obj->apply_coupon( $primera_code );
                    // }
                    // <===  <===  <===  <===  <===


                    // Displaying a custom message <===  <===  <===  <===
                    $max_discount_message = get_option( 'primera_cpoupon_max_discount_message', true );
                    if( !empty ( $max_discount_message ) ){
                        $placeholders = array(
                            '[max]' => $max_discount,
                        );
                        
                        foreach ( $placeholders as $placeholder => $placeholder_value ) {
                            $discount_message = str_replace( $placeholder , $placeholder_value , $max_discount_message );
                        }
                        
                        if( !wc_has_notice( $discount_message, $notice_type = 'error', $data = array() ) ){
                            wc_add_notice( $discount_message, $notice_type = 'error', $data = array() );
                        }
                        
                    }

                } 
            }

            // return $cart_obj;
        }  

        /**
         * mybe_creat_coupon
         *
         * @param  mixed $data
         * @return void
         */
        public function creat_coupon( $max_discount ){

            if( (int) $max_discount <= 0 ) {
                return;
            }
            // Generate unique coupon code
            $length = 8;
            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $count = strlen($charset);
            $random_coupon = '';
            while ($length--) {
                $random_coupon .= $charset[mt_rand(0, $count - 1)];
            }
            $random_coupon = 'pri-'.implode('-', str_split(strtoupper($random_coupon), 4));
            $coupon = new WC_Coupon();
            $coupon->set_code( $random_coupon ); // Coupon code
            $coupon->set_amount( (int) $max_discount ); // Discount amount
            $coupon->set_usage_limit_per_user(1);
            $coupon->save();
            update_post_meta( $coupon->get_id(), 'primera-disc-coupon', '1' );

            return $coupon->get_code();
            
        }

        /**
         * Change coupon label in cart and checkout page
         * @param string $label
         * @param Object $coupon
         * @return string
         */
        public function change_coupon_label($label, $coupon) {
            $coupon_id = $coupon->get_id();
            $pri_coupon_max_discount_enable = get_option('pri_coupon_max_discount_enable', 'no');
            $msg = get_option( 'primera_max_discount_message', 'By Primera : ' );
            $is_primera_coupon = get_post_meta( $coupon_id, 'primera-disc-coupon', true );
            if ( 'yes' === $pri_coupon_max_discount_enable && '1' === $is_primera_coupon ) {
                // $label = sprintf(esc_html__('Cashback: %s', 'primera'), $coupon->get_code());
                $label = sprintf(esc_html__('%s', 'primera'), $msg);

            }
            return $label;
        }
        
        /**
         * remove_coupon
         *
         * @param  mixed $order_id
         * @param  mixed $old_status
         * @param  mixed $new_status
         * @return void
         */
        public function remove_coupon( $order_id, $old_status, $new_status ){

            if( $new_status === "completed" ) {
                // Get an instance of WC_Order object
                $order = wc_get_order( $order_id );
                foreach( $order->get_coupon_codes() as $coupon_code ) {
                    // Get the WC_Coupon object
                    $coupon    = new WC_Coupon( $coupon_code );
                    $coupon_id = $coupon->get_id(); // Get coupon id
                    $is_primera_coupon = get_post_meta( $coupon_id, 'primera-disc-coupon', true );
                    if( '1' === $is_primera_coupon ){
                        // prr((int)$coupon_id); wp_die();
                        wp_delete_post( (int)$coupon_id, true );
                    }
 
                }
            } 
        }
        
        /**
         * remove_coupon_cart
         *
         * @return delete/coupon_code
         */
        public function remove_coupon_cart( $coupon_code ){
                $coupon_id = wc_get_coupon_id_by_code( $coupon_code );
                if( $coupon_id > 0  ) {
                    $is_primera_coupon = get_post_meta( $coupon_id, 'primera-disc-coupon', true );
                    if( '1' === $is_primera_coupon ){
                        // prr((int)$coupon_id); wp_die();
                        wp_delete_post( (int) $coupon_id, true );
                    }
                }
        }
                
        /**
         * add_settings_tab
         *
         * @param  mixed $settings_tabs
         * @return void
         */
        public static function add_settings_tab( $settings_tabs ) {
            $settings_tabs['primera_woo'] = __( 'Primera', 'primera' );
            return $settings_tabs;
        }
        
        /**
         * settings_tab
         *
         * @return void
         */
        public function settings_tab() {
            woocommerce_admin_fields( $this->get_settings() );
        }
        
        /**
         * get_settings
         *
         * @return void
         */
        public function get_settings() {
            $settings = array(
                'section_title' => array(
                    'name'     => __( 'Settings Primera Coupons', 'primera' ),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'wc_settings_tab_demo_section_title'
                ),
                'enable_global_discount_coupon' => array(
                    'name' => __( 'Max Discount Amount', 'primera' ),
                    'type' => 'checkbox',
                    'desc' => __( 'Enable Max Discount Amount For All Site Coupons', 'primera' ),
                    'id'   => 'pri_coupon_max_discount_enable',
                    'custom_attributes' => 'readonly',
                ),
                
                'title' => array(
                    'name' => __( 'Coupons Max Discount Amount', 'primera' ),
                    'type' => 'number',
                    'desc' => __( 'Add Max Discount Amount For All Site Coupons', 'primera' ),
                    'id'   => 'primera_max_discount'
                ),
                'description' => array(
                    'name' => __( 'Coupons Max Discount Amount Info', 'primera' ),
                    'type' => 'text',
                    'desc' => __( 'Replace Coupons code With Custom Text', 'primera' ),
                    'id'   => 'primera_max_discount_message'
                ),
                'coupon_notes' => array(
                    'name' => __( 'Coupons Max Discount Amount Massege', 'primera' ),
                    'type' => 'text',
                    'desc' => __( 'Replace Coupons code With Custom Text Placeholder for max number is [max]', 'primera' ),
                    'id'   => 'primera_cpoupon_max_discount_message'
                ),
                // 'coupon_remove_notes' => array(
                //     'name' => __( 'Coupons Remove Max Discount Amount Massege', 'primera' ),
                //     'type' => 'text',
                //     'desc' => __( 'The [code] Coupon code has Been Removed Try Another One', 'primera' ),
                //     'id'   => 'primera_cpoupon_remove'
                // ),
                'section_end' => array(
                     'type' => 'sectionend',
                     'id' => 'wc_settings_tab_demo_section_end'
                )
            );
            return apply_filters( 'wc_primera_woo_settings', $settings );
        }
        
        /**
         * update_settings
         *
         * @return void
         */
        function update_settings() {
            woocommerce_update_options( $this->get_settings() );
            // $this->mybe_creat_coupon($_POST);
        }
        
        public function load_plugin_textdomain() {
            $domain = 'primera';
            $dir = untrailingslashit(WP_LANG_DIR);
            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            if ($exists = load_textdomain($domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo')) {
                return $exists;
            } else {
                load_plugin_textdomain($domain, FALSE, basename(dirname(__FILE__)) . '/languages/');
            }
        }

    }

    Primera_Coupons_Max_Val::instance();
}
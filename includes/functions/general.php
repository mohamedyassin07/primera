<?php 

remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
add_action('woocommerce_checkout_order_review_payment', 'woocommerce_checkout_payment', 20);

/* Custom Login */
function alk_login_logo() { ?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
	        background-image: url(https://primera.app/wp-content/uploads/logo-new.png);
			height:86px;
			width:268px;
			background-size: 268px 86px;
			background-repeat: no-repeat;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'alk_login_logo' );


function alk_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'alk_login_logo_url' );


function alk_login_logo_url_title() {
    return 'primera.app';
}
add_filter( 'login_headertext', 'alk_login_logo_url_title' );


// Mine Filter
function alk_filter_edit_shop_order_views( $views ) { 
    // Unset the Mine option. 
    unset( $views['mine'] );
    return $views; 
}; 
add_filter( 'views_edit-shop_order', 'alk_filter_edit_shop_order_views' ); 

/* Add to cart button above desc */
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 15);


/* Remove WordPress logo and menu from admin bar */
function wp_debranding_remove_wp_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
}
add_action( 'wp_before_admin_bar_render', 'wp_debranding_remove_wp_logo');

/* Remove WordPress name from adming interface footer */
function change_admin_footer_text() {
    return '';
}
add_filter('admin_footer_text', 'change_admin_footer_text');


/* Remove My Account Tabs */
add_filter( 'woocommerce_account_menu_items', 'alk_remove_address_my_account', 999 );
 
function alk_remove_address_my_account( $items ) {
    unset($items['edit-address']);
    unset($items['wt-smart-coupon']);
    unset($items['wt-store-credit']);
    unset($items['customer-logout']);
    return $items;
}

// Add address to account details
add_action( 'woocommerce_account_edit-account_endpoint', 'woocommerce_account_edit_address' );

/**
 * Exclude products from a particular category on the shop page
 */
function custom_pre_get_posts_query( $q ) {

    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'point-only' ), // Don't display products in the clothing category on the shop page.
           'operator' => 'NOT IN'
    );


    $q->set( 'tax_query', $tax_query );

}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );  


/**
 * Always check the WC Terms & Conditions checkbox
 *
 * @since 1.0
 * 
 * @return bool
 */
add_filter('woocommerce_terms_is_checked_default', '__return_true');

// Wallet
function woo_mini_wallet_callback() {
    if (!function_exists('woo_wallet') || !is_user_logged_in()) {
        return '';
    }
    ob_start();
    $mini_wallet = woo_wallet()->wallet->get_wallet_balance(get_current_user_id());
    echo $mini_wallet;
    return ob_get_clean();
}
add_shortcode('woo-mini-wallet', 'woo_mini_wallet_callback');

// add_filter('woocommerce_available_payment_gateways', 'woocommerce_available_payment_gateways_callback');
// if(!function_exists('woocommerce_available_payment_gateways_callback')){
//     function woocommerce_available_payment_gateways_callback($_available_gateways){
//         if( is_enable_wallet_partial_payment()){
//             unset($_available_gateways[ 'ppec_paypal' ]);
//             unset($_available_gateways[ 'tap' ]);
//             unset($_available_gateways[ 'mycred' ]);
//             unset($_available_gateways[ 'halalah' ]);
//         }
//         return $_available_gateways;
//     }
// }

// Disable partial payments
add_filter('woo_wallet_disable_partial_payment', '__return_true');

// NP Password
function alk_min_password_strength( $strength ) {
    return 1;
}
 
add_filter( 'woocommerce_min_password_strength', 'alk_min_password_strength', 10, 1 );

add_action( 'wp_enqueue_scripts', 'alk_strength_meter_localize_script' );
function alk_strength_meter_localize_script() {
    wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
        'empty'    => __( 'فضلًا اكتب كلمة مرورك', 'alkhdmah' ),
        'short'    => __( 'كلمة مرور قصيرة', 'alkhdmah' ),
        'bad'      => __( 'مقبولة ولكن ضعيفة', 'alkhdmah' ),
        'good'     => __( 'جيدة', 'alkhdmah' ),
        'strong'   => __( 'كلمة مرور قوية!', 'alkhdmah' ),
        'mismatch' => __( 'كلمات المرور لا تتطابق', 'alkhdmah' )
    ) );
}


/**
 * Changes the MAXup url if guest
 *
 * @return string
 */
function alk_maxup_redirect() {
    if ( ! is_user_logged_in() && is_page('maxup') ) {
        wp_redirect( site_url( '/maxup-overview' ) );
        exit();
    }
}
add_action( 'template_redirect', 'alk_maxup_redirect' );


// Add specific CSS class by filter
function alk_class_names($classes) {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if(strchr($useragent,'rn-alkhdmah')) $classes[] = 'gonative';
    return $classes;
}
add_filter('body_class','alk_class_names');

// Remove cancel button
add_filter('woocommerce_my_account_my_orders_actions', 'alk_remove_my_cancel_button', 10, 2);
function alk_remove_my_cancel_button($actions, $order){
    unset($actions['cancel']);
    unset($actions['pay']);
    return $actions;
}

// Hide admin bar
add_filter('show_admin_bar', '__return_false');

// My Account
add_filter( 'woocommerce_my_account_my_orders_columns', 'additional_my_account_orders_column', 10, 1 );
function additional_my_account_orders_column( $columns ) {
    $new_columns = [];
    unset( $columns['order-total'] );
    unset( $columns['order-date'] );

    foreach ( $columns as $key => $name ) {
        $new_columns[ $key ] = $name;

        if ( 'order-status' === $key ) {
            $new_columns['order-items'] = __( 'Product', 'woocommerce' );
        }
    }
    return $new_columns;
}

add_action( 'woocommerce_my_account_my_orders_column_order-items', 'additional_my_account_orders_column_content', 10, 1 );
function additional_my_account_orders_column_content( $order ) {
    $details = array();

    foreach( $order->get_items() as $item )
        $details[] = $item->get_name() . '&nbsp;&times;&nbsp;' . $item->get_quantity();

    echo count( $details ) > 0 ? implode( '<br>', $details ) : '&ndash;';
}

/**
 * @snippet       Remove Additional Information Tab @ WooCommerce Single Product Page
 */
 
add_filter( 'woocommerce_product_tabs', 'alk_remove_product_tabs', 98 );
 
function alk_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] ); 
    return $tabs;
}

// Change mref
add_filter( 'mycred_affiliate_key', 'mycredpro_adjust_affiliate_key' );
function mycredpro_adjust_affiliate_key( $key ) {

    return 'ref';

}

// Disable wallet topup
function cwg_woo_wallet_disable_topup($bool) {
    $user = wp_get_current_user();
    if (in_array('customer', (array) $user->roles)) {
        $bool = false;
    }
    return $bool;
}

add_filter('woo_wallet_is_enable_top_up', 'cwg_woo_wallet_disable_topup');

// Clear billing for Cashier
// function cwg_reset_checkout_values($input) {
//   $user = wp_get_current_user();
//   if ( in_array( 'cashier', (array) $user->roles ) ) {
//     return '';
//   }
//   return $input;
// }
// add_filter( 'woocommerce_checkout_get_value' , 'cwg_reset_checkout_values' );

// Remove for cashier
// add_filter('woocommerce_billing_fields','wpb_custom_billing_fields');
// function wpb_custom_billing_fields( $fields = array() ) {

//     $user = wp_get_current_user();
//     if ( in_array( 'cashier', (array) $user->roles ) ) {
//         unset($fields['billing_address_1']);
//         unset($fields['billing_address_2']);
//         unset($fields['billing_state']);
//         unset($fields['billing_city']);
//         unset($fields['billing_postcode']);
//         unset($fields['billing_country']);
//         $fields['billing_phone']['required'] = false;
//         $fields['billing_email']['required'] = false;
//         $fields['billing_first_name']['required'] = false;
//         $fields['billing_last_name']['required'] = false;
//     }
//     return $fields;
// }

// Make Saudi 1st
add_filter('woocommerce_sort_countries', '__return_false');
add_filter( 'woocommerce_countries', 'alk_change_country_order_in_checkout_form' );
function alk_change_country_order_in_checkout_form($countries)
{
    $usa = $countries['SA']; // Store the data for "US" key
    unset($countries["SA"]); // Remove "US" entry from the array

    // Return "US" first in the countries array
    return array('SA' => $usa ) + $countries;
}

// Disable digits page
// add_action('init', function () {
//     remove_action('init', 'digits_login', 100);
// });

// Remove post actions
remove_filter( 'post_row_actions', 'rocket_post_row_actions', 10, 2 );
remove_filter( 'page_row_actions', 'rocket_post_row_actions', 10, 2 );
remove_filter( 'user_row_actions', 'rocket_user_row_actions', 10, 2 );
remove_filter( 'tag_row_actions', 'rocket_user_row_actions', 10, 2 );

remove_filter( 'post_row_actions', 'perfmatters_script_manager_row_actions', 10, 2 );
remove_filter( 'page_row_actions', 'perfmatters_script_manager_row_actions', 10, 2 );

// Product Custom JS
add_action( 'wp_footer', 'alk_product_custom_js' );
 
function alk_product_custom_js() { 
   if ( ! is_product() ) return;
   ?>
   <script>
    jQuery(document).ready(function () {
        jQuery('ul.variable-items-wrapper > li').find('img').attr('alt', function () {
            var alt = jQuery(this).attr('alt');
            jQuery(this).before(alt);
        });
    });
    </script>
   <?php
}


// Reply To
add_filter( 'woocommerce_email_headers', 'alk_change_reply_to_email_address', 10, 3 );
function alk_change_reply_to_email_address( $header, $email_id, $order ) {

    // HERE below set the name and the email address
    $reply_to_name  = 'Primera Support';
    $reply_to_email = 'support@primera.app';

    // Get the WC_Email instance Object
    $email = new WC_Email($email_id);

    $header  = "Content-Type: " . $email->get_content_type() . "\r\n";
    $header .= 'Reply-to: ' . $reply_to_name . ' <' . $reply_to_email . ">\r\n";

    return $header;
}

/**
 * AutomateWoo Reply To
 */
add_filter( 'automatewoo/workflow/mailer', 'alk_my_filter_automatewoo_workflow_mailer' );

/**
 * @param AutomateWoo\Mailer $mailer
 * @return AutomateWoo\Mailer
 */
function alk_my_filter_automatewoo_workflow_mailer( $mailer ) {
	$mailer->reply_to = 'Primera Support <support@primera.app>';
	return $mailer;
}

/**
 * Woo Point Rewards by Order Total
 * Reward store purchases by paying a percentage of the order total
 * as points to the buyer.
 * @version 1.2
 */
function mycred_pro_reward_order_percentage($order_id) {

    if (!function_exists('mycred'))
        return;

    // Get Order
    $order = wc_get_order($order_id);
    $cost = $order->get_subtotal();

    // Do not payout if order was paid using points
    if ($order->get_payment_method() == 'mycred')
        return;

    $order_user_id = $order->get_user_id();

    // The percentage to payout
    $percent = 5;
    //get the role of corresponding order user 
    $get_user = get_userdata($order_user_id);
    if ($get_user) {
        $get_user_roles = $get_user->roles;
        if (in_array("customer", $get_user_roles)) {
            $percent = 10;
        } else {
            $percent = 5;
        }
    }

    // Load myCRED
    $mycred = mycred();

    // Make sure user only gets points once per order
    if ($mycred->has_entry('reward', $order_id, $order_user_id))
        return;

    // Reward example 25% in points.
    $reward = $cost * ( $percent / 100 );

    // Add reward
    $mycred->add_creds(
            'reward', $order_user_id, $reward, 'Reward for store purchase', $order_id, array('ref_type' => 'post')
    );
}

add_action('woocommerce_order_status_completed', 'mycred_pro_reward_order_percentage');

// Limit woocommerce order search fields
function alk_woocommerce_shop_order_search_fields( $search_fields ) {
    unset( $search_fields );
    return $search_fields;
}
add_filter( 'woocommerce_shop_order_search_fields', 'alk_woocommerce_shop_order_search_fields' );


// Webhooks hotfix for Metorik
add_filter( 'get_user_metadata', 'mtkdocs_filter_user_metadata', 10, 4 );
function mtkdocs_filter_user_metadata( $value, $object_id, $meta_key, $single ) {
    // Check if it's one of the keys we want to filter
    if ( in_array( $meta_key, array( '_money_spent', '_order_count' ) ) ) {
        // Return 0 so WC doesn't try calculate it
        return 0;
    }
    // Default
    return $value;
}

// Remove order again
//remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );

// Remove AS logs
add_filter( 'action_scheduler_retention_period', function() { return DAY_IN_SECONDS * 1; } );

// Remove extra scripts
add_action( 'wp_print_scripts', 'mtk_helper_dequeue_scripts' );
function mtk_helper_dequeue_scripts() {
    wp_dequeue_script('sourcebuster');
    wp_dequeue_script('metorik-js');
    wp_dequeue_script('wc-endpoint-wallet');
}


/**
 * @snippet       WooCommerce Remove Product Permalink @ Order Table
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=20455
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.5
 */
 
add_filter( 'woocommerce_order_item_permalink', '__return_false' );

// Single variation
add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );
add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

// Remove my account fields
add_filter( 'woocommerce_billing_fields', 'alk_remove_account_billing_phone_and_email_fields', 20, 1 );
function alk_remove_account_billing_phone_and_email_fields( $billing_fields ) {
    // Only on my account 'edit-address'
    if( is_wc_endpoint_url( 'edit-address' ) ){
        unset($billing_fields['billing_address_1']);
        unset($billing_fields['billing_address_2']);
        unset($billing_fields['billing_state']);
        unset($billing_fields['billing_city']);
        unset($billing_fields['billing_postcode']);
    }
    return $billing_fields;
}

add_filter( 'nm_banner_slider_slick_options', function( $slider_settings ) {
    //if ('ar' == weglot_get_current_language()){
        $slider_settings['rtl'] = 'true';
    //}
    return $slider_settings;
} );


// Make CKO delay 0 for orders < 500
// add_filter('option_ckocom_card_cap_delay', 'ckocom_card_cap_delay_callback');
// function ckocom_card_cap_delay_callback($value){
//     if(!is_admin() && wc()->cart->get_total('edit') <= 180 ){
//         $value = 10 / 3600;
//     }
//     return $value;
// }

/**
 * @snippet       Always Show Variation Price @ WooCommerce Single Product
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.8
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_filter( 'woocommerce_show_variation_price', '__return_true' );

/** WpExperts customizations **/

// change wallet payment order to processing instead of completed
add_filter('pinkcrab_completed_order_status', 'pa_change_wallet_payment_method_completion_status', 20, 2);

function pa_change_wallet_payment_method_completion_status($status, $order){
    if($order->get_payment_method() == 'wallet'){
        return 'processing';
    }
    
    return $status;
}

// add meta info to order for future investigation purposes
add_action('woocommerce_checkout_order_processed', 'pa_add_oredr_meta_info', 1, 3);

function pa_add_oredr_meta_info($order_id, $posted_data, $order){
    update_post_meta($order_id, '_pa_logged_user', get_current_user_id());
    update_post_meta($order_id, '_pa_user_ip', $_SERVER['REMOTE_ADDR']);
}

// customize "sale" badge on variable product
add_action('wp_enqueue_scripts', 'pa_variable_product_customization', 20 );

function pa_variable_product_customization(){
    if(is_product()){
        global $post;
        $product = wc_get_product($post->ID);
        if($product->is_type('variable')){
            $final = array();
            $prices = $product->get_variation_prices();
            $variations = $product->get_available_variations();
            foreach($variations as $variation){
                $id = $variation['variation_id'];
                $regular = 0;
                $sale = 0;
                $is_sale = true;
                $currency_html = '';
                $currency_sale_html = '';
                if(isset($prices['regular_price'][$id])){
                    $regular = $prices['regular_price'][$id];
                }
                if(isset($prices['sale_price'][$id])){
                    $sale = $prices['sale_price'][$id];
                }
                if($regular == $sale){
                    $is_sale = false;
                    if (function_exists('wcj_price') && function_exists('wcj_get_current_currency_code')) {
                        $currency_html = wcj_price($regular, wcj_get_current_currency_code('multicurrency'), 'no');
                    }
                }else{
                    if (function_exists('wcj_price') && function_exists('wcj_get_current_currency_code')) {
                        $currency_html = wcj_price($regular, wcj_get_current_currency_code('multicurrency'), 'no');
                        $currency_sale_html = wcj_price($sale, wcj_get_current_currency_code('multicurrency'), 'no');
                    }
                }
                
                $final[$id] = array(
                    'price' => $regular,
                    'sale' => $sale,
                    'is_sale' => $is_sale,
                    'fee' => get_post_meta($id, 'product-fee-amount', true),
                    'currency_html' => $currency_html,
                    'currency_sale_html' => $currency_sale_html
                );
            }
            
            wp_enqueue_script('wpex-variable-product-js', PRIMERA_THEME_URL . '/assets/js/variable_product.js', array('jquery'), NM_THEME_VERSION_CHILD, true);
            wp_localize_script('wpex-variable-product-js', 'variables', array(
                'variations' => $final,
                'fee_message' => get_option('wcpf_fee_message', 'يطبق علي هذا المنتج رسوم خدمة')
            ));
        }
    }
}

// add general message setting
add_filter('wcpf_global_product_settings', 'pa_add_fee_message_field');

function pa_add_fee_message_field($settings){
    $new_settings = array();
    
    foreach($settings as $setting){
        if($setting['type'] == 'sectionend'){
            $new_settings[] = array(
                'title'    => __( 'Fee Message', 'woocommerce-product-fees' ),
                'desc'     => __( 'This message will be shown in single product page.', 'woocommerce-product-fees' ),
                'id'       => 'wcpf_fee_message',
                //'css'      => 'min-width:150px;',
                //'default'  => 'title',
                'type'     => 'text',
                //'class'    => 'wc-enhanced-select',
                //'options'  => $this->tax_classes(),
                'desc_tip' =>  true,
            );
        }
        
        $new_settings[] = $setting;
    }
    
    return $new_settings;
}

// add fee item data
/*add_filter('woocommerce_get_item_data', 'pa_add_fee_item_data', 20, 2);

function pa_add_fee_item_data($item_data, $cart_item){
    $product_id  = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
    $fee_name = get_post_meta($product_id, 'product-fee-name', true);
    $fee_amount = get_post_meta($product_id, 'product-fee-amount', true);
    if($fee_name && $fee_amount){
        $item_data[] = array(
        'key' => $fee_name,
        'value' => wc_price($fee_amount)
        );
    }
    
    return $item_data;
}*/

// add detailed item total
add_filter( 'woocommerce_cart_item_subtotal', 'pa_add_detailed_item_total', 20, 3);

function pa_add_detailed_item_total($subtotal, $cart_item, $cart_item_key){
    if(!is_checkout()){
        //echo '<pre>';print_r($cart_item);echo '</pre>';
        $new_subtotal = '';
        $fees = '';
        $fee = 0;
        $product_id  = isset($cart_item['variation_id']) && $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];
        $fee_name = get_post_meta($product_id, 'product-fee-name', true);
        $fee_amount = get_post_meta($product_id, 'product-fee-amount', true);
        if($fee_name && $fee_amount){
            $fees .= $fee_name;
            $fees .= str_replace('woocommerce-Price-amount', 'woocommerce-Price-amount pa-cart-item-info', wc_price(pa_get_current_currency_amount_value($fee_amount))).'<br/>';
            $fee = pa_get_current_currency_amount_value($fee_amount);
        }/*else{
            $fees .= __('رسوم خدمة', 'alkhdmah');
            $fees .= str_replace('woocommerce-Price-amount', 'woocommerce-Price-amount pa-cart-item-info', wc_price(0)).'<br/>';
        }*/
        
        $new_subtotal .= __('السعر', 'alkhdmah');
        $new_subtotal .= str_replace('woocommerce-Price-amount', 'woocommerce-Price-amount pa-cart-item-info', wc_price($cart_item['line_total'])).'<br/>';
        if($cart_item['line_subtotal_tax']){
        $new_subtotal .= __('الضريبة', 'alkhdmah');
        $new_subtotal .= str_replace('woocommerce-Price-amount', 'woocommerce-Price-amount pa-cart-item-info', wc_price($cart_item['line_subtotal_tax'])).'<br/>';
        }
        $new_subtotal .= $fees;
        $new_subtotal .= __('المجموع النهائي', 'alkhdmah');
        //$new_subtotal .= $subtotal;
        $new_subtotal .= str_replace('woocommerce-Price-amount', 'woocommerce-Price-amount pa-cart-item-info', wc_price($cart_item['line_total'] + $cart_item['line_subtotal_tax'] + $fee));

        return $new_subtotal;
    }
    
    return $subtotal;
}

// replace close icon with trash icon
add_filter( 'woocommerce_cart_item_remove_link', 'pa_add_class_to_remove_item', 20);

function pa_add_class_to_remove_item($icon){
    wp_enqueue_style( 'dashicons' );
    if(strpos($icon, 'nm-font nm-font-close2') !== false){
        //$icon = str_replace('remove', 'remove pa-cart-item-remove', $icon);
        $icon = str_replace('<i class="nm-font nm-font-close2"></i>', '', $icon);
    }
    
    $icon = str_replace('></a', '><span class="dashicons dashicons-trash"></span></a', $icon);
    
    return $icon;
}

// remove checkout card payment gateway
/*add_filter('woocommerce_payment_gateways', 'pa_remove_checkout_card_payment_gateway', 20);
function pa_remove_checkout_card_payment_gateway($payment_gateways) {
    $key = array_search('WC_Gateway_Checkout_Com_Cards', $payment_gateways);
    if(false !== $key){
        unset($payment_gateways[$key]);
    }

    return $payment_gateways;
}*/

// override raw price (used in tax calculation) to be compatibale with currency switcher
add_filter('alk_profit_tax_raw_price', 'pa_override_raw_price', 20, 3);

function pa_override_raw_price($raw_price, $variation_id, $product_id){
    if($raw_price){
        $raw_price = pa_get_current_currency_amount_value($raw_price);
    }
    
    return $raw_price;
}

// override fees amount to be compatibale with currency switcher
add_filter('wcpf_filter_fee_data', 'pa_override_fee_amount', 20, 2);

function pa_override_fee_amount($fee, $item_data){
    if($fee['amount']){
        $fee['amount'] = pa_get_current_currency_amount_value($fee['amount']);
    }
    
    return $fee;
}

add_filter('wcpf_filter_fee_amount', 'pa_override_fee_combine_amount', 20, 2);

function pa_override_fee_combine_amount($amount, $item_data){
    if($amount){
        $amount = pa_get_current_currency_amount_value($amount);
    }
    
    return $amount;
}

// General function to get current currency value for certain amount
function pa_get_current_currency_amount_value($amount){
    if(function_exists('wcj_get_currency_exchange_rate') && function_exists('wcj_get_current_currency_code')){
        $exchange_rate = wcj_get_currency_exchange_rate('multicurrency', wcj_get_current_currency_code('multicurrency'));

        return round(($amount * $exchange_rate), 2);
    }
    
    return $amount;
}

// add "remove the remaining stock quantity setting
add_filter('woocommerce_inventory_settings', 'pa_remove_remaining_stock_message_setting');

function pa_remove_remaining_stock_message_setting($settings){
    $new_settings = array();
    
    foreach($settings as $setting){
        if($setting['type'] == 'sectionend'){
            $new_settings[] = array(
                'name'    => __( 'Remove remaining stock quantity', 'woocommerce' ),
                'desc'    => __( 'Remove the "remaining stock quantity" portion of "not enough stock" message', 'woocommerce' ),
                'id'      => 'pa_remove_remaining_stock_quantity',
                'default' => 'yes', // WooCommerce >= 2.0
                'type'    => 'checkbox'
            );
        }
        
        $new_settings[] = $setting;
    }
    
    return $new_settings;
}

// add "remove the remaining stock quantity setting on product level
add_action('woocommerce_product_options_inventory_product_data', 'pa_remove_remaining_stock_message_product_setting');

function pa_remove_remaining_stock_message_product_setting(){
    global $post;
    $value = get_post_meta($post->ID, '_pa_remove_remaining_stock_quantity', true) ? get_post_meta($post->ID, '_pa_remove_remaining_stock_quantity', true) : 'global';
    ?>
    <div class="options_group">
        <?php
        woocommerce_wp_select(
            array(
                'id'            => '_pa_remove_remaining_stock_quantity',
                'value'         => $value,
                'wrapper_class' => 'pa_remove_remaining_stock_quantity_field',
                'label'         => __('Remove remaining stock quantity', 'woocommerce'),
                'options'       => array(
                    'global' => __('As Global Setting', 'woocommerce'),
                    'yes' => __('Yes', 'woocommerce'),
                    'no' => __('No', 'woocommerce'),
                ),
                'desc_tip'      => true,
                'description'   => __('Remove the "remaining stock quantity" portion of "not enough stock" message. Default is "As Global Setting".', 'woocommerce'),
            )
        );
        ?>
    </div>    
    <?php
}

// Save the custom fields
add_action( 'woocommerce_process_product_meta', 'pa_save_remove_remaining_stock_message_product_setting');

function pa_save_remove_remaining_stock_message_product_setting($post_id){
    $remove = isset($_POST['_pa_remove_remaining_stock_quantity']) ? $_POST['_pa_remove_remaining_stock_quantity'] : 'global';
    update_post_meta($post_id, '_pa_remove_remaining_stock_quantity', sanitize_text_field($remove));
}

// remove the "remaining stock quantity" portion of "not enough stock" message
add_filter('woocommerce_cart_product_not_enough_stock_message', 'pa_remove_remaining_stock_message', 20, 3);

function pa_remove_remaining_stock_message($message, $product_data, $stock_quantity){
    $final = '';
    if ($product_data->is_type( 'variation')) {
        $product_data = wc_get_product($product_data->get_parent_id());
    }
    $meta = get_post_meta($product_data->get_id(), '_pa_remove_remaining_stock_quantity', true) ? get_post_meta($product_data->get_id(), '_pa_remove_remaining_stock_quantity', true) : 'global';
    if($meta == 'global'){
        $final = get_option('pa_remove_remaining_stock_quantity', 'yes');
    }else{
        $final = $meta;
    }
    if('yes' === $final){
        $message = substr($message, 0, strpos($message, "("));
        $message .= '.';
    }
    
    return $message;
}

// add snap and tiktok integration codes
add_action('wp_head', 'pa_add_integration_codes');

function pa_add_integration_codes(){
?>

<?php
}

// function to get product price in html \

function get_product_price_html( $product ){
    $price_html = '<div class="product-price">';
    if ( $product->get_price() > 0 ) {
        if ($product->get_price() && $product->get_regular_price()) {
            $from = $product->get_regular_price();
            $to = $product->get_price();
            $price_html .= '<del>'. ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) .'</del><ins>'.( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) .'</ins>';
        }else{
            $to = $product->get_price();
            $price_html .= '&nbsp; <ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</ins>';
        }
    }else{
        $price_html .= '<div class="free">free</div>';
    }
    $price_html .= '</div>';
    return $price_html;
}
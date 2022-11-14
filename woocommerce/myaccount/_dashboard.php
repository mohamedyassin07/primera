<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 NM: Modified */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $nm_theme_options;
?>

<div class="nm-MyAccount-dashboard">
    <?php
        // Custom dashboard text
        if ( strlen( $nm_theme_options['myaccount_dashboard_text'] ) > 0 ) {
            echo wp_kses_post( $nm_theme_options['myaccount_dashboard_text'] );
        }
    ?>

	<p><?php /*
        printf(
            __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' ),
            esc_url( wc_get_endpoint_url( 'orders' ) ),
            esc_url( wc_get_endpoint_url( 'edit-address' ) ),
            esc_url( wc_get_endpoint_url( 'edit-account' ) )
        );
    */ ?></p>

    <div class="alk_my_account_main">
        <div class="alk_my_account alk_points">
            <p class="number"><?php echo do_shortcode('[mycred_my_balance wrapper=0 title_el="" balance_el=""]') ?> نقطة</p>
            <p>تصفح متجر الهدايا</p>
            <a href="https://www.alkhdmah.com/maxup/"><i class="nm-font nm-font-arrow-left"></i></a>
        </div>
        <div class="alk_my_account alk_wallet">
            <p class="number"><?php echo do_shortcode('[woo-mini-wallet]') ?></p>
            <p>رصيد محفظتك الحالي</p>
            <a href="https://www.alkhdmah.com/my-account/make-a-deposit/"><i class="nm-font nm-font-arrow-left"></i></a>
        </div>
    </div>

    <?php
        /**
         * My Account dashboard.
         *
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_dashboard' );

        /**
         * Deprecated woocommerce_before_my_account action.
         *
         * @deprecated 2.6.0
         */
        do_action( 'woocommerce_before_my_account' );

        /**
         * Deprecated woocommerce_after_my_account action.
         *
         * @deprecated 2.6.0
         */
        do_action( 'woocommerce_after_my_account' );
    ?>

</div>

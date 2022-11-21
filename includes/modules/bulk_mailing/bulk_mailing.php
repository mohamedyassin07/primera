<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Primera_Bulk_Mailing
 *
 * use the class to render it's form at any admin page
 * the class will be loaded in the ajax if the action
 *
 * @package		PRIMERA
 */
if ( ! class_exists( 'Primera_Bulk_Mailing' ) ) {

    class Primera_Bulk_Mailing {
        /**
         *
         * @var instance object
         */
        protected static $_instance = null;

        /**
         * @see Primera_Bulk_Mailing()
         * @return object
         */
        public static function instance()
        {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function __construct()
        {
            if( defined( 'DOING_AJAX' ) && DOING_AJAX == true  ){
                add_action( 'wp_ajax_primera_bulk_mailing', array( $this , 'ajax_handler' ) );
            }
        }

        public function ajax_handler()
        {
            $this->nonce_check(); // will die if nonce not correct or expired
            $this->send_affiliates_sorry_mail();

            if( ! isset( $_REQUEST['process'] ) || empty( $_REQUEST['process'] ) ||  ! isset( $_REQUEST['passkey'] ) || empty( $_REQUEST['passkey'] ) ){
                die( "Missing data! " );
            }

            if( $_REQUEST['process'] === 'send_affiliates_sorry_mail' && $_REQUEST['passkey'] === 'Whc3G+(cvt(yYs7L' ){
                $this->send_affiliates_sorry_mail();
            }

        }

        public function nonce_check()
        {
            $nonce = $_REQUEST['action'] . '_nonce_field';
            if ( ! isset( $_REQUEST[ $nonce ] ) 
                || ! wp_verify_nonce( $_REQUEST[ $nonce ], $_REQUEST['action'] ) 
            ) {
                die( 'Why the kingaro jumbed here !!!' );
            }
        }
        public function send_affiliates_sorry_mail()
        {
            $data = $_GET;
            global $wpdb;
            $per_request = 30;

            // 1# people with effect
            $step_0_title = 'People with effect';
            if( ! isset( $data[ 'steps' ] ) || ! isset( $data[ 'steps' ][ 0 ] ) ||  ! isset( $data[ 'steps' ][ 0 ][ 'total' ] ) ){
                $step_0_query= "SELECT COUNT(*) FROM `{$wpdb->prefix}affiliate_wp_affiliates` WHERE ( `earnings` > 0 OR `referrals` > 0 OR `visits` > 0 )  AND `payment_email` != '' ";
                $data['start'] = time();
                $data[ 'steps' ][ 0 ][ 'total' ] =  (int)$wpdb->get_row( $step_0_query, ARRAY_A )['COUNT(*)'];
                $data[ 'steps' ][ 0 ][ 'title' ] =  $step_0_title;
                $data[ 'steps' ][ 0 ][ 'done' ] = 0;
                $data[ 'request_status' ] = 'working'; 
                wp_send_json_success( $data );
            }

            if( $data[ 'steps' ][ 0 ][ 'total' ] >  $data[ 'steps' ][ 0 ][ 'done' ] ){
                $offset = $data[ 'steps' ][ 0 ][ 'done' ];

                $step_0_query= "SELECT affiliate_id, payment_email FROM `{$wpdb->prefix}affiliate_wp_affiliates` WHERE ( `earnings` > 0 OR `referrals` > 0 OR `visits` > 0 )  AND `payment_email` != '' LIMIT {$offset}, {$per_request} ";
                $results = $wpdb->get_results( $step_0_query, ARRAY_A );

                $this->set_aff_mail_settings();
                foreach ( $results as $aff ) {
                    wp_mail( $aff['payment_email'], $this->email_subject , $this->email_body, $this->email_headers );
                }
                update_option( 'last_send_affiliates_sorry_mail', $aff['affiliate_id'] ,false );

                $data[ 'steps' ][ 0 ][ 'title' ] = $step_0_title . ' ' . $aff['affiliate_id'];
                $data[ 'steps' ][ 0 ][ 'done' ] = (int)$data[ 'steps' ][ 0 ][ 'done' ] + count( $results );
                wp_send_json_success( $data );
            }

            // 2# people without effect
            $step_1_title = 'People without effect';
            if( ! isset( $data[ 'steps' ][ 1 ] ) ||  ! isset( $data[ 'steps' ][ 1 ][ 'total' ] ) ){
                $step_1_query= "SELECT COUNT(*) FROM `{$wpdb->prefix}affiliate_wp_affiliates` WHERE ( `earnings` = 0 OR `referrals` = 0 OR `visits` = 0 )  AND `payment_email` != '' ";
                $data[ 'steps' ][ 1 ][ 'total' ] =  (int)$wpdb->get_row( $step_1_query, ARRAY_A )['COUNT(*)'];
                $data[ 'steps' ][ 1 ][ 'title' ] = $step_1_title;
                $data[ 'steps' ][ 1 ][ 'done' ] = 0;
                wp_send_json_success( $data );
            }

            if( $data[ 'steps' ][ 0 ][ 'total' ] ==  $data[ 'steps' ][ 0 ][ 'done' ] ){
                $data[ 'request_status' ] = 'done'; //  we will stop here cause kinesta refuse to send a huge number of emails
                $data['end'] = time();
                $data['time_dif'] = $data['start'] - $data['end'];
                wp_send_json_success( $data );
            }

            if( $data[ 'steps' ][ 1 ][ 'total' ] >  $data[ 'steps' ][ 1 ][ 'done' ] &&  100 > $data[ 'steps' ][ 1 ][ 'done' ]  ){
                $offset = $data[ 'steps' ][ 1 ][ 'done' ];

                $step_1_query= "SELECT affiliate_id, payment_email FROM `{$wpdb->prefix}affiliate_wp_affiliates` WHERE ( `earnings` = 0 OR `referrals` = 1 OR `visits` = 1 )  AND `payment_email` != '' LIMIT {$offset}, {$per_request} ";
                $results = $wpdb->get_results( $step_1_query, ARRAY_A );

                $this->set_aff_mail_settings();
                foreach ( $results as $aff ) {
                    wp_mail( $aff['payment_email'], $this->email_subject , $this->email_body, $this->email_headers );
                }
                update_option( 'last_send_affiliates_sorry_mail', $aff['affiliate_id'] ,false );

                $data[ 'steps' ][ 1 ][ 'title' ] = $step_1_title . ' ' . $aff['affiliate_id'];
                $data[ 'steps' ][ 1 ][ 'done' ] = (int)$data[ 'steps' ][ 1 ][ 'done' ] + count( $results );
                wp_send_json_success( $data );
            }
            
            if( $data[ 'steps' ][ 1 ][ 'total' ] ==  $data[ 'steps' ][ 1 ][ 'done' ] ){
                $data[ 'request_status' ] = 'done';
                $data['end'] = time();
                $data['time_dif'] = $data['start'] - $data['end'];
                wp_send_json_success( $data );
            }

        }
        public function set_aff_mail_settings()
        {
            add_filter( 'wp_mail_content_type', array( $this, 'set_html_mail_content_type' ) );

            $this->email_subject    = 'أعتذار يطيب الخاطر 😍💜';
            $this->email_body       = file_get_contents( PRIMERA_THEME_URL  .'tests/aff_mail_con.html' );
            $this->email_headers    = array( 'Content-Type: text/html; charset=UTF-8' );
        }

        public function send_mail( $to )
        {
            wp_mail( $to, $this->email_subject , $this->email_body, $this->email_headers );
            update_option( 'last_send_affiliates_sorry_mail', 320 ,false );

        }

        public function set_html_mail_content_type() {
            return 'text/html';
        }

        public static function render_form( $action )
        {
            require_once PRIMERA_THEME_DIR . 'includes/classes/class-ajax.php';
            Primera_ِAjax::render_form_start( $action );
            echo '<label>Process</label><br><input type="text" name="process"><br>';
            echo '<label>Prpasskeyocess</label><br><input type="text" name="passkey"><br>';
            Primera_ِAjax::render_form_end( $action );
        }
    }

    Primera_Bulk_Mailing::instance();
}
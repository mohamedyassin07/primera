<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Primera_ِAjax
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 * 
 * 
 * 
 * HOW TO USE ??
 * - in the frontend page 
 * require_once PRIMERA_THEME_DIR . 'includes/classes/class-ajax.php';
 * Primera_ِAjax::render_form_start( $action );
 * do your normal field/s here,  all of them will be send throug the ajax request automatically
 * Primera_ِAjax::render_form_end( $action );
 * 
 * - in the backend 
 * $data[ 'steps' ][ 0 ][ 'total' ] =  (int)$wpdb->get_row( $step_1_query, ARRAY_A )['COUNT(*)'];
 * $data[ 'steps' ][ 0 ][ 'title' ] = $step_1_title;
 * $data[ 'steps' ][ 0 ][ 'done' ] = 0;
 * $data[ 'request_status' ] = 'working';
 * wp_send_json_success( $data );
 *
 * @package		PRIMERA
 * @subpackage	Classes/Primera_ِAjax
 * @author		Mohamed Yassin
 * @since		1.0.0
 */
class Primera_ِAjax
{
    static public function action( $action )
    {
        return trim( urlencode( $action ) );
    }

    static public function render_form_start( $action, $submit = 'submit' ){
        echo  "<form id='{$action}_form'>";
    }

    static public function render_form_end( $action, $submit = 'submit' )
    {
        $action = self::action( $action );
        wp_nonce_field( $action, $action.'_nonce_field' );
        echo self::clicked_item( $action, $submit );
        echo self::response_div( $action );
        echo "</form>";
    }

    static public function clicked_item( $action, $submit )
    {    
        return '<input type="submit" class="primera-ajax-btn" value="'.$submit.'" data-action="' . $action . '">';
    }

    static public function response_div( $action )
    {
        return '<div class="primera-ajax-resp" id="' . $action . '_response_div"></div>';
    }
}

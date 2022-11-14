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
 * <?php require_once PRIMERA_THEME_DIR . 'includes/classes/class-ajax.php'; ?>
 * <form id='{action_name}_form'>
 *      do your normal field/s here,  all of them will be send throug the ajax request automatically
 *      <?php Primera_ِAjax::render( '{action_name}' ); ?> it will create submit btn and response div
 * </form>
 * 
 * 
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

    static public function render( $action, $tag = 'submit' )
    {
        $action = self::action( $action );
        echo self::clicked_item( $action, $tag );
        echo self::response_div( $action );
    }

    static public function clicked_item( $action, $tag )
    {    
        return '<input type="submit" class="primera-ajax-btn" value="submit" data-action="' . $action . '">';
    }

    static public function response_div( $action )
    {
        return '<div class="primera-ajax-resp" id="' . $action . '_response_div"></div>';
    }
}

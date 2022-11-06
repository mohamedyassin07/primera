<?php
function primera_option( $key )
{
    $primera_options = get_option( 'primera_option' );
    if( isset( $primera_options[ $key ] ) ){
        return $primera_options[ $key ];
    }
   return null;
}

function primera_duplicated_plugin_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php echo PRIMERA_VERSION . ' ' . __('Primera plugin version is allready installed' ,'primera' ); ?></p>
    </div>
    <?php
}

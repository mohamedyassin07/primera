<?php
add_action( 'wp_footer', 'primera_hook_product_category_javascript');
function primera_hook_product_category_javascript() {
    //Level 1. Main page (Product category page)
    if ( is_product_category() ) {
        $product_category = get_queried_object();
        $ad_category = '';
        if( !empty( $product_category ) ) {
            if( $product_category->taxonomy === 'product_cat' ) {
                $ad_category = $product_category->name; 
            }
        }
        ?>
        <script type="text/javascript" id="retag-product-category">

            window.ad_category = <?php echo $ad_category; ?>;   // required
            console.log(window.ad_category);
            window._retag = window._retag || [];
            window._retag.push({code: "9ce8884f0c", level: 1});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s=document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })()
        </script>
        <?php
    }
    //  Level 2. Product page
    if( is_product() ) { 
        global $product;
        $id = $product->get_id();
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        $_category = '';
        if( $terms ) {
            $_category = isset( $terms[0] ) ? $terms[0]->name : '';
        }
        ?>
    <script type="text/javascript" id="retag-product-page">
            // required object
            window.ad_product = {
                "id": "<?php echo $product->get_id(); ?>",   // required
                "vendor": "",
                "price": "<?php echo $product->get_price(); ?>",
                "url": "<?php echo get_permalink(  $product->get_id() ); ?>",
                "picture": "",
                "name": "<?php echo $product->get_name(); ?>",
                "category": "<?php echo $_category; ?>"
            };
            console.log(window.ad_product);
            window._retag = window._retag || [];
            window._retag.push({code: "9ce8884f0d", level: 2});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })()
        </script>
    <?php }

    // Level 3. Cart page/Checkout page
    if( is_cart() || ( is_checkout() && empty( is_wc_endpoint_url('order-received') ) ) ) { 
        
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $ad_product = [];
        if( !empty( $items ) ) {
            foreach ( $items as $item ) {
                $ad_product[] = [
                    "id" => $item['product_id'],   // required
                    "number" => $item['quantity']
                ];
                
            }
        }
        $ad_product = json_encode($ad_product);
        // var_dump($ad_product);
        ?>
        <script type="text/javascript">
            window.ad_products = <?php echo $ad_product; ?>;
            console.log(window.ad_products);
            window._retag = window._retag || [];
            window._retag.push({code: "9ce8884f0a", level: 3});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })()
        </script>
    <?php }

    // Level 4. Thank you page
    if ( is_checkout() && !empty( is_wc_endpoint_url('order-received') ) ) { 
        global $wp;
        if ( isset($wp->query_vars['order-received']) ) {
            $order_id = absint($wp->query_vars['order-received']); // The order ID
            $order    = wc_get_order( $order_id ); // The WC_Order object
        }
        $ad_order = $order->get_id();
        $ad_amount = $order->get_total();
        $ad_product = [];
        foreach( $order->get_items() as $item_id => $item ) {
            $ad_product[] = [
                "id" => $item->get_product_id(),   // required
                "number" => $item->get_quantity()
            ];
        }
        $ad_product = json_encode($ad_product);
        // var_dump($ad_product);
        ?>
        <script type="text/javascript">
            window.ad_order    = <?php echo $ad_order; ?>;    // required
            window.ad_amount   = <?php echo $ad_amount; ?>;
            window.ad_products = <?php echo $ad_product; ?>;            
            window._retag = window._retag || [];
            window._retag.push({code: "9ce8884f0b", level: 4});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })()
        </script>
    <?php }
}
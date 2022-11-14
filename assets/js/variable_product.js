(function($) {
    $(document).ready(function(){
        var prices = variables.variations;
        $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
            //alert( variation.variation_id );
            var id = variation.variation_id;
            //console.log(prices[variation.variation_id]);
            var info = prices[id];
            if(info){
                if(info.currency_sale_html){
                    $('.woocommerce-variation-price .price .amount bdi').html(info.currency_sale_html);
                }else{
                    if(info.currency_html){
                        $('.woocommerce-variation-price .price .amount bdi').html(info.currency_html);
                    }
                }
                
                if(info.is_sale){
                    $('span.onsale').show();
                    //var currency = $('.product .summary .price del .amount bdi .woocommerce-Price-currencySymbol').clone();
                    //$('.product .summary .price del .amount bdi').html(info.price).append(currency);
                    $('.product .summary .price del .amount bdi').html(info.currency_html);
                }else{
                    $('span.onsale').hide();
                }
                
                if(info.fee){
                    if($('.pa_fee_message').length == 0){
                        $('.product_title').after('<p class="pa_fee_message nm-highlight-text">'+ variables.fee_message +'</p>');
                    }
                }else{
                    $('.pa_fee_message').remove();
                }
            }
            
            $('.woocommerce-price-suffix').hide();
        });
    });
    
})(jQuery);
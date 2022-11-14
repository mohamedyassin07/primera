(function($) {
    $(document).ready(function(){
        $(document).ajaxComplete(function(){
            if($('#k-net_payment_button_container').length){
                $('#k-net_payment_button_container').html('<button type="button" id="k-net_payment_button"><img src="'+ variables.knet_logo +'" width="100" height="100"></button>');
                $('#k-net_payment_button').on('click', function(){
                    $('#place_order').trigger('click');
                });
                $('#k-net_payment_button_container').show();
            }
            var id = $('input[name=payment_method]:checked').attr('id');
            if(id){
                change_order_button(id);
            }
        });
        
        $(document).on('change', 'input[name=payment_method]', function(){
            var pay_id = $(this).attr('id');
            change_order_button(pay_id);
        });
        
        var id = $('input[name=payment_method]:checked').attr('id');
        if(id){
            change_order_button(id);
        }
    });
    
    function change_order_button(pay_id){
        if(pay_id.includes('payment_method_hyperpay')){
            $('#place_order').val(variables.next);
        }else{
            $('#place_order').val(variables.place_order);
        }
    }
    
})(jQuery);
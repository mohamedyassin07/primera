(function($) {
    $(document).ready(function(){
        $(document).ajaxComplete(function(){
            $('*[data-action="show-initial-forms"]').text(variables.add);
            $('.wpwl-button-pay').show();
            var back_button = '<div class="pa_back_button_container"><a href="'+variables.checkout_url+'" class="wpwl-button pa_back_button">'+variables.back+'</a></div>';
            $('div.clear').after(back_button);
        });
    });
    
})(jQuery);
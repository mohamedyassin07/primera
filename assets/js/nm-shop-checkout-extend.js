(function ($) {
    'use strict';
    if ($.nmThemeExtensions.checkout) {
        $('.edit_billing').bind('click.nmShowForm', function (e) {
            e.preventDefault();
            cwg_show_popup_from_magnific('#nm-checkout-billing-form', 'nm-billing-popup');
        });

        $('.cwg-popup-billing-proceed').bind('click.nmShowForm', function (e) {
            e.preventDefault();
            $.magnificPopup.close();
        });
    }

    /*reuse the function from logincouponshowform but that is not contain any callback which helps to do additional stuff*/
    function cwg_show_popup_from_magnific($formContainer, containerClass) {
        $('form.checkout').find('.input-text, select, input:checkbox').trigger('validate').blur();
        $.magnificPopup.open({
            mainClass: containerClass + ' nm-mfp-fade-in',
            alignTop: true,
            closeMarkup: '<a class="mfp-close nm-font nm-font-close2"></a>',
            removalDelay: 180,
            items: {
                src: $formContainer,
                type: 'inline'
            }, callbacks: {
                close: function () {
                    cwg_fetch_current_form_data();
                },
                change: function () {
                    $('form.checkout').find('.input-text, select, input:checkbox').trigger('validate').blur();
                },
            },
        });

    }

    cwg_fetch_current_form_data();
    function cwg_fetch_current_form_data() {
        var fieldtocheck = ["name", "email", "phone"];
        var element_prefix = "cwg-deliver-to-";
        $.each(fieldtocheck, function (index, value) {
            var prefix_data = "billing_";
            if (value == 'name') {
                var name = $('#' + prefix_data + "first_name").val() + " " + $('#' + prefix_data + "last_name").val();
                $("." + element_prefix + value).html("<strong>" + name + "</strong>");
            } else {
                var getfieldvalue = $('#' + prefix_data + value).val();
                $("." + element_prefix + value).html(getfieldvalue);
            }
        });
        $(document.body).trigger("update_checkout");
        $('.cwg-deliver-to-details').show();
    }

})(jQuery);

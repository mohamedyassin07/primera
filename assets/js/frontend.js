jQuery(function () {
    "use strict";

    var $ = jQuery;
    var click_to_reveal = alk_localize.click_to_reveal;
    var click_to_copy = alk_localize.click_to_copy;
    var alk_copied = alk_localize.copied;
    var alk_revealed = alk_localize.revealed;
    var alk_error = alk_localize.error_in_copy;

    $('.alk_clipboard_reveal').on('click', function (e) {
        $('.alk_clipboard_reveal').html(click_to_reveal);
        $('.license-key').addClass('license-key-blur');
        $(this).parent().find('.license-key-blur').removeClass('license-key-blur');
        $(this).parent().find('.alk_clipboard_reveal').html(alk_revealed);
        return false;
    });
    var anchors = document.querySelectorAll('a.clipboard');
    var clipboard = new ClipboardJS(anchors);

    clipboard.on('success', function (e) {
        $('.alk_clipboard_reveal').html(click_to_reveal);
        $('.license-key').addClass('license-key-blur');
        $('a.clipboard').html(click_to_copy);
        //change button text
        $(e.trigger).html(alk_copied);
        $(e.trigger).parent().find('.license-key').removeClass('license-key-blur');
        $(e.trigger).parent().find('.alk_clipboard_reveal').html(alk_revealed);
    });

    clipboard.on('error', function (e) {
        console.log(e);
        $(e.trigger).html(alk_error);
    });
});

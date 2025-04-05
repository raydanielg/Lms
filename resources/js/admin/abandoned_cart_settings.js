(function ($) {
    "use strict";


    $('body').on('change', '#reset_cart_itemsSwitch', function (e) {
        const value = $(this).val();
        const $el = $('.js-reset-hours-field');

        if (value === "1") {
            $el.removeClass('d-none');
        } else {
            $el.removeClass('d-none');
        }
    });



})(jQuery);

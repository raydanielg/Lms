(function ($) {
    "use strict";


    $('body').on('change', '#activate_text_service_typeSwitch', function (e) {
        const value = $(this).val();
        const $el = $('.js-text-fields');

        if (value === "1") {
            $el.removeClass('d-none');
        } else {
            $el.removeClass('d-none');
        }
    });

    $('body').on('change', '#activate_image_service_typeSwitch', function (e) {
        const value = $(this).val();
        const $el = $('.js-image-fields');

        if (value === "1") {
            $el.removeClass('d-none');
        } else {
            $el.removeClass('d-none');
        }
    });


})(jQuery);

(function () {
    "use strict"

    $('body').on('change','input[name="type"]',function () {
        const val = $(this).val();
        const $email = $('.js-email-fields');
        const $mobile = $('.js-mobile-fields');

        if (val === "email") {
            $email.removeClass('d-none');
            $mobile.addClass('d-none')
        } else {
            $email.addClass('d-none');
            $mobile.removeClass('d-none')
        }
    })
})(jQuery)

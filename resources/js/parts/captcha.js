
(function ($) {
    "use strict";

    window.captcha_src = function (callback) {

        $.ajax({
            url: '/captcha/create',
            type: 'post',
            cache: false,
            timeout: 30000,
            success: function (data) {
                if (data.status == 'success') {
                    if (callback) {
                        callback(data.captcha_src);
                    }
                } else {
                    callback(false);
                }
            }
        });
    };

    window.refreshCaptcha = function () {
        captcha_src(function (captcha_src) {
            if (captcha_src) {
                $('.captcha-image').attr('src', captcha_src);
            } else {
                $('.captcha-image').closest('.form-group').find('.help-block').html('Oops!');
            }
        });
    };

    if ($('#captchaImageComment').attr("src") === '') {
        refreshCaptcha();
    }

    $('body').on('click', '#refreshCaptcha', function (e) {
        e.preventDefault();
        refreshCaptcha();
    });
})(jQuery);

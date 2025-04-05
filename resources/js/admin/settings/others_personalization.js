(function ($) {
    "use strict";

    $('body').on('change', '.js-user-avatar-style-select', function (e) {
        e.preventDefault();

        const $input = $('.js-default-user-avatar-field');

        if ($(this).val() === "default_avatar") {
            $input.removeClass('d-none');
        } else {
            $input.addClass('d-none');
        }
    });

})(jQuery);

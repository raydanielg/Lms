(function ($) {
    "use strict";

    $('body').on('click', '.js-show-gift-message', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#giftMessage');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');
    });
})(jQuery);

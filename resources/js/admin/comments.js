(function ($) {
    "use strict";

    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#contactMessage');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');
    });
})(jQuery);

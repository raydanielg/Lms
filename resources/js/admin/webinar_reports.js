(function ($) {
    "use strict";

    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('.report-description').val();
        const reason = $(this).parent().find('.report-reason').val();

        const $modal = $('#reportMessage');
        $modal.find('.js-reason span').html(reason);
        $modal.find('.js-description p').html(message);

        $modal.modal('show');
    });
})(jQuery);

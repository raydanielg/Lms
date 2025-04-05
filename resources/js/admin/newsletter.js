(function () {
    "use strict";

    $('body').on('change', '.js-newsletter-send-method', function (e) {

        const value = $(this).val();
        const $bcc = $('.js-newsletter-bcc-email');
        const $excel = $('.js-newsletter-excel');

        if (value === 'send_to_bcc') {
            $bcc.removeClass('d-none');
            $excel.addClass('d-none');
        } else if (value === 'send_to_excel') {
            $bcc.addClass('d-none');
            $excel.removeClass('d-none');
        } else {
            $bcc.addClass('d-none');
            $excel.addClass('d-none');
        }
    });

    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#newsletterMessageModal');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');
    });
})(jQuery);

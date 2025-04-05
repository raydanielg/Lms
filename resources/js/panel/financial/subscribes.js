(function ($) {
    "use strict";

    $('body').on('click', '.request-payout', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#requestPayoutModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '40rem',
        });
    });

})(jQuery);

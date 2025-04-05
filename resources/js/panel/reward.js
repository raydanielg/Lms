(function () {
    "use strict";

    $('body').on('click', '.js-exchange-btn', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#exchangePointsModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '28rem',
        });
    });

    $('body').on('click', '.js-apply-exchange', function (e) {
        const $this = $(this);

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.post('/panel/rewards/exchange', {}, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    title: exchangeSuccessAlertTitleLang,
                    text: exchangeSuccessAlertDescLang,
                    showConfirmButton: false,
                    icon: 'success',
                });
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }).fail(err => {
            Swal.fire({
                title: exchangeErrorAlertTitleLang,
                text: exchangeErrorAlertDescLang,
                showConfirmButton: false,
                icon: 'error',
            });
        });
    });

})(jQuery);

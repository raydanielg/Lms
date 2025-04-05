(function ($) {
    "use strict";


    $('body').on('click', '.js-pay-promotion', function (e) {
        e.preventDefault();
        var $this = $(this);
        var subscribe_plan = $this.closest('.subscribe-plan');

        var promotion_id = $this.attr('data-promotion-id');
        var promotion_title = subscribe_plan.find('.subscribe-plan-title').text();
        var promotion_price = subscribe_plan.find('.subscribe-plan-price').text();


        $('#promotionModal input[name="promotion_id"]').val(promotion_id);
        $('#promotionModal .modal-title').text(promotion_title);
        $('#promotionModal .modal-price').text(promotion_price);


        let promotion_modal = '<div id="payPromotionModal">';
        promotion_modal += $('#promotionModal').html();
        promotion_modal += '</div>';

        Swal.fire({
            html: promotion_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
        });
    });

    $('body').on('click', '#payPromotionModal .js-submit-promotion', function (e) {
        const select = $('#payPromotionModal select[name="webinar_id"]');
        select.removeClass('is-invalid');

        if (select.val()) {
            $(this).addClass('loadingbar primary').prop('disabled', true);
            $('#payPromotionModal form').trigger('submit');
        } else {
            select.addClass('is-invalid');
        }
    });
})(jQuery)

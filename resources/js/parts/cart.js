(function ($) {
    "use strict";

    $('body').on('click', '#checkCoupon', function (e) {
        e.preventDefault();
        var $this = $(this);
        var couponInput = $('#coupon_input');
        const invalidFeedback = couponInput.parent().find('.invalid-feedback');
        var coupon = couponInput.val();
        couponInput.removeClass('is-invalid is-valid');
        invalidFeedback.text(couponInvalidLng);

        if (coupon) {
            $this.addClass('loadingbar primary').prop('disabled', true);

            $.post('/cart/coupon/validate', {coupon: coupon}, function (result) {
                if (result && result.status == 200) {
                    $('#totalAmount').text(result.total_amount);
                    $('#taxPrice').text(result.total_tax);
                    $('#totalDiscount').text(result.total_discount);
                    $('#cartForm input[name="discount_id"]').val(result.discount_id);
                    $('#checkCoupon').prop('disabled', true);
                    couponInput.addClass('is-valid');
                } else if (result && result.status == 422) {
                    couponInput.removeClass('is-valid');
                    couponInput.addClass('is-invalid');
                    invalidFeedback.text(result.msg);
                }
            }).always(() => {
                $this.removeClass('loadingbar primary').prop('disabled', false);
            });
        } else {
            couponInput.addClass('is-invalid');
        }
    })
})(jQuery);

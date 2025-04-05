(function () {
    "use strict";

    $('body').on('change', '.js-discount-type', function (e) {

        const fixedAmountInputs = $('.js-fixed-amount-inputs');
        const percentageInputs = $('.js-percentage-inputs');

        if ($(this).val() === 'percentage') {
            percentageInputs.removeClass('d-none');
            fixedAmountInputs.addClass('d-none');
        } else {
            percentageInputs.addClass('d-none');
            fixedAmountInputs.removeClass('d-none');
        }
    });


    $('body').on('change', '.js-discount-source', function (e) {
        const value = $(this).val();

        const courseInput = $('.js-courses-input');
        const categoriesInput = $('.js-categories-input');
        const productsInput = $('.js-products-input');
        const bundlesInput = $('.js-bundles-input');

        courseInput.addClass('d-none');
        categoriesInput.addClass('d-none');
        productsInput.addClass('d-none');
        bundlesInput.addClass('d-none');

        if (value === 'course') {
            courseInput.removeClass('d-none');
        } else if (value === 'category') {
            categoriesInput.removeClass('d-none');
        } else if (value === 'product') {
            productsInput.removeClass('d-none');
        } else if (value === 'bundle') {
            bundlesInput.removeClass('d-none');
        }
    });
})(jQuery);

(function () {
    "use strict";

    $('body').on('change', 'select[name="action"]', function (e) {

        const discountField = $('.js-discounts-field');

        if ($(this).val() === 'send_coupon') {
            discountField.removeClass('d-none');
        } else {
            discountField.addClass('d-none');
        }
    });


    $('body').on('change', '#repeat_actionSwitch', function (e) {

        const field = $('.js-repeat-action-count-field');

        if (this.checked) {
            field.removeClass('d-none');
        } else {
            field.addClass('d-none');
        }
    });


    $('body').on('change', '.js-target-types-input', function () {
        const value = $(this).val();
        const $targets = $('.js-select-target-field');

        $targets.find('select').val("");

        $targets.addClass('d-none');
        $targets.find('.js-target-option').addClass('d-none');

        if (value && value !== 'all') {
            $targets.removeClass('d-none');
            $targets.find(`.js-target-option-${value}`).removeClass('d-none');
        }

        handleSpecificItemsShow();
    });

    $('body').on('change', '.js-target-input', function () {
        const value = $(this).val();
        const targetType = $('.js-target-types-input').val();

        handleSpecificItemsShow(value, targetType);
    });

    function handleSpecificItemsShow(target = '', targetType = '') {
        const $specificCategoriesField = $('.js-specific-categories-field');
        const $specificInstructorsField = $('.js-specific-instructors-field');
        const $specificSellersField = $('.js-specific-sellers-field');
        const $specificCoursesField = $('.js-specific-courses-field');
        const $specificProductsField = $('.js-specific-products-field');
        const $specificBundlesField = $('.js-specific-bundles-field');

        $specificCategoriesField.addClass('d-none');
        $specificInstructorsField.addClass('d-none');
        $specificSellersField.addClass('d-none');
        $specificCoursesField.addClass('d-none');
        $specificProductsField.addClass('d-none');
        $specificBundlesField.addClass('d-none');

        if (target === "specific_categories") {
            $specificCategoriesField.removeClass('d-none');
        } else if (target === "specific_instructors") {
            $specificInstructorsField.removeClass('d-none');
        } else if (target === "specific_sellers") {
            $specificSellersField.removeClass('d-none');
        } else if (target === "specific_courses") {
            $specificCoursesField.removeClass('d-none');
        } else if (target === "specific_products") {
            $specificProductsField.removeClass('d-none');
        } else if (target === "specific_bundles") {
            $specificBundlesField.removeClass('d-none');
        }
    }

})(jQuery);

(function () {
    "use strict";


    $('body').on('change', '.js-target-types-input', function () {
        const value = $(this).val();
        const $targets = $('.js-select-target-field');

        $targets.find('select').val("");

        $targets.addClass('d-none');
        $targets.find('.js-target-option').addClass('d-none');

        if (value && value !== 'all' && value !== 'recharge_wallet') {
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
        const $subscriptionSpecificPackagesField = $('.js-subscription-specific-packages-field');
        const $registrationSpecificPackagesField = $('.js-registration-specific-packages-field');

        $specificCategoriesField.addClass('d-none');
        $specificInstructorsField.addClass('d-none');
        $specificSellersField.addClass('d-none');
        $specificCoursesField.addClass('d-none');
        $specificProductsField.addClass('d-none');
        $specificBundlesField.addClass('d-none');
        $subscriptionSpecificPackagesField.addClass('d-none');
        $registrationSpecificPackagesField.addClass('d-none');

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
        } else if (target === "specific_packages" && targetType === "subscription_packages") {
            $subscriptionSpecificPackagesField.removeClass('d-none');
        } else if (target === "specific_packages" && targetType === "registration_packages") {
            $registrationSpecificPackagesField.removeClass('d-none');
        }
    }

    $('body').on('change', '.js-amount-type-select', function () {
        const value = $(this).val();

        const $card = $('.js-apply-cashback-per-item');
        const $maxAmountField = $('.js-max-amount-field');


        $card.addClass('d-none');
        $maxAmountField.addClass('d-none');

        $card.find('input').prop('checked', false);
        $maxAmountField.find('input').val('');

        if (value === 'fixed_amount') {
            $card.removeClass('d-none');
        }

        if (value === 'percent') {
            $maxAmountField.removeClass('d-none');
        }
    });

})(jQuery);

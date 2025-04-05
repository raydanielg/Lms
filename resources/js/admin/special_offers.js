(function () {
    "use strict"

    $('body').on('change', '.js-offer-type', function () {
        const value = $(this).val();

        const $courseField = $('.js-course-field');
        const $bundleField = $('.js-bundle-field');
        const $productField = $('.js-product-field');
        const $subscribeField = $('.js-subscribe-field');
        const $registrationPackageField = $('.js-registration_package-field');

        $courseField.addClass('d-none');
        $bundleField.addClass('d-none');
        $productField.addClass('d-none');
        $subscribeField.addClass('d-none');
        $registrationPackageField.addClass('d-none');

        if (value === "courses") {
            $courseField.removeClass('d-none')
        } else if (value === "bundles") {
            $bundleField.removeClass('d-none')
        } else if (value === "products") {
            $productField.removeClass('d-none')
        } else if (value === "subscription_packages") {
            $subscribeField.removeClass('d-none')
        } else if (value === "registration_packages") {
            $registrationPackageField.removeClass('d-none')
        }
    })
})(jQuery)

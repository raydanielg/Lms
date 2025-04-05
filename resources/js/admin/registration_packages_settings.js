(function ($) {
    "use strict";

    $('body').on('change', '#showPackagesDuringRegistrationSwitch', function (e) {

        var $forceUserSelectPackageSwitch = $('#forceUserSelectPackageSwitch');

        if (this.checked) {
            $forceUserSelectPackageSwitch.prop('disabled', false);
        } else {
            $forceUserSelectPackageSwitch.prop('checked', false);
            $forceUserSelectPackageSwitch.prop('disabled', true);
        }
    });

})(jQuery);

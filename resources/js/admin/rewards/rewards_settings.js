(function () {
    "use strict";

    $('body').on('change', '#exchangeableSwitch', function (e) {
        const $exchangeableUnitInput = $('#exchangeableUnitInput');

        if (this.checked) {
            $exchangeableUnitInput.removeClass('d-none');
        } else {
            $exchangeableUnitInput.addClass('d-none');
            $exchangeableUnitInput.find('input').val('');
        }
    });
})(jQuery);

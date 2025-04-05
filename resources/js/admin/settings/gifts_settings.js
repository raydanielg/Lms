(function () {
    "use strict"

    $('body').on('change', '#giftsStatusSwitch', function () {

        const $fields = $('.js-show-after-enable');

        if (this.checked) {
            $fields.removeClass('d-none')
        } else {
            $fields.addClass('d-none')
        }
    })

})(jQuery)

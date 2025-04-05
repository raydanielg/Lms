(function ($) {
    "use strict";

    $('body').on('change', '#banSwitch', function () {
        if (this.checked) {
            $('#banSection').removeClass('d-none');
        } else {
            $('#banSection').addClass('d-none');
        }
    });

    $('body').on('change', '.js-user-bank-input', function (e) {
        e.preventDefault();

        const $optionSelected = $(this).find("option:selected");
        const specifications = $optionSelected.attr('data-specifications')

        const $card = $('.js-bank-specifications-card');
        let html = '';

        if (specifications) {
            Object.entries(JSON.parse(specifications)).forEach(([index, item], key) => {

                html += '<div class="form-group">\n' +
                    '         <label class="font-weight-500 text-dark-blue">' + item + '</label>\n' +
                    '         <input type="text" name="bank_specifications[' + index + ']" value="" class="form-control"/>\n' +
                    ' </div>'
            })
        }

        $card.html(html);
    });

    $('body').on('change', '#enable_registration_bonusSwitch', function () {
        const $field = $('.js-registration-bonus-field')

        if (this.checked) {
            $field.removeClass('d-none');
        } else {
            $field.addClass('d-none');
        }
    });

})(jQuery);

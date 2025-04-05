(function ($) {
    "use strict";


    // added in forms.js
    /*$('body').on('click', '.panel-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'});
    });*/

    $('body').on('change', 'select[name="role"]', function (e) {
        e.preventDefault();

        const value = $(this).val();
        const $instructorLabel = $('.js-instructor-label');
        const $organizationLabel = $('.js-organization-label');

        let type = "become_organization";

        if (value === 'teacher') {
            $organizationLabel.addClass('d-none');
            $instructorLabel.removeClass('d-none');

            type = "become_instructor";
        } else {
            $organizationLabel.removeClass('d-none');
            $instructorLabel.addClass('d-none');
        }

        $.post('/become-instructor/form-fields', {type: type}, function (result) {
            if (result) {
                $('.js-form-fields-card').html(result.html);

                formsDatetimepicker();

                feather.replace();
            }
        })
    });

    $('body').on('change', 'input[name="id"]', function (e) {
        e.preventDefault();

        $('button#paymentSubmit').removeAttr('disabled');

        const packageId = $(this).val();

        checkPackageHasInstallment(packageId);
    });

    function checkPackageHasInstallment(id) {
        const path = '/become-instructor/packages/' + id + '/checkHasInstallment';
        const $btn = $('.js-installment-btn');
        $btn.addClass('d-none');

        $.get(path, function (result) {
            if (result && result.has_installment) {
                $btn.removeClass('d-none');
                $btn.attr('href', '/become-instructor/packages/' + id + '/installments');
            }
        });
    }


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

})(jQuery);

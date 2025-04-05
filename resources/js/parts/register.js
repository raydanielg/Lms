(function () {
    "use strict"

    $('body').on('change', 'input[name="account_type"]', function () {
        const value = $(this).val();

        $.post('/register/form-fields', {type: value}, function (result) {
            if (result) {
                $('.js-form-fields-card').html(result.html);

                formsDatetimepicker();

                feather.replace();
            }
        })
    })
})(jQuery)

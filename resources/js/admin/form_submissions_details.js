(function () {
    "use strict"

    $('body').on('click', '.js-clear-form', function () {
        const $form = $(this).closest('form')

        $form.find('textarea').val('');

        const $inputs = $form.find('input');

        for (const input of $inputs) {
            const $input = $(input);

            if ($input.attr('name') !== "_token") {
                if ($input.is(':checkbox') || $input.is(':radio')) {
                    $input.prop('checked', false)
                } else {
                    $input.val('')
                }
            }
        }
    })
})(jQuery)

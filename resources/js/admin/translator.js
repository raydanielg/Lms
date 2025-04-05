(function () {
    "use strict";

    $('body').on('click', '.js-submit-translator', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const path = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar').prop('disabled', true)
        const $msg = $('.js-translate-msg');
        $msg.removeClass('text-danger').addClass('d-none text-success');

        $.post(path, data, function (result) {
            $msg.removeClass('d-none');

            if (result.error && result.error !== "null") {
                $msg.addClass('text-danger');
                $msg.text(result.error);
            } else {
                $msg.text(result.msg);
            }

            $this.removeClass('loadingbar').prop('disabled', false)
        }).fail(err => {
            $this.removeClass('loadingbar').prop('disabled', false)

            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find(`.js-ajax-${key}`);
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });

                $('html, body').animate({
                    scrollTop: 5
                }, 600);
            }
        })
    })

    $('body').on('change', '#specificSwitch', function () {
        const $card = $('.js-specific-files-card');

        $card.addClass('d-none');
        $card.find('input').prop('checked', false);

        if (this.checked) {
            $card.removeClass('d-none');
        }

    })

})(jQuery)

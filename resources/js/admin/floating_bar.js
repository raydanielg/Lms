(function ($) {
    "use strict";

    $('body').on('click', '.js-submit-form', function () {
        const $this = $(this);
        const $form = $this.closest('form');

        $this.addClass('loadingbar primary').prop('disabled', true);
        $form.find('input[name="preview"]').val('')

        $form.trigger('submit')
    });

    $('body').on('click', '.js-preview-bar', function () {
        const $this = $(this);
        const $form = $(this).closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar').prop('disabled', true);

        $form.find('.invalid-feedback').text('');
        $form.find('.is-invalid').removeClass('is-invalid');

        $.post(action, data, function (result) {
            $this.removeClass('loadingbar').prop('disabled', false);

            window.open('/?preview_floating_bar=1')
        }).fail(err => {
            $this.removeClass('loadingbar').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });
})(jQuery);

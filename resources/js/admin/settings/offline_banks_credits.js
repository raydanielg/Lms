(function () {
    "use strict"

    function getModalData(path) {
        loadingSwl();

        $.get(path, function (result) {
            if (result.code === 200) {
                Swal.fire({
                    html: result.html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                    onOpen: () => {

                    }
                });
            }
        })
    }

    $('body').on('click', '.js-add-offline-banks, .js-edit-offline-banks', function () {
        const path = $(this).attr('data-path');

        getModalData(path)
    })

    $('body').on('change', '.js-offline-banks-locale', function () {
        const form = $(this).closest('#addOfflineBankForm');
        let path = form.attr('data-action')
        path = path.replaceAll('update', 'edit')
        path = path + "?locale=" + $(this).val()

        getModalData(path)
    })

    $('body').on('click', '.js-save-bank', function () {
        const $this = $(this)
        const $form = $('#addOfflineBankForm')

        let data = serializeObjectByTag($form);
        let action = $form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        $form.find('input').removeClass('is-invalid');
        $form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                //window.location.reload();
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    })

    $('body').on('click', '.js-add-specification', function () {
        const $parent = $('.js-specifications-lists');
        const random = randomString();

        const html = `<div class="js-specification-card row align-items-center">
                    <div class="col-10">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>${specificationLang}</label>
                                    <input type="text" name="specifications[${random}][name]" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>${valueLang}</label>
                                    <input type="text" name="specifications[${random}][value]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" class="js-remove-specification btn btn-danger">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>`;

        $parent.append(html)
    })

    $('body').on('click', '.js-remove-specification', function () {
        $(this).closest('.js-specification-card').remove();
    })
})(jQuery)

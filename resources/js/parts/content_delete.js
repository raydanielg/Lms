(function ($) {
    "use strict"

    function makeModalHtml(itemId, itemType) {
        return `<div class="custom-modal-body">
                <h2 class="section-title after-line">${deleteRequestLang}</h2>

                <div class="js-delete-content-form mt-20" data-action="/panel/content-delete-request">
                    <input type="hidden" name="item_id" value="${itemId}">
                    <input type="hidden" name="item_type" value="${itemType}">

                    <div class="bg-gray200 rounded-sm text-gray p-10 font-14">${deleteRequestDescriptionLang}</div>

                    <div class="form-group mt-20">
                        <label class="input-label">${requestDetailsLang}</label>
                        <textarea name="description" class="js-ajax-description form-control" rows="5"></textarea>
                        <div class="invalid-feedback font-14"></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end mt-3">
                        <button type="button" class="js-send-delete-request btn btn-sm btn-primary">${sendRequestLang}</button>
                        <button type="button" class="close-swl btn btn-sm btn-danger ml-2">${closeLang}</button>
                    </div>
                </div>
            </div>`
    }

    $('body').on('click', '.js-content-delete-action', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $this = $(this);
        const href = $this.attr('href');
        const itemId = $this.attr('data-item');
        const itemType = $this.attr('data-item-type');

        Swal.fire({
            html: makeModalHtml(itemId, itemType),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
        })
    });


    $('body').on('click', '.js-send-delete-request', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.js-delete-content-form');
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                //window.location.reload();
                Swal.fire({
                    icon: 'success',
                    title: result.title,
                    html: '<p class="font-16 text-center text-gray">' + result.msg + '</p>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 1500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });


})(jQuery)

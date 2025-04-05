(function () {
    "use strict";

    $('body').on('click', '.js-answer-edit', function (e) {
        e.preventDefault();
        const $this = $(this);
        const action = $this.attr('data-action');

        loadingSwl();

        $.get(action, function (result) {
            if (result && result.code === 200) {
                const post = result.post;

                const random = randomString();
                const html = makeEditPostHtml(post, action, random);

                Swal.fire({
                    html: html,
                    showConfirmButton: false,
                    width: '60rem',
                });
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });

    $('body').on('click', '.js-save-post', function (e) {
        e.preventDefault();
        const $this = $(this);
        const $form = $this.closest('form');

        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $form.find('.invalid-feedback').text('');
        $form.find('.is-invalid').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + savedSuccessfullyLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }

            $this.removeClass('loadingbar primary').prop('disabled', false);
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);

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

    function makeEditPostHtml(post, action, record) {

        return `<div>
        <h3 class="section-title after-line font-20 text-dark-blue">${editPostLang}</h3>

        <form action="${action}" method="post" class="mt-25">

            ${
            (typeof post.title !== "undefined") ?
                `
                    <div class="form-group text-left">
                        <label class="input-label">${titleLang}</label>
                        <input name="title" class="form-control" value="${post.title}"/>
                        <div class="invalid-feedback"></div>
                    </div>
                `
                :''
        }

            <div class="form-group text-left">
                <label class="input-label">${descriptionLang}</label>
                <textarea name="description" rows="5" class="form-control">${post.description}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            ${
                (typeof post.attach !== "undefined") ?
                `<div class="row">
                    <div class="col-12 col-md-7">
                        <div class="form-group text-left">
                            <label class="input-label">${editAttachmentLabelLang}</label>

                            <div class="d-flex align-items-center">
                                <div class="input-group mr-10">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text admin-file-manager" data-input="postAttachmentInput_${record}" data-preview="holder">
                                            <i class="fa fa-upload"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="attach" id="postAttachmentInput_${record}" value="${post.attach ?? ''}" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
                : ''
            }

            <div class="text-left">
                <button type="button" class="js-save-post btn btn-primary btn-sm mt-2">${sendLang}</button>
            </div>
        </form>
    </div>`;
    }
})(jQuery);

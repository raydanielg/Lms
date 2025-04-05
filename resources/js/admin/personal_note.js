(function ($) {
    "use strict";

    $('body').on('click', '.js-show-note', function (e) {
        e.preventDefault();
        const $this = $(this);
        const note = $this.parent().find('input').val();

        const html = '<div class="">' +
            '<h3 class="section-title after-line">' + noteLang + '</h3>' +
            '<p class="text-gray mt-20">' + note + '</p>' +
            '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '40rem',
        });


    });

    $('body').on('click', '.js-edit-note', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $tr = $this.closest("tr")
        const action = $this.attr('data-action')
        const note = $tr.find('.js-note-message').val();
        const attachment = $tr.find('.js-note-attachment').val();

        const html = `<form action="${action}" method="post">
            <div class="js-personal-notes-form text-left">
                <h4 class="font-14 font-weight-bold">${personalNoteLang}</h4>

                <textarea name="note" rows="5" class="form-control mt-1">${note ?? ''}</textarea>

                <div class="form-group mt-15">
                    <label class="input-label">${attachmentLang}</label>

                    <div class="input-group mr-10">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="personalNotesAttachment" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="attachment" id="personalNotesAttachment" value="${attachment ?? ''}" class="form-control" placeholder=""/>
                    </div>
                </div>

                <div class="d-flex align-items-center mt-15">
                    <button type="button" class="js-save-personal-note btn btn-primary">${saveNoteLang}</button>
                    <button type="button" class="btn btn-danger ml-2 close-swl">${closeLang}</button>
                </div>
            </div>
        </form>`;

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '40rem',
        });

    })

    $('body').on('click', '.js-save-personal-note', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    title: result.title,
                    html: '<p class="font-16 text-center text-gray py-2">' + result.msg + '</p>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload()
                }, 1500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {

            }
        });
    })

})(jQuery);

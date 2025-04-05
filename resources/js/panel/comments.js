(function ($) {
    "use strict";

    $('body').on('click', '.js-reply-comment', function (e) {
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.attr('data-comment-id');
        var comment = $('#commentDescription' + comment_id).val();

        var html = '<div class="">\n' +
            '        <h3 class="section-title after-line">' + replyToCommentLang + '</h3>\n' +
            '        <div class="rounded-sm p-15 border border-gray300 bg-info-light mt-20 text-gray">' + comment + '</div>\n' +
            '        <form id="commentForm" action="/panel/webinars/comments/' + comment_id + '/reply" method="post" class="mt-20">\n' +
            '            <div class="form-group">\n' +
            '                <label class="input-label">' + replyToCommentLang + '</label>\n' +
            '                <textarea name="comment" rows="6" class="form-control"></textarea>\n' +
            '            </div>\n' +
            '\n' +
            '            <div class="mt-30 d-flex align-items-center justify-content-end">\n' +
            '                <button type="button" class="btn btn-sm btn-primary js-save-form">' + saveLang + '</button>\n' +
            '                <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + closeLang + '</button>\n' +
            '            </div>\n' +
            '        </form>\n' +
            '    </div>';

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

    $('body').on('click', '.js-save-form', function (e) {
        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $(this).closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + result.msg + '</h3>',
                    showConfirmButton: true,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500)
            } else if (result && result.code === 401) {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + failedLang + '</h3>',
                    showConfirmButton: false,
                });
            }
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
        })
    });

    $('body').on('click', '.js-view-comment', function (e) {
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.attr('data-comment-id');
        var comment = $('#commentDescription' + comment_id).val();


        var html = '<div class="">' +
            '<h3 class="section-title after-line">' + commentLang + '</h3>' +
            '<p class="font-weight-500 text-gray mt-20">' + comment + '</p>' +
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

    $('body').on('click', '.js-edit-comment', function (e) {
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.attr('data-comment-id');
        var description = $('#commentDescription' + comment_id).val();


        var html = '<div class="">\n' +
            '        <h3 class="section-title after-line">' + editCommentLang + '</h3>\n' +
            '        <form action="/panel/webinars/comments/' + comment_id + '/update" method="post" class="mt-20">\n' +
            '            <input type="hidden" name="_token" value="' + csrfToken + '">\n' +
            '            <div class="form-group">\n' +
            '                <label class="input-label">' + replyToCommentLang + '</label>\n' +
            '                <textarea name="comment" rows="6" class="form-control">' + description + '</textarea>\n' +
            '            </div>\n' +
            '\n' +
            '            <div class="mt-30 d-flex align-items-center justify-content-end">\n' +
            '                <button type="button" class="btn btn-sm btn-primary js-save-form">' + saveLang + '</button>\n' +
            '                <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + closeLang + '</button>\n' +
            '            </div>\n' +
            '        </form>\n' +
            '    </div>';

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

    $('body').on('click', '.report-comment', function (e) {
        e.preventDefault();
        const comment_id = $(this).attr('data-comment-id');

        const html = '<div id="reportModal">\n' +
            '    <h3 class="section-title after-line font-20 text-dark-blue mb-25">' + reportLang + '</h3>\n' +
            '\n' +
            '    <form action="/panel/webinars/comments/' + comment_id + '/report" method="post">\n' +
            '        <div class="form-group">\n' +
            '            <label class="input-label">' + messageToReviewerLang + '</label>\n' +
            '            <textarea name="message" class="form-control" rows="6"></textarea>\n' +
            '            <div class="invalid-feedback"></div>\n' +
            '        </div>\n' +
            '\n' +
            '        <div class="mt-30 d-flex align-items-center justify-content-end">\n' +
            '            <button type="button" id="saveReport" class="btn btn-sm btn-primary">' + saveLang + '</button>\n' +
            '            <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + closeLang + '</button>\n' +
            '        </div>\n' +
            '    </form>\n' +
            '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });
    });

    $('body').on('click', '#saveReport', function (e) {
        const $this = $(this);
        let form = $('#reportModal form');
        let data = form.serializeObject();
        let action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + reportSuccessLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });

    $('body').on('change', '#newCommentsSwitch', function (e) {
        e.preventDefault();

        $(this).closest('form').trigger('submit');
    })

})(jQuery);

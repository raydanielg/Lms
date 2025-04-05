(function ($) {
    "use strict";

    $('body').on('click', '.reply-comment', function (e) {
        e.preventDefault();
        const $this = $(this);
        let comment_card = $this.closest('.comments-card');
        let comment_id = comment_card.attr('data-id');
        let csrf = comment_card.attr('data-csrf');
        let address = comment_card.attr('data-address');
        const item_id = $('#commentItemId').val();
        const item_name = $('#commentItemName').val();

        let replyCommentHtml = '<form action="' + address + '" method="post" class="mt-30">\n' +
            '<input type="hidden" name="_token" value="' + csrf + '"/>\n' +
            '<input type="hidden" name="item_id" value="' + item_id + '"/>' +
            '<input type="hidden" name="item_name" value="' + item_name + '"/>' +
            '<input type="hidden" name="comment_id" value="' + comment_id + '"/>\n' +
            '<div class="form-group">\n' +
            '<textarea name="reply" id="" class="form-control" rows="10"></textarea>\n' +
            '</div>\n' +
            '<button type="submit" class="btn btn-primary btn-sm">' + replyLang + '</button>\n' +
            '<button type="button" class="btn btn-gray btn-sm ml-20 reply-close">' + closeLang + '</button>\n' +
            '</form>';

        comment_card.append(replyCommentHtml);
    });

    $('body').on('click', '.reply-close', function (e) {
        e.preventDefault();
        $(this).closest('form').remove();
    });

    $('body').on('click', '.report-comment', function (e) {
        e.preventDefault();
        const comment_id = $(this).attr('data-comment-id');
        const item_id = $('#commentItemId').val();
        const item_name = $('#commentItemName').val();

        const html = '<div id="reportModal">\n' +
            '    <h3 class="section-title after-line font-20 text-dark-blue mb-25">' + reportLang + '</h3>\n' +
            '\n' +
            '    <form action="/comments/' + comment_id + '/report" method="post">\n' +
            '         <input type="hidden" name="item_id" value="' + item_id + '"/>' +
            '         <input type="hidden" name="item_name" value="' + item_name + '"/>' +
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
})(jQuery);

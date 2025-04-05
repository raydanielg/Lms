(function () {
    "use strict";

    $('body').on('click', '#askNewQuestion', function (e) {
        e.preventDefault();

        const rand = randomString();

        let clone = $('#askNewQuestionModal').clone();
        let copyHtml = clone.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', rand);
        clone.html(copyHtml);

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',
        });
    });

    function handleForumPostForm(form, $this) {
        let data = serializeObjectByTag(form);
        let action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

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
                }, 500);
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
        });
    }

    $('body').on('click', '.js-save-question', function (e) {
        e.preventDefault();

        const $this = $(this);
        const form = $this.closest('form');

        handleForumPostForm(form, $this);
    });

    $('body').on('click', '.js-save-question-answer', function (e) {
        e.preventDefault();

        const $this = $(this);
        const form = $this.closest('form');

        handleForumPostForm(form, $this);
    });

    $('body').on('click', '.js-reply-course-question', function (e) {
        e.preventDefault();

        const $this = $(this);
        const form = $this.closest('form');

        handleForumPostForm(form, $this);
    });

    $('body').on('click', '.js-edit-forum', function (e) {
        e.preventDefault();

        const $this = $(this);
        const action = $this.attr('data-action');

        loadingSwl();

        $.get(action, function (result) {
            if (result && result.forum) {
                const rand = randomString();

                let clone = $('#askNewQuestionModal').clone();
                let copyHtml = clone.prop('innerHTML');
                copyHtml = copyHtml.replaceAll('record', rand);
                clone.html('<div id="editQuestionModal">' + copyHtml + '</div>');

                Swal.fire({
                    html: clone.html(),
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                    onOpen: function () {
                        const modal = $('#editQuestionModal');

                        const form = modal.find('form');
                        let path = form.attr('action');
                        path = path.replaceAll('store', result.forum.id + '/update');

                        form.attr('action', path);

                        Object.keys(result.forum).forEach((key) => {
                            const value = result.forum[key];
                            let element = form.find('[name="' + key + '"]');

                            if (element && element.length) {
                                element.val(value);
                            }
                        });
                    }
                });
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });

    $('body').on('click', '.js-edit-forum-answer', function (e) {
        e.preventDefault();

        const $this = $(this);
        let action = $this.attr('data-action');

        loadingSwl();

        $.get(action, function (result) {
            if (result && result.answer) {
                const rand = randomString();

                let clone = $('#editQuestionAnswerModal').clone();
                let copyHtml = clone.prop('innerHTML');
                copyHtml = copyHtml.replaceAll('record', rand);
                clone.html('<div id="editAnswerModal">' + copyHtml + '</div>');

                Swal.fire({
                    html: clone.html(),
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                    onOpen: function () {
                        const modal = $('#editAnswerModal');

                        const form = modal.find('form');
                        const formAction = action.replaceAll('edit', 'update');

                        form.attr('action', formAction);

                        Object.keys(result.answer).forEach((key) => {
                            const value = result.answer[key];
                            let element = form.find('[name="' + key + '"]');

                            if (element && element.length) {
                                element.val(value);
                            }
                        });
                    }
                });
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });


    $('body').on('click', '.question-forum-pin-btn, .js-btn-answer-un_pin, .js-btn-answer-pin, .js-btn-answer-mark_as_not_resolved, .js-btn-answer-mark_as_resolved', function (e) {
        e.preventDefault();
        const $this = $(this);

        const action = $this.attr('data-action');

        loadingSwl();

        $.post(action, {}, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + changesSavedSuccessfullyLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                Swal.fire(oopsLang, somethingWentWrongLang, 'error');
            }
        }).fail(err => {
            Swal.fire(oopsLang, somethingWentWrongLang, 'error');
        });
    });
})(jQuery);

(function ($) {
    "use strict";

    // *******************
    // create
    // *****************

    $('body').on('click', '#add_multiple_question', function (e) {
        e.preventDefault();
        const quizId = $(this).attr('data-quiz-id');

        var multipleQuestionModal = $('.multipleQuestionModal' + quizId);
        var clone = multipleQuestionModal.clone();
        var id = 'correctAnswerSwitch' + randomString();
        clone.find('label.js-switch').attr('for', id);
        clone.find('input.js-switch').attr('id', id);

        const random_id = randomString();
        clone.find('.panel-file-manager').attr('data-input', random_id);

        clone.find('.main-answer-row').removeClass('main-answer-row').addClass('main-answer-box');

        let copyHtml = clone.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', random_id);
        copyHtml = copyHtml.replaceAll('ans_tmp', 'ans_temp');
        clone.html(copyHtml);

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '.add-answer-btn', function (e) {
        e.preventDefault();
        var mainRow = $('.add-answer-container .main-answer-box');

        var copy = mainRow.clone();
        copy.removeClass('main-answer-box');
        copy.find('.answer-remove').removeClass('d-none');

        const id = 'correctAnswerSwitch' + randomString();
        copy.find('label.js-switch').attr('for', id);
        copy.find('input.js-switch').attr('id', id);

        copy.find('input[type="checkbox"]').prop('checked', false);

        var copyHtml = copy.prop('innerHTML');
        const nameId = randomString();
        copyHtml = copyHtml.replaceAll('ans_temp', nameId);
        copyHtml = copyHtml.replace(/\[\d+\]/g, '[' + nameId + ']');
        copy.html(copyHtml);
        copy.find('input[type="checkbox"]').prop('checked', false);
        copy.find('input[type="text"]').val('');
        mainRow.parent().append(copy);
    });

    $('body').on('click', '.answer-remove', function (e) {
        e.preventDefault();
        $(this).closest('.add-answer-card').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }


    $('body').on('click', '#add_descriptive_question', function (e) {
        e.preventDefault();
        const quizId = $(this).attr('data-quiz-id');

        var multipleQuestionModal = $('.descriptiveQuestionModal' + quizId);
        var clone = multipleQuestionModal.clone();

        const random_id = randomString();

        let copyHtml = clone.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', random_id);
        copyHtml = copyHtml.replaceAll('ans_tmp', 'ans_temp');
        clone.html(copyHtml);

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('change', '.js-switch', function () {
        const $this = $(this);
        const parent = $this.closest('.js-switch-parent');

        if (this.checked) {
            $('.js-switch').each(function () {
                const switcher = $(this);
                const switcher_parent = switcher.closest('.js-switch-parent');
                const switcher_input = switcher_parent.find('input[type="checkbox"]');
                switcher_input.prop('checked', false);
            });

            $this.prop('checked', true);
        }
    });

    $('body').on('click', '.save-question', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.quiz-questions-form');
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
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
                    let element = form.find('.js-ajax-' + key);
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });

    $('body').on('click', '.js-submit-quiz-form', function (e) {
        e.preventDefault();
        const $this = $(this);

        let form = $this.closest('.quiz-form');
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + saveSuccessLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    if (result.redirect_url && result.redirect_url !== '') {
                        window.location.href = result.redirect_url;
                    } else {
                        window.location.reload();
                    }
                }, 2000)
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
        });
    });

    // *******************
    // edit
    // *****************

    $('body').on('click', '.edit_question', function (e) {
        e.preventDefault();
        const $this = $(this);
        const question_id = $this.attr('data-question-id');

        loadingSwl();

        $.get(adminPanelPrefix + '/quizzes-questions/' + question_id + '/edit', function (result) {
            if (result && result.html) {
                let $html = '<div id="editQuestion">' + result.html + '</div>';
                Swal.fire({
                    html: $html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        const editModal = $('#editQuestion');
                        editModal.find('.main-answer-row').removeClass('main-answer-row').addClass('main-answer-box');

                        const random_id = randomString();
                        editModal.find('.admin-file-manager').first().attr('data-input', random_id);
                        editModal.find('.lfm-input').first().attr('id', random_id);

                        const id = 'correctAnswerSwitch' + randomString();
                        editModal.find('label.js-switch').first().attr('for', id);
                        editModal.find('input.js-switch').first().attr('id', id);
                    }
                });
            }
        })
    });


    $('body').on('change', '.js-quiz-question-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.quiz-questions-form');
        const locale = $this.val();
        const item_id = $this.attr('data-id');

        $this.addClass('loadingbar gray');

        const path = adminPanelPrefix + '/quizzes-questions/' + item_id + '/getQuestionByLocale?locale=' + locale;

        $.get(path, function (result) {
            const question = result.question;

            if (question.type === 'descriptive') {
                const fields = ['title', 'correct'];

                Object.keys(question).forEach(function (key) {
                    const value = question[key];

                    if ($.inArray(key, fields) !== -1) {
                        let element = $form.find('.js-ajax-' + key);
                        element.val(value);
                    }
                });
            } else {

                $form.find('.js-ajax-title').val(question.title);

                if (question.quizzes_questions_answers && question.quizzes_questions_answers.length) {
                    var answers = question.quizzes_questions_answers;

                    for (let answer of answers) {
                        if (answer) {
                            $form.find('.js-ajax-answer-title-' + answer.id).val(answer.title);
                        }
                    }
                }
            }

            $this.removeClass('loadingbar gray');
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });

    });

    $('body').on('change', '.js-ajax-display_limited_questions', function () {
        const $input = $('.js-display-limited-questions-count-field');

        $input.find('input').val('');

        if (this.checked) {
            $input.removeClass('d-none');
        } else {
            $input.addClass('d-none');
        }
    })

    $(document).ready(function () {
        const style = getComputedStyle(document.body);
        const primaryColor = style.getPropertyValue('--primary');

        function updateToDatabase(table, quizId, idString) {
            $.post(adminPanelPrefix + '/quizzes/'+ quizId +'/order-items', {table: table, items: idString}, function (result) {
                if (result && result.title && result.msg) {
                    $.toast({
                        heading: result.title,
                        text: result.msg,
                        bgColor: primaryColor,
                        textColor: 'white',
                        hideAfter: 10000,
                        position: 'bottom-right',
                        icon: 'success'
                    });
                }
            });
        }

        function setSortable(target) {
            if (target.length) {
                target.sortable({
                    group: 'no-drop',
                    handle: '.move-icon',
                    axis: "y",
                    update: function (e, ui) {
                        var sortData = target.sortable('toArray', {attribute: 'data-id'});
                        var table = e.target.getAttribute('data-order-table');
                        var quizId = e.target.getAttribute('data-quiz');

                        updateToDatabase(table, quizId, sortData.join(','))
                    }
                });
            }
        }

        const items = [];

        var draggableContentLists = $('.draggable-questions-lists');
        if (draggableContentLists.length) {
            for (let item of draggableContentLists) {
                items.push($(item).attr('data-drag-class'))
            }
        }

        if (items.length) {
            for (let item of items) {
                const tag = $('.' + item);

                if (tag.length) {
                    setSortable(tag);
                }
            }
        }
    })

    /* feather icons */
    // **
    // **
    feather.replace();
})(jQuery);

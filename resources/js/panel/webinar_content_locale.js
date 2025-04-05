(function ($) {
    "use strict";

    $('body').on('change', '.js-webinar-content-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.js-content-form');
        const locale = $this.val();
        const webinarId = $this.attr('data-webinar-id');
        const item_id = $this.attr('data-id');
        const relation = $this.attr('data-relation');
        let fields = $this.attr('data-fields');
        fields = fields.split(',');


        $this.addClass('loadingbar gray');

        const path = '/panel/webinars/' + webinarId + '/getContentItemByLocale';
        const data = {
            item_id,
            locale,
            relation
        };

        $.post(path, data, function (result) {
            if (result && result.item) {
                const item = result.item;

                Object.keys(item).forEach(function (key) {
                    const value = item[key];

                    if ($.inArray(key, fields) !== -1) {
                        let element = $form.find('.js-ajax-' + key);
                        element.val(value);
                    }

                    if (relation === 'textLessons' && key === 'content') {
                        var summernoteTarget = $form.find('.js-content-' + item_id);

                        if (summernoteTarget.length) {
                            summernoteTarget.summernote('destroy');


                            summernoteTarget.val(value);
                            $('.js-hidden-content-' + item_id).val(value);


                            if (jQuery().summernote) {
                                makeSummernote(summernoteTarget, 400, function (contents, $editable) {
                                    $('.js-hidden-content-' + item_id).val(contents);
                                })
                            }
                        }
                    }
                });

                $this.removeClass('loadingbar gray');
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });
    });

    $('body').on('change', '.js-quiz-question-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.quiz-questions-form');
        const locale = $this.val();
        const item_id = $this.attr('data-id');

        $this.addClass('loadingbar gray');

        const path = '/panel/quizzes-questions/' + item_id + '/getQuestionByLocale?locale=' + locale;

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
})(jQuery);

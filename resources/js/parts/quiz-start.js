(function ($) {
    "use strict";

    if (jQuery().startTimer) {
        $('.timer').startTimer({
            onComplete: function (element) {
                element.addClass('text-danger');
                $('.quiz-form form').trigger('submit');
            }
        });
    }

    var question_step = 1;
    var current_fs, next_fs, previous_fs; //fieldsets
    var opacity;

    function handleButtonDisable() {
        const question_count = $('.js-quiz-question-count').val();

        if (question_step >= question_count) {
            $(".next").prop('disabled', true);
        } else {
            $(".next").prop('disabled', false);
        }

        if (question_step <= 1) {
            $(".previous").prop('disabled', true);
        } else {
            $(".previous").prop('disabled', false);
        }
    }

    $("body").on('click', '.next', function () {
        current_fs = $('.question-step-' + question_step);
        next_fs = $('.question-step-' + (question_step + 1));

        if (next_fs.length < 1) {
            return
        }

        next_fs.show();

        current_fs.animate({opacity: 0}, {
            step: function (now) {
                opacity = 1 - now;
                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                next_fs.css({'opacity': opacity});
            },
            duration: 600
        });

        question_step += 1;

        handleButtonDisable();
    });

    $("body").on('click', '.previous', function () {
        current_fs = $('.question-step-' + question_step);
        previous_fs = $('.question-step-' + (question_step - 1));

        if (previous_fs.length < 1) {
            return
        }

        previous_fs.show();


        current_fs.animate({opacity: 0}, {
            step: function (now) {
                opacity = 1 - now;
                current_fs.css({
                    'display': 'none',
                    'position': 'relative'
                });
                previous_fs.css({'opacity': opacity});
            },
            duration: 600
        });

        question_step--;

        handleButtonDisable();
    });

    let videoOptions = {
        autoplay: false,
        preload: 'auto',
    };

    var videoItems = $('.video-js');

    if (videoItems.length) {
        videoItems.each(function (key) {
            var el = videoItems[key];

            var id = $(el).attr('id');

            videojs(id, videoOptions);
        });
    }

})(jQuery);

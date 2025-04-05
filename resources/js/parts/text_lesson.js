(function ($) {
    "use strict";



    $('body').on('change', '#readLessonSwitch', function (e) {
        e.preventDefault();

        const $this = $(this);
        const course_id = $this.attr('data-course-id');
        const lesson_id = $this.attr('data-lesson-id');
        const status = this.checked;

        $this.addClass('loadingbar primary').prop('disabled', true);

        const data = {
            item: 'text_lesson_id',
            item_id: lesson_id,
            status: status
        };

        $.post('/course/' + course_id + '/learningStatus', data, function (result) {
            $.toast({
                heading: '',
                text: learningToggleLangSuccess,
                bgColor: '#43d477',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'success'
            });
        }).catch(err => {
            $.toast({
                heading: '',
                text: learningToggleLangError,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: 'error'
            });
        });
    })
})(jQuery);

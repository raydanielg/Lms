(function ($) {
    "use strict";

    $('body').on('change', 'select[name="webinar_id"]', function (e) {
        e.preventDefault();

        const quizFilter = $('#quizFilter');
        $('#quizFilter option').each((key, element) => {
            if ($(element).val() !== 'all') {
                $(element).addClass('d-none');

                quizFilter.prop('disabled', true);

                if ($(element).attr('data-webinar-id') === $(this).val()) {
                    $(element).removeClass('d-none');
                    quizFilter.prop('disabled', false);
                }
            }
        })
    })
})(jQuery);

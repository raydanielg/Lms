(function ($) {
    "use strict";

    $('body').on('change', 'select[name="role"]', function (e) {
        const organizationInputs = $('.js-organization-inputs');
        const instructorInputs = $('.js-instructor-inputs');
        const value = $(this).val();

        organizationInputs.addClass('d-none');
        instructorInputs.addClass('d-none');

        if (value === 'organizations') {
            organizationInputs.removeClass('d-none');
        } else if (value === 'instructors') {
            instructorInputs.removeClass('d-none');
        }
    });

})(jQuery);

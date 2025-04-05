(function ($) {
    "use strict";

    $("#conversationsCard").animate({scrollTop: $('#conversationsCard').height()}, "slow");

    $('body').on('change', '#userRole', function (e) {
        e.preventDefault();

        const studentSelectInput = $('#studentSelectInput');
        const teacherSelectInput = $('#teacherSelectInput');
        const val = $(this).val();

        studentSelectInput.find('select').val('');
        teacherSelectInput.find('select').val('');

        if (val === 'student') {
            studentSelectInput.removeClass('d-none');
            teacherSelectInput.addClass('d-none');
        } else if (val === 'teacher') {
            teacherSelectInput.removeClass('d-none');
            studentSelectInput.addClass('d-none');
        } else {
            teacherSelectInput.addClass('d-none');
            studentSelectInput.addClass('d-none');
        }
    });

    $('body').on('change', '#supportType', function (e) {
        const value = $(this).val();

        $('#courseInput,#departmentInput').addClass('d-none');

        if (value === 'course_support') {
            $('#courseInput').removeClass('d-none');
            panelSearchWebinarSelect2();
        } else if (value === 'platform_support') {
            $('#departmentInput').removeClass('d-none');
        }
    })

})(jQuery);

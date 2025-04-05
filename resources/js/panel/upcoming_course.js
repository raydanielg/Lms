(function ($) {
    "use strict";


    $('body').on('change', '#onlyReleasedSwitch', function (e) {
        e.preventDefault();

        $(this).closest('form').trigger('submit');
    });

    $('body').on('click', '.js-mark-as-released', function (e) {
        e.preventDefault();
        const $this = $(this);
        const upcomingId = $this.attr('data-id');

        const path = `/panel/upcoming_courses/${upcomingId}/assign-course`;

        loadingSwl();

        $.get(path, function (result) {
            if (result.code === 200) {
                Swal.fire({
                    html: result.html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left'
                    },
                    width: '32rem',
                    onOpen: function () {
                        $(".js-select2").select2({
                            width: '100%',
                            dropdownParent: $('#upcomingAssignCourseModal')
                        });
                    }
                });
            }
        })
    });

    $('body').on('click', '.js-save-assign-course', function (e) {
        e.preventDefault();
        const $this = $(this);

        let form = $this.closest('#upcomingAssignCourseModal');
        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + result.msg + '</h3>',
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


})(jQuery);

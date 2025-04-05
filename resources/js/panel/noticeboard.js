(function ($) {
    "use strict";


    if (jQuery().summernote) {
        makeSummernote($('.summernote'), 400)
    }

    $('body').on('click', '#submitForm', function (e) {
        e.preventDefault();
        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-16 text-center text-dark-blue">' + noticeboard_success_send + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.href = result.redirectTo ? result.redirectTo : '/panel/noticeboard';
                }, 2000)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });

    $('body').on('click', '.js-view-message', function (e) {
        const $this = $(this);

        const card = $this.closest('.noticeboard-item');
        const title = card.find('.js-noticeboard-title').text();
        const time = card.find('.js-noticeboard-time').text();
        const message = card.find('.js-noticeboard-message').val();

        const modal = $('#noticeboardMessageModal');
        modal.find('.modal-title').text(title);
        modal.find('.modal-time').text(time);
        modal.find('.modal-message').html(message);


        Swal.fire({
            html: modal.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });
    });

    $('body').on('change', '#typeSelect', function (e) {
        e.preventDefault();

        const value = this.value;
        const instructorCourses = $('#instructorCourses');
        instructorCourses.addClass('d-none');

        if (value === 'course') {
            instructorCourses.removeClass('d-none');
        }
    });

})(jQuery);

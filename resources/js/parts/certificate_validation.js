(function ($) {
    "use strict";


    $('body').on('click', '#formSubmit', function (e) {
        e.preventDefault();
        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 404) {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + certificateNotFound + '</h3>',
                    showConfirmButton: true,
                    showCancelButton: false,
                    confirmButtonText: close,
                    confirmButtonColor: '#f63c3c',
                    dangerMode: true
                });
            } else if (result && result.code === 200) {
                const certificate = result.certificate;

                $('#certificateModal .modal-student').text(certificate.student);
                $('#certificateModal .modal-webinar').text(certificate.webinar_title);
                $('#certificateModal .modal-date').text(certificate.date);

                let modal = '<div id="validCertificateModal">';
                modal += $('#certificateModal').html();
                modal += '</div>';

                Swal.fire({
                    html: modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '36rem',
                });
            }
        }).fail(err => {
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        }).always(() => {
            refreshCaptcha();
            $this.removeClass('loadingbar primary').prop('disabled', false);
        })
    })
})(jQuery);

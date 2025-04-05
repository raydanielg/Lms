(function () {
    "use strict";

    $('body').on('click', '.js-update-btn', function () {
        const $this = $(this);
        const $form = $this.closest('form');
        const path = $form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        $form.find('input').removeClass('is-invalid');

        const file = $form.find('input[type="file"]')[0].files[0];
        let formData = new FormData();
        formData.append("file", file);

        const items = $form.find('input, textarea, select').serializeArray();

        $.each(items, function () {
            formData.append(this.name, this.value);
        });

        $form.find('.progress').addClass('d-none');

        $.ajax({
            type: 'POST',
            url: path,
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            xhr: function () {
                const xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = ((evt.loaded / evt.total) * 100) - 1;

                        $form.find('.progress').removeClass('d-none');
                        const bar = $form.find('.progress .progress-bar');

                        bar.css("width", percentComplete + '%');
                        bar.text(percentComplete + '%');
                    }
                }, false);

                return xhr;
            },
            success: function (result) {
                if (result && result.code === 200) {
                    //window.location.reload();
                    Swal.fire({
                        icon: 'success',
                        html: '<p class="font-16 font-weight-500 text-center text-dark-blue py-25">' + result.msg + '</p>',
                        showConfirmButton: false,
                        width: '25rem',
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            },
            error: function (err) {
                $form.find('.progress').addClass('d-none');
                $this.removeClass('loadingbar primary').prop('disabled', false);
                var errors = err.responseJSON;

                if (errors && errors.errors) {
                    Object.keys(errors.errors).forEach((key) => {
                        const error = errors.errors[key];
                        let element = $form.find('.js-ajax-' + key);

                        element.addClass('is-invalid');
                        element.parent().find('.invalid-feedback').text(error[0]);
                    });
                }
            }
        });
    })

    $('body').on('click', '.js-database-update-btn', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const path = $form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        $form.find('input').removeClass('is-invalid');

        const data = $form.serializeObject();

        $.post(path, data, function (result) {
            if (result.code === 200) {
                $('.js-database-update-message').html(result.message);
            }

            $this.removeClass('loadingbar primary').prop('disabled', false);
        }).fail(err => {
            $form.find('.progress').addClass('d-none');
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('.js-ajax-' + key);

                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    })

})(jQuery)

(function ($) {
    "use strict";

    function handleSelect2() {
        const $select = $('.related-course-select2');

        $select.select2({
            placeholder: $select.data('placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: adminPanelPrefix + '/webinars/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        webinar_id: $select.data('webinar-id')
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.title,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    }

    function getModalByPath(path) {
        loadingSwl();

        $.get(path, function (result) {
            if (result.code === 200) {

                Swal.fire({
                    html: result.html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '32rem',
                    onOpen: () => {
                        handleSelect2()
                    }
                });
            }
        })
    }

    $('body').on('click', '#addRelatedCourse', function (e) {
        e.preventDefault();
        const path = $(this).attr('data-path')

        getModalByPath(path)
    });

    $('body').on('click', '.js-edit-related-course', function (e) {
        e.preventDefault();
        const path = $(this).attr('data-path')

        getModalByPath(path)
    })


    $('body').on('click', '#saveRelateCourse', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.js-related-course-form');

        let data = serializeObjectByTag(form);
        let action = form.attr('data-action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                //window.location.reload();
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + result.title + '</h3>',
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

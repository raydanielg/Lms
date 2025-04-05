(function () {
    "use strict";

    $('body').on('click', '#addStudentToCourse', function (e) {
        e.preventDefault();

        let html = '<div id="addStudentToCourseModalSwl">';
        html += $('#addStudentToCourseModal').html();
        html += '</div>';
        html = html.replaceAll('user-search', 'user-search2');

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
            onOpen: () => {
                $('.user-search2').select2({
                    placeholder: $(this).data('placeholder'),
                    minimumInputLength: 3,
                    allowClear: true,
                    ajax: {
                        url: adminPanelPrefix + '/users/search',
                        dataType: 'json',
                        type: "POST",
                        quietMillis: 50,
                        data: function (params) {
                            return {
                                term: params.term,
                                option: $(this).attr('data-search-option'),
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.name,
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });
            }
        });
    });

    $('body').on('click', '.js-save-manual-add', function (e) {
        e.preventDefault();

        e.preventDefault();
        var $this = $(this);
        var form = $this.closest('form');
        var data = form.serializeObject();
        var action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);

        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem'
                });

                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }
        }).fail(function (err) {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach(function (key) {
                    var error = errors.errors[key];
                    var element = form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        });
    });
})(jQuery);

(function ($) {
    "use strict";

    $('body').on('click', '.js-webinar-next-session', function (e) {
        e.preventDefault();

        const $this = $(this);
        const webinarId = $this.attr('data-webinar-id');

        loadingSwl();

        $.get('/panel/webinars/' + webinarId + '/getNextSessionInfo', function (result) {
            if (result && result.code === 200) {
                const session = result.session;

                let mainRow = $('#webinarNextSessionModal').clone();
                mainRow = mainRow.prop('innerHTML');
                if (session !== null) {
                    mainRow = mainRow.replace(/\[new\]/g, '[' + session.id + ']');
                }

                const $html = '<div id="webinarNextSessionHtml">' + mainRow + '</div>';

                Swal.fire({
                    html: $html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {

                        let $modal = $('#webinarNextSessionHtml');
                        let webinar_id_input_key = 'new';

                        if (session !== null) {
                            $modal.find('form').attr('action', '/panel/sessions/' + session.id + '/update');

                            Object.keys(session).forEach(key => {
                                $modal.find('[name="ajax[' + session.id + '][' + key + ']"]').val(session[key]);
                            });

                            $modal.find('[name="ajax[' + session.id + '][status]"]').val((session.status === 'active') ? 'on' : null);

                            if (session.agora_settings) {
                                $modal.find('[name="ajax[' + session.id + '][agora_chat]"]').val((session.agora_settings.chat) ? 'on' : null);
                                $modal.find('[name="ajax[' + session.id + '][agora_rec]"]').val((session.agora_settings.record) ? 'on' : null);
                            }

                            webinar_id_input_key = session.id;
                        }

                        $modal.find('[name="ajax[' + webinar_id_input_key + '][webinar_id]"]').val(result.webinar_id);

                        if (session && session.session_api !== 'local') {
                            $modal.find('[name="ajax[' + session.id + '][session_api]"]').prop('disabled', true);
                            $modal.find('[name="ajax[' + session.id + '][api_secret]"]').prop('disabled', true);
                            $modal.find('[name="ajax[' + session.id + '][link]"]').prop('disabled', true);
                            $modal.find('[name="ajax[' + session.id + '][date]"]').prop('disabled', true);
                            $modal.find('[name="ajax[' + session.id + '][duration]"]').prop('disabled', true);
                        }

                        if (session && session.session_api === 'big_blue_button') {
                            $modal.find('.js-moderator-secret').removeClass('d-none');
                            $modal.find('[name="ajax[' + session.id + '][moderator_secret]"]').prop('disabled', true);
                        } else {
                            $modal.find('.js-moderator-secret').addClass('d-none');
                        }

                        if (session && (session.session_api === 'zoom' || session.session_api === 'agora' || session.session_api === 'jitsi')) {
                            $modal.find('.js-api-secret').addClass('d-none');
                        }

                        var $chapterSelection = $modal.find('.js-ajax-chapter_id');
                        if (result && result.chapters) {
                            var html = '<option value="">' + selectChapterLang + '</option>';
                            Object.keys(result.chapters).forEach(key => {
                                const chapter = result.chapters[key];

                                html += '<option value="' + chapter.id + '">' + chapter.title + '</option>';
                            });

                            $chapterSelection.html(html);
                        }

                        resetDatePickers();
                    }
                });
            }
        }).fail((err) => {
            Swal.fire({
                icon: 'error',
                html: '<h3 class="font-20 text-center text-dark-blue">' + undefinedActiveSessionLang + '</h3>',
                showConfirmButton: false,
            });
        })
    });

    $('body').on('click', '.js-save-next-session', function (e) {
        e.preventDefault();

        const $this = $(this);
        let form = $this.closest('form');

        let data = serializeObjectByTag(form);
        let action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                //window.location.reload();
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
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


    $('body').on('change', '.js-ajax-session_api', function (e) {
        e.preventDefault();

        const sessionForm = $(this).closest('form');
        const value = this.value;

        if (value === 'big_blue_button') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').removeClass('d-none');
            sessionForm.find('.js-moderator-secret').removeClass('d-none');
        } else if (value === 'zoom') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').addClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        } else if (value === 'agora') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').addClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        } else if (value === 'jitsi') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').addClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        } else {
            sessionForm.find('.js-local-link').removeClass('d-none');
            sessionForm.find('.js-api-secret').removeClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        }
    });

    $('body').on('change', '#conductedSwitch', function (e) {
        e.preventDefault();
        $(this).closest('form').trigger('submit');
    });

    $('body').on('change', '#freeClassesSwitch', function (e) {
        e.preventDefault();
        $(this).closest('form').trigger('submit');
    });
})(jQuery);

(function ($) {
    "use strict";

    $('body').on('click', '.add-meeting-url', function (e) {
        e.preventDefault();
        const item_id = $(this).attr('data-item-id');
        const meeting_password = $('.js-meeting-password-' + item_id).val();
        const meeting_link = $('.js-meeting-link-' + item_id).val();


        const $modalHtml = $('#liveMeetingLinkModal');

        Swal.fire({
            html: '<div id="meetingLinkModal">' + $modalHtml.html() + '</div>',
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                const editModal = $('#meetingLinkModal');

                editModal.find('input[name="item_id"]').val(item_id);
                editModal.find('input[name="password"]').val(meeting_password);
                editModal.find('input[name="link"]').val(meeting_link);
            }
        });
    });

    $('body').on('click', '.js-save-meeting-link', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + linkSuccessAdd + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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

    $('body').on('click', '.js-add-meeting-session', function (e) {
        e.preventDefault();
        const item_id = $(this).attr('data-item-id');
        const itemDate = $(this).attr('data-date');


        const $modalHtml = $('#meetingCreateSessionModal');

        Swal.fire({
            html: '<div id="meetingSessionModal">' + $modalHtml.html() + '</div>',
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '42rem',
            onOpen: () => {
                const editModal = $('#meetingSessionModal');

                editModal.find('input[name="item_id"]').val(item_id);
                editModal.find('.js-meeting-date').text(itemDate);
                editModal.find('.js-create-meeting-session').removeClass('d-none');
                editModal.find('.js-create-meeting-session').attr('data-item-id', item_id);

                editModal.find('.js-join-to-session').addClass('d-none');

                editModal.find('.js-for-create-session-text').removeClass('d-none');
                editModal.find('.js-for-join-session-text').addClass('d-none');
            }
        });
    });

    $('body').on('click', '.js-create-meeting-session', function (e) {
        e.preventDefault();

        const $this = $(this);
        const item_id = $this.attr('data-item-id');
        const action = `/panel/meetings/${item_id}/add-session`;

        $this.addClass('loadingbar primary').prop('disabled', true);

        const successHtml = `<div class="">
                <h3 class="font-16 text-center text-dark-blue">${sessionSuccessAdd}</h3>
                <p class="mt-5 font-14 text-gray">${youCanJoinTheSessionNowLang}</p>
            </div>`;

        $.post(action, {}, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: successHtml,
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
        });
    });

})(jQuery);

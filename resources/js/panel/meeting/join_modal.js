(function ($) {
    "use strict";

    $('body').on('click', '.js-join-reserve', function (e) {
        e.preventDefault();

        const reserve_id = $(this).attr('data-reserve-id');
        const meeting_password = $('.js-meeting-password-' + reserve_id).val();
        const meeting_link = $('.js-meeting-link-' + reserve_id).val();


        const $modalHtml = $('#joinMeetingLinkModal');

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

                editModal.find('.js-join-meeting-link').attr('href', '/panel/meetings/' + reserve_id + '/join');
                editModal.find('input[name="password"]').val(meeting_password);
                editModal.find('input[name="link"]').val(meeting_link);
            }
        });
    });
})(jQuery);

(function ($) {
    "use strict";

    $('body').on('click', '.js-show-join-modal', function (e) {
        e.preventDefault();

        const parent = $(this).closest('td');

        const reserve_id = $(this).attr('data-reserve-id');
        const password = parent.find('.js-meeting-password').val();
        const link = parent.find('.js-meeting-link').val();

        const $modal = $('#joinModal');
        $modal.find('.js-join-btn').attr('href', adminPanelPrefix + '/appointments/' + reserve_id + '/join');

        $modal.find('input[name="password"]').val(password);
        $modal.find('input[name="link"]').val(link);

        $modal.modal('show')
    });

    $('body').on('click', '.js-send-reminder', function (e) {
        e.preventDefault();
        const reserve_id = $(this).attr('data-reserve-id');

        const $modal = $('#sendReminderModal');

        $.get(adminPanelPrefix + '/appointments/' + reserve_id + '/getReminderDetails', function (result) {

            $modal.find('.js-send-reminder-btn').attr('href', adminPanelPrefix + '/appointments/' + reserve_id + '/sendReminder');
            $modal.find('.js-consultant').html(result.consultant);
            $modal.find('.js-reservatore').html(result.reservatore);
            $modal.find('.js-title').html(result.title);
            $modal.find('.js-message').html(result.content);

            $modal.modal('show');
        });
    });
})(jQuery);

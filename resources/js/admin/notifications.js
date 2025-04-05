(function ($) {
    "use strict";

    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();
        const item_id = $(this).attr('data-item-id');
        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#notificationMessageModal');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');

        $.get(adminPanelPrefix + '/notifications/' + item_id + '/mark_as_read', function (result) {

        });
    });

    $('body').on('change', '#typeSelect', function (e) {
        e.preventDefault();

        const value = this.value;
        const userSelect = $('#userSelect');
        const groupSelect = $('#groupSelect');
        const webinarSelect = $('#webinarSelect');

        userSelect.addClass('d-none');
        groupSelect.addClass('d-none');
        webinarSelect.addClass('d-none');

        if (value === 'single') {
            userSelect.removeClass('d-none');
        } else if (value === 'group') {
            groupSelect.removeClass('d-none');
        } else if (value === 'course_students') {
            webinarSelect.removeClass('d-none');
        }
    });
})(jQuery);

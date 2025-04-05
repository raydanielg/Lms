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
})(jQuery);

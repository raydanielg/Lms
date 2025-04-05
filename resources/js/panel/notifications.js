(function ($) {
    "use strict";

    $('body').on('click', '.js-show-message', function (e) {
        e.preventDefault();

        const $this = $(this);
        const notification_id = $this.attr('data-id');

        const card = $this.closest('.notification-card');
        const title = card.find('.notification-title').text();
        const time = card.find('.notification-time').text();
        const message = card.find('.notification-message').val();

        const modal = $('#messageModal');
        modal.find('.modal-title').text(title);
        modal.find('.modal-time').text(time);
        modal.find('.modal-message').html(message);


        Swal.fire({
            html: modal.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });

        if (!$this.hasClass('seen-at')) {
            $.get('/panel/notifications/' + notification_id + '/saveStatus', function () {
                $this.addClass('seen-at');
                card.find('.notification-badge').remove();
            })
        }
    });
})(jQuery);

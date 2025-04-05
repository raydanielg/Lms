(function ($) {
    "use strict";

    $('body').on('click', '.join-purchase-webinar', function (e) {
        e.preventDefault();

        const $this = $(this);
        const webinarId = $this.attr('data-webinar-id');

        loadingSwl();

        const data = {
            webinar_id: webinarId
        };

        $.post('/panel/webinars/purchases/getJoinInfo', data, function (result) {
            if (result && result.code === 200) {
                const session = result.session;
                const $html = '<div id="joinWebinarModalHtml">' + $('#joinWebinarModal').html() + '</div>';

                Swal.fire({
                    html: $html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        let $modal = $('#joinWebinarModalHtml');

                        Object.keys(session).forEach(key => {
                            $modal.find('.js-join-session-' + key).val(session[key]);
                        });

                        $modal.find('.js-join-session-link-action').attr('href', session.link);
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

    $('body').on('change', '#conductedSwitch', function (e) {
        e.preventDefault();
        $(this).closest('form').trigger('submit');
    });

})(jQuery);

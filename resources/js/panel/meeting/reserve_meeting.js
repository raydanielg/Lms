(function ($) {
    "use strict";

    $('body').on('click', '.js-finish-meeting-reserve', function (e) {
        e.preventDefault();

        const reserve_id = $(this).attr('data-id');
        const action = '/panel/meetings/' + reserve_id + '/finish';

        var html = '<div class="">\n' +
            '    <p class="">' + finishReserveHint + '</p>\n' +
            '    <div class="mt-30 d-flex align-items-center justify-content-center">\n' +
            '        <button type="button" id="finishReserve" data-href="' + action + '" class="btn btn-sm btn-primary">' + finishReserveConfirm + '</button>\n' +
            '        <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + finishReserveCancel + '</button>\n' +
            '    </div>\n' +
            '</div>';

        Swal.fire({
            title: finishReserveTitle,
            html: html,
            icon: 'warning',
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: () => !Swal.isLoading(),
        });
    });

    $('body').on('click', '#finishReserve', function (e) {
        e.preventDefault();
        var $this = $(this);
        const href = $this.attr('data-href');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(href, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    title: finishReserveSuccess,
                    text: finishReserveSuccessHint,
                    showConfirmButton: false,
                    icon: 'success',
                });
                setTimeout(() => {

                    if (typeof result.redirect_to !== "undefined" && result.redirect_to !== undefined && result.redirect_to !== null && result.redirect_to !== '') {
                        window.location.href = result.redirect_to;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                Swal.fire({
                    title: finishReserveFail,
                    text: finishReserveFailHint,
                    icon: 'error',
                })
            }
        }).error(err => {
            Swal.fire({
                title: finishReserveFail,
                text: finishReserveFailHint,
                icon: 'error',
            })
        }).always(() => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
        });
    })

    $('body').on('click', '.js-join-meeting-session', function (e) {
        e.preventDefault();
        const item_id = $(this).attr('data-item-id');
        const itemDate = $(this).attr('data-date');
        const href = $(this).attr('data-link');


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
                editModal.find('.js-create-meeting-session').addClass('d-none');
                editModal.find('.js-join-to-session').removeClass('d-none');

                editModal.find('.js-for-create-session-text').addClass('d-none');
                editModal.find('.js-for-join-session-text').removeClass('d-none');

                editModal.find('.js-join-to-session').attr('href', href);
            }
        });
    });

})(jQuery);

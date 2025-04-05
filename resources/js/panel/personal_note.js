(function ($) {
    "use strict";

    $('body').on('click', '.js-show-note', function (e) {
        e.preventDefault();
        const $this = $(this);
        const note = $this.parent().find('input').val();

        const html = '<div class="">' +
            '<h3 class="section-title after-line">' + noteLang + '</h3>' +
            '<p class="text-gray mt-20">' + note + '</p>' +
            '</div>';

        Swal.fire({
            html: html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '40rem',
        });


    });

})(jQuery);

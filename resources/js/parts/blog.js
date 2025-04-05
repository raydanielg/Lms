(function ($) {
    "use strict";

    function copyToClipboard() {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.css('position', 'absolute');

        $temp.val($('.js-blog-share-link').html()).select();
        document.execCommand("copy");
        $temp.remove();
    }


    $('body').on('click', '.js-blog-share-link-copy', function (e) {
        e.preventDefault();

        $(this).attr('data-original-title', copiedLang)
            .tooltip('show');

        $(this).attr('data-original-title', copyLang);

        copyToClipboard();
    });


    $('body').on('click', '.js-share-blog', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#blogShareModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            onOpen: () => {
                $('[data-toggle="tooltip"]').tooltip();
            },
            width: '32rem',
        });
    });

})(jQuery);

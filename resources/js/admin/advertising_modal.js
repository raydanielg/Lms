(function ($) {
    "use strict";

    function makeHtml(title, image, description, button1, button2) {
        return `<div id="advertisingModalSettings">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="section-title font-20 text-dark-blue mb-10">${title ?? ''}</h3>

            <button type="button" class="btn-close-advertising-modal close-swl btn-transparent d-flex">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="d-flex align-items-center justify-content-center">
            <img src="${image ?? ''}" class="img-fluid rounded-lg" alt="">
        </div>

        <p class="font-14 text-gray mt-20">${description ?? ''}</p>

        <div class="row align-items-center mt-20">
            ${
            (button1 && button1.link && button1.title) ?
                `<div class="col-6">
                    <a href="${button1.link}" class="btn btn-primary btn-block">${button1.title}</a>
                    </div>` : ''
        }


            ${
            (button2 && button2.link && button2.title) ?
                `<div class="col-6">
                        <a href="${button2.link}" class="btn btn-primary btn-block">${button2.title}</a>
                        </div>` : ''
        }
        </div>
    </div>`;
    }

    $('body').on('click', '.js-preview-modal', function () {

        const title = $('input[name="value[title]"]').val();
        const image = $('input[name="value[image]"]').val();
        let description = $('textarea[name="value[description]"]').val();

        description = (description + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');

        const button1 = {link: '', title: ''};
        button1.title = $('input[name="value[button1][title]"]').val();
        button1.link = $('input[name="value[button1][link]"]').val();

        const button2 = {link: '', title: ''};
        button2.title = $('input[name="value[button2][title]"]').val();
        button2.link = $('input[name="value[button2][link]"]').val();


        Swal.fire({
            html: makeHtml(title, image, description, button1, button2),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '36rem',

        });
    });
})(jQuery);

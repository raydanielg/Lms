(function ($) {
    "use strict";


    $('body').on('click', '.js-view-content', function (e) {
        e.preventDefault();

        const $parent = $(this).parent();

        const prompt = $parent.find('.js-prompt').val() ?? null;
        let result = $parent.find('.js-result').val();

        const $modal = $('#contentModal');

        const $prompt = $modal.find('.js-prompt-card');
        $prompt.addClass('d-none');

        if (prompt && prompt !== '') {
            $prompt.removeClass('d-none');
            $prompt.find('p').text(prompt)
        }

        const $textCard = $modal.find('#generatedTextContents');
        const $imageCard = $modal.find('.js-image-generated-modal');

        $textCard.addClass('d-none');
        $imageCard.addClass('d-none');

        const $imageCardContent = $imageCard.find('.js-content-modal');

        $imageCardContent.html('');


        if (result) {
            result = JSON.parse(result);

            if (result.contents && Object.keys(result.contents).length) {
                $textCard.removeClass('d-none');

                for (const content of result.contents) {
                    const html = makeTextContentHtml(content);

                    $textCard.append(html);
                }
            }

            if (result.images && Object.keys(result.images).length) {
                $imageCard.removeClass('d-none');

                for (const image of result.images) {
                    const html = `<a href="${image}" target="_blank" class="d-block image-generated-card">
                                    <img src="${image}" alt="image" class="img-cover rounded-sm">
                                </a>`;

                    $imageCardContent.append(html);
                }
            }
        }

        $modal.modal('show');
    });

    function makeTextContentHtml(text) {
        return `<div class="js-text-generated mt-20 p-15 bg-info-light border-gray300 rounded-sm">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="font-14 text-gray">${generatedContentLang}</h4>

                <div class="form-group mb-0">
                    <button type="button" class="btn-transparent d-flex align-items-center js-copy-content-modal" data-toggle="tooltip" data-placement="top" title="${copyLang}" data-copy-text="${copyLang}" data-done-text="${doneLang}">
                        <i data-feather="copy" width="18" height="18" class="text-gray"></i>
                        <span class="text-gray font-12 ml-5">${copyLang}</span>
                    </button>
                </div>
            </div>

            <div class="mt-15 font-14 text-gray js-content">${text}</div>
        </div>`
    }


    var copyTimeout;

    $('body').on('click', '.js-copy-content-modal', function (e) {
        e.preventDefault();

        const $this = $(this);
        const copyText = $this.attr('data-copy-text');
        const doneText = $this.attr('data-done-text');
        const contentToCopy = $this.closest('.js-text-generated').find('.js-content').text();
        const tempTextarea = $('<textarea>').val(contentToCopy).appendTo('body').select();

        document.execCommand('copy');

        // Remove the temporary textarea
        tempTextarea.remove();

        const $span = $this.find('span')
        $span.text(doneText);

        if (copyTimeout) {
            clearTimeout(copyTimeout)
        }

        copyTimeout = setTimeout(() => {
            $span.text(copyText);
        }, 1500)
    });

})(jQuery);

(function ($) {
    "use strict";

    $('body').on('change', '.js-template-type', function (e) {
        const value = $(this).val();
        const $text = $('.js-text-fields');
        const $image = $('.js-image-fields');
        const $prompt = $('.js-prompt-field');
        const $textPromptHint = $('.js-text-prompt-hint');
        const $imagePromptHint = $('.js-image-prompt-hint');

        $text.addClass('d-none');
        $image.addClass('d-none');
        $prompt.addClass('d-none');
        $textPromptHint.addClass('d-none');
        $imagePromptHint.addClass('d-none');

        if (value === "text") {
            $text.removeClass('d-none');
            $textPromptHint.removeClass('d-none');
        } else if (value === "image") {
            $image.removeClass('d-none');
            $imagePromptHint.removeClass('d-none');
        }

        if (value) {
            $prompt.removeClass('d-none')
        }
    });


    $('body').on('change', 'input[name="enable_length"]', function (e) {
        const value = $(this).val();
        const $el = $('.js-length-field');

        if (this.checked) {
            $el.removeClass('d-none');
        } else {
            $el.addClass('d-none');
        }
    });


})(jQuery);

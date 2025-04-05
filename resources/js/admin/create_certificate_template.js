(function () {
    "use strict";

    function syncContentToForm() {
        $('.js-template-contents').val($('#certificateTemplateContainer').html());
    }

    function applyDraggable() {
        $(".draggable-element").draggable({
            containment: ".certificate-template-container",
            stop: function (event, ui) {
                syncContentToForm()
            }
        });
    }

    $(document).ready(function () {
        applyDraggable()
    })

    function removeDragElement(name) {
        $('.certificate-template-container').find('.draggable-element[data-name="' + name + '"]').remove();
    }

    function makeDragElement($accordion, name) {
        //removeDragElement(name);

        let content = $accordion.find('.js-element-content').val();
        const fontSize = $accordion.find('.js-element-font_size').val();
        const fontColor = $accordion.find('.js-element-font_color').val();
        const fontBold = $accordion.find('.js-element-font_weight_bold').is(":checked");
        const textCenter = $accordion.find('.js-element-text_center').is(":checked");
        const textRight = $accordion.find('.js-element-text_right').is(":checked");

        let $element = $(`.draggable-element[data-name="${name}"]`);
        if ($element.length < 1) {
            $element = $(`<div class="draggable-element" data-name="${name}"></div>`);
            $('.certificate-template-container').append($element);
        }

        const textAlign = textCenter ? 'center' : (textRight ? 'right' : 'inherit');
        const fontWeight = fontBold ? 'bold' : 'inherit';

        let styles = "";

        const top = $element.css("top") ?? 0;
        const left = $element.css("left") ?? 0;

        if (name === "platform_signature" || name === "stamp") {
            const $image = $accordion.find('.js-element-image');
            const prefix = $image.attr("data-prefix");
            const src = $image.val();

            content = `<img src="${prefix + src}" style="max-height: 100%; max-width: 100%;"/>`;

            styles += `display: flex; align-items: center; justify-content: center;`;
        }


        $element.html(content);

        styles += `top: ${top};`;
        styles += `left: ${left};`;
        styles += `font-size: ${fontSize};`;
        styles += `color: ${fontColor};`;
        styles += `text-align: ${textAlign};`;
        styles += `font-weight: ${fontWeight};`;

        let extraStyles = $accordion.find('.js-element-styles').val();
        styles = styles + (extraStyles ? extraStyles : '');

        $element.css('cssText', styles);

        if (name === "qr_code" || name === "platform_signature" || name === "stamp") {
            const size = $accordion.find('.js-element-image_size option:checked').val();

            if (size) {
                $element.css('width', size);
                $element.css('height', size);
            }
        }

        applyDraggable()
    }

    function afterChange($accordion, checked) {
        const elementName = $accordion.attr('data-element');
        const $statusBadge = $accordion.find('.js-status-element');

        if (checked) {
            makeDragElement($accordion, elementName);

            $statusBadge.removeClass('d-none')
        } else {
            removeDragElement(elementName);
            $statusBadge.addClass('d-none');
        }

        syncContentToForm();
    }

    $('body').on('change', '.js-element-enable', function () {
        const checked = this.checked;
        const $accordion = $(this).closest('.accordion-row');

        afterChange($accordion, checked)
    })

    $('body').on('keyup change', '.js-changeable-element-input', function () {
        const $accordion = $(this).closest('.accordion-row');
        const checked = $accordion.find('.js-element-enable').is(":checked");

        afterChange($accordion, checked)
    })

    $('body').on('change', '.js-certificate-image', function (e) {
        e.preventDefault();
        let prefix = $(this).attr('data-prefix');
        prefix = prefix.replaceAll('\\',"/");
        const path = prefix + $(this).val();

        $('.certificate-template-container').css('background-image', "url('" + path + "')");

        syncContentToForm();
    });

})(jQuery)

/*
(function ($) {
"use strict";

$('body').on('click', '#preview', function (e) {
    e.preventDefault();

    const form = $('#templateForm');
    const action = $(this).attr('data-action');

    form.attr('target', '_blank');
    form.attr('action', action);
    form.attr('method', 'post');
    form.trigger('submit');
});

$('body').on('click', '#submiter', function (e) {
    e.preventDefault();

    const form = $('#templateForm');
    const action = $(this).attr('data-action');

    form.removeAttr('target');
    form.attr('action', action);
    form.attr('method', 'post');
    form.trigger('submit');
});

$('body').on('change', '.js-certificate-image', function (e) {
    e.preventDefault();

    $('.note-editable').css('background-image', 'url("' + $(this).val() + '")');
});
})(jQuery);
*/

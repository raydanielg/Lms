(function () {
    "use strict";

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    $('body').on('change', '.js-bundle-content-locale', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $(this).closest('.js-content-form');
        const locale = $this.val();
        const bundleId = $this.attr('data-bundle-id');
        const item_id = $this.attr('data-id');
        const relation = $this.attr('data-relation');
        let fields = $this.attr('data-fields');
        fields = fields.split(',');


        $this.addClass('loadingbar gray');

        const path = '/panel/bundles/' + bundleId + '/getContentItemByLocale';
        const data = {
            item_id,
            locale,
            relation
        };

        $.post(path, data, function (result) {
            if (result && result.item) {
                const item = result.item;

                Object.keys(item).forEach(function (key) {
                    const value = item[key];

                    if ($.inArray(key, fields) !== -1) {
                        let element = $form.find('.js-ajax-' + key);
                        element.val(value);
                    }
                });

                $this.removeClass('loadingbar gray');
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray');
        });
    });

    $('body').on('click', '#addBundleWebinar', function (e) {
        e.preventDefault();
        const key = randomString();

        let html = $('#newBundleWebinarForm').html();
        html = html.replaceAll('record', key);
        html = html.replaceAll('bundleWebinars-select2', 'bundleWebinars-select2-' + key);

        $('#bundleWebinarsAccordion').prepend(html);

        $('.bundleWebinars-select2-' + key).select2({
            placeholder: $(this).data('placeholder'),
            allowClear: false,
        });

        feather.replace();
    });

    $('body').on('click', '.js-save-bundleWebinar', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('.bundleWebinar-form');
        handleWebinarItemForm(form, $this);
    });
})(jQuery);

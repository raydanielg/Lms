(function ($) {
    "use strict";

    $('body').on('click', '.js-submit-form', function () {
        const $this = $(this);
        const $form = $this.closest('form');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $form.trigger('submit')
    });


    $(document).ready(function () {
        const $searchSelect = $('.search-content-select2');
        const path = $searchSelect.attr('data-path');

        $searchSelect.select2({
            placeholder: $searchSelect.attr('data-placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: path,
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term,
                        option: $searchSelect.attr('data-search-option'),
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.title,
                                id: `${item.id}_${item.type}`,
                            };
                        })
                    };
                }
            }
        });
    })

})(jQuery);

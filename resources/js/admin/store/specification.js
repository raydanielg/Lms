(function ($) {
    "use strict";

    $('body').on('change', '#inputTypes input', function (e) {
        if ($(this).val() === 'multi_value' && this.checked) {
            $('#multiValues').removeClass('d-none');
        } else {
            $('#multiValues').addClass('d-none');
        }
    });


    $('body').on('click', '.add-btn', function (e) {
        e.preventDefault();
        var mainRow = $('.main-row');

        var copy = mainRow.clone();
        copy.removeClass('main-row');
        copy.removeClass('d-none');
        var copyHtml = copy.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', randomString());
        copy.html(copyHtml);
        $('.multi-values-card').append(copy);
    });

    $('body').on('click', '.remove-btn', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 16; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

})(jQuery);

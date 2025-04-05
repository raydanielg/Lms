(function () {
    "use strict";

    $('body').on('click', '.add-btn', function (e) {
        e.preventDefault();
        const parent = $(this).attr('data-parent');
        const main_row = $(this).attr('data-main-row');

        var mainRow = $('#' + main_row);

        var copy = mainRow.clone();
        copy.removeClass('main-row');
        copy.attr('id','')
        copy.removeClass('d-none');
        var copyHtml = copy.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', randomString());
        copy.html(copyHtml);
        $('#' + parent).append(copy);
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

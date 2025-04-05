(function ($) {
    "use strict";

    $('body').on('click', '.add-btn', function (e) {
        e.preventDefault();
        var mainRow = $(this).closest('.main-row');

        var copy = mainRow.clone();
        copy.removeClass('main-row');
        copy.find('.add-btn').addClass('remove-btn btn-danger').removeClass('add-btn btn-success').text(removeLang);
        var copyHtml = copy.prop('innerHTML');
        copyHtml = copyHtml.replace(/\[record\]/g, '[' + randomString() + ']');
        copy.html(copyHtml);
        mainRow.parent().append(copy);
    });

    $('body').on('click', '.remove-btn', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }
})(jQuery);

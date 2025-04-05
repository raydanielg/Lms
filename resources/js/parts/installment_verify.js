(function () {
    "use strict";

    $('body').on('click', '.panel-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'});
    });

    $('body').on('click', '.js-add-btn', function (e) {
        e.preventDefault();

        let copy = $('.js-main-row').clone();
        copy.removeClass('js-main-row');
        copy.removeClass('d-none');
        copy.addClass('js-second-row');
        copy.find('input').val('');

        const $add = copy.find('.js-add-btn');
        $add.removeClass('js-add-btn btn-primary');
        $add.addClass('js-remove-btn btn-danger');
        $add.find('svg').css({'transform' : 'rotate(45deg)'});

        let copyHtml = copy.prop('innerHTML');
        copyHtml = copyHtml.replaceAll('record', randomString());
        copy.html(copyHtml);

        $('.js-attachments').append(copy);
    });

    $('body').on('click', `.js-remove-btn`, function (e) {
        e.preventDefault();

        $(this).closest('.js-second-row').remove();
    });

})(jQuery);

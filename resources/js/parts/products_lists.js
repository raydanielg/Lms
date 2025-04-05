(function () {
    "use strict";

    $('body').on('change', '#topFilters input,#topFilters select', function (e) {
        e.preventDefault();
        $('#filtersForm').trigger('submit');
    });

})(jQuery);

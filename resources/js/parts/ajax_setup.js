/* Ajax Setup */
// *
(function ($) {
    "use strict";

    window.csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

})(jQuery);

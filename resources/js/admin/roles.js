(function ($) {
    "use strict";

    $('body').on('change', '#isAdmin', function () {
        const $card = $('.section-card');
        $card.addClass('d-none');

        $card.find('input').prop('checked', false);

        if (this.checked) {
            $('.section-card.is_admin').removeClass('d-none');
        } else {
            $('.section-card.is_panel').removeClass('d-none');
        }
    });

    $('body').on('change', '.section-parent', function (e) {
        let $this = $(this);
        let parent = $this.parent().closest('.section-box');
        let isChecked = e.target.checked;

        if (isChecked) {
            parent.find('input[type="checkbox"].section-child').prop('checked', true);
        } else {
            parent.find('input[type="checkbox"].section-child').prop('checked', false);
        }
    });

    $('body').on('change', '.section-child', function (e) {
        let $this = $(this);
        let parent = $(this).parent().closest('.section-box');
        let setChecked = false;
        let allChild = parent.find('input[type="checkbox"].section-child');

        allChild.each(function (index, child) {
            if ($(child).is(':checked')) {
                setChecked = true;
            }
        });

        let parentInput = parent.find('input[type="checkbox"].section-parent');
        parentInput.prop('checked', setChecked);
    });


    $('body').on('keyup', 'input[name="name"]', function (e) {
        const value = $(this).val();

        if (value) {
            $(this).val(value.replaceAll(' ', '_'))
        }
    });
})(jQuery);

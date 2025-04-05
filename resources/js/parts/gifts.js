(function () {
    "use strict"

    $(document).ready(function () {
        const $datetimepicker = $('.datetimepicker');
        const format = $datetimepicker.attr('data-format') ?? 'YYYY-MM-DD HH:mm';
        const drops = $datetimepicker.attr('data-drops') ?? 'down';

        $datetimepicker.daterangepicker({
            locale: {
                format: format,
                cancelLabel: 'Clear'
            },
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            autoUpdateInput: false,
            drops: drops,
        });
        $datetimepicker.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm'));
        });

        $datetimepicker.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

    })
})(jQuery)

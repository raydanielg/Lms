(function ($) {
    $('body').on('change', 'input[name="meeting_type"]', function () {

        const regionCard = $('#regionCard');

        if ($(this).val() === 'in_person') {
            regionCard.removeClass('d-none');
        } else {
            regionCard.addClass('d-none');
        }
    });

    $('body').on('change', 'input[name="flexible_date"]', function () {
        if (this.checked) {
            $('#dateTimeCard').addClass('d-none');
        } else {
            $('#dateTimeCard').removeClass('d-none');
        }
    });


    $(document).ready(function () {

        var $timeRange = $('#timeRange');

        if ($timeRange && $timeRange.length && jQuery().wRunner) {
            const minLimit = $timeRange.attr('data-minLimit');
            const maxLimit = $timeRange.attr('data-maxLimit');

            const minTimeEl = $timeRange.find('input[name="min_time"]');
            const maxTimeEl = $timeRange.find('input[name="max_time"]');

            const minValue = minTimeEl.val();
            const maxValue = maxTimeEl.val();

            var wtime = $timeRange.wRunner({
                type: 'range',
                limits: {
                    minLimit,
                    maxLimit,
                },
                rangeValue: {
                    minValue,
                    maxValue,
                },
                step: 1,
            });

            wtime.onValueUpdate(function (values) {
                minTimeEl.val(values.minValue);
                maxTimeEl.val(values.maxValue);
            });
        }
    });
})(jQuery);

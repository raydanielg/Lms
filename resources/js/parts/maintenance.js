(function () {
    "use strict";

    var maintenanceCountDown = $('#maintenanceCountDown');

    if (maintenanceCountDown.length) {
        var endtimeDate = maintenanceCountDown.attr('data-day');
        var endtimeHours = maintenanceCountDown.attr('data-hour');
        var endtimeMinutes = maintenanceCountDown.attr('data-minute');
        var endtimeSeconds = maintenanceCountDown.attr('data-second');

        maintenanceCountDown.countdown100({
            endtimeYear: 0,
            endtimeMonth: 0,
            endtimeDate: endtimeDate,
            endtimeHours: endtimeHours,
            endtimeMinutes: endtimeMinutes,
            endtimeSeconds: endtimeSeconds,
            timeZone: ""
        });
    }

})(jQuery);

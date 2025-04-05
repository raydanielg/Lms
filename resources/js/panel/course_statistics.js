(function () {
    "use strict";
    const style = getComputedStyle(document.body);
    const primaryColor = style.getPropertyValue('--primary');
    const secondaryColor = style.getPropertyValue('--secondary');
    const warningColor = style.getPropertyValue('--warning');

    function hexToRgb(hex, rgba = null) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

        let rgb = '';
        if (result) {
            rgb = `rgb(${parseInt(result[1], 16)},${parseInt(result[2], 16)},${parseInt(result[3], 16)})`;

            if (rgba) {
                rgb = `rgba(${parseInt(result[1], 16)},${parseInt(result[2], 16)},${parseInt(result[3], 16)}, ${rgba})`;
            }
        }

        return rgb;
    }

    function pieChart($el, labels, datasets) {

        new Chart($el, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: datasets,
                    borderWidth: 0,
                    borderColor: '',
                    backgroundColor: [primaryColor, secondaryColor, warningColor],
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6777ef',
                    pointRadius: 4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                segmentShowStroke: false,
                legend: {
                    display: false
                },
            }
        });
    }

    window.makePieChart = function (elId, labels, datasets) {
        let bodyEl = document.getElementById(elId).getContext('2d');

        pieChart(bodyEl, labels, datasets);
    };

    window.handleMonthlySalesChart = function (labels, datasets) {
        let ctx = document.getElementById('monthlySalesChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: datasets,
                    backgroundColor: 'transparent',
                    borderColor: primaryColor,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                legend: {
                    display: false
                },
            },
        });
    };

    window.handleCourseProgressChart = function (labels, datasets) {
        let ctx = document.getElementById('courseProgressLineChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: datasets,
                    backgroundColor: hexToRgb(primaryColor, 0.4),
                    borderColor: primaryColor,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                legend: {
                    display: false
                },
            },
        });
    };
})(jQuery);

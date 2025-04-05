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

    function pieChart($el, labels, datasets, colors) {

        new Chart($el, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: datasets,
                    borderWidth: 0,
                    borderColor: '',
                    backgroundColor: colors,
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

    window.makePieChart = function (elId, labels, percent) {
        let bodyEl = document.getElementById(elId).getContext('2d');

        const notComplete = 100 - percent;
        const colors = percent === 100 ? [primaryColor, '#f7f7f7'] : [warningColor, '#f7f7f7'];

        pieChart(bodyEl, labels, [percent, notComplete], colors);
    };
})(jQuery);

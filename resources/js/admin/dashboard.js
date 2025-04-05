var saleStatisticsChart = document.getElementById("saleStatisticsChart").getContext('2d');
var usersStatisticsChart = document.getElementById("usersStatisticsChart").getContext('2d');
var chart = {};

function makeStatisticsChart(name, section, badge, labels, datasets) {
    chart[name] = new Chart(section, {
        type: 'line',

        data: {
            labels: labels,
            datasets: [{
                label: badge,
                data: datasets,
                borderWidth: 5,
                borderColor: '#6777ef',
                backgroundColor: 'transparent',
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6777ef',
                pointRadius: 4
            }]
        },
        options: {

            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        stepSize: 150
                    }
                }],
                xAxes: [{
                    gridLines: {
                        color: '#fbfbfb',
                        lineWidth: 2
                    }
                }]
            },
        }
    });
}

function getSaleStatisticsData(type) {
    $.post(adminPanelPrefix + '/dashboard/getSaleStatisticsData', {type: type}, function (result) {
        if (result && result.code == 200) {
            if (chart && chart.saleStatisticsChart) {
                chart.saleStatisticsChart.destroy();
            }

            makeStatisticsChart('saleStatisticsChart', saleStatisticsChart, 'Sale', result.chart.labels, result.chart.data);
        }
    })
}

(function () {
    "use strict";

    $('body').on('click', '.js-sale-chart-month', function (e) {
        e.preventDefault();

        $(this).addClass('btn-primary');
        $('.js-sale-chart-year').removeClass('btn-primary');

        getSaleStatisticsData('day_of_month');
    });

    $('body').on('click', '.js-sale-chart-year', function (e) {
        e.preventDefault();

        $(this).addClass('btn-primary');
        $('.js-sale-chart-month').removeClass('btn-primary');

        getSaleStatisticsData('month_of_year');
    });

})(jQuery);


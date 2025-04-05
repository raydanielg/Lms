var classesStatisticsChart = document.getElementById("classesStatisticsChart").getContext('2d');
var netProfitChart = document.getElementById("netProfitChart").getContext('2d');
var chart;

function makeClassesStatisticsChart(badge, labels, datasets) {
    new Chart(classesStatisticsChart, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: badge,
                data: datasets,
                borderWidth: 0,
                borderColor: '#6777ef',
                backgroundColor: ['#43d477', '#1f3b64', '#ffab00'],
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6777ef',
                pointRadius: 4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            segmentShowStroke: false
        }
    });
}

function makeNetProfitChart(badge, labels, datasets) {
    chart = new Chart(netProfitChart, {
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
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
    $.post(adminPanelPrefix + '/marketing/getNetProfitChart', {type: type}, function (result) {
        if (result && result.code == 200) {
            if (chart) {
                chart.destroy();
            }

            makeNetProfitChart('Sale', result.chart.labels, result.chart.data);
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

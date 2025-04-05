(function ($) {
    "use strict";


    var ctx = document.getElementById('myChart').getContext('2d');

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: $chartDataMonths,
            datasets: [{
                label: '',
                data: $chartData,
                backgroundColor: 'transparent',
                borderColor: '#43d477',
                borderWidth: 2
            }]
        },

    });

    function handleNextBadgeChart() {
        const card = $('#nextBadgeChart');
        var percent = card.attr('data-percent');
        var label = card.attr('data-label');

        var options = {
            series: [Number(percent)],
            chart: {
                height: 300,
                width: "100%",
                type: 'radialBar',
                offsetY: -30,
            },

            plotOptions: {
                radialBar: {
                    startAngle: -130,
                    endAngle: 130,
                    inverseOrder: true,

                    hollow: {
                        margin: 5,
                        size: '50%',
                        image: '/assets/default/img/radial-image.png',
                        imageWidth: 140,
                        imageHeight: 140,
                        imageClipped: false,
                    },
                    track: {
                        opacity: 0.4,
                        colors: '#222'
                    },
                    dataLabels: {
                        enabled: false,
                        enabledOnSeries: undefined,
                        formatter: function (val, opts) {
                            return val + "%"
                        },
                        textAnchor: 'middle',
                        distributed: false,
                        offsetX: 0,
                        offsetY: 0,

                        style: {
                            fontSize: '14px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fill: ['#2b2b2b'],

                        },
                    },
                }
            },

            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.05,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100],
                    gradientToColors: ['#a927f9'],
                    type: 'horizontal'
                },
                strokeLinecap: 'round'
            },
            stroke: {
                dashArray: 9,
                strokecolor: ['#ffffff'],
            },

            labels: [label],
            colors: ['#0d6efd'],
        };

        var chart = new ApexCharts(document.querySelector("#nextBadgeChart"), options);
        chart.render();
    }

    handleNextBadgeChart();

    $('body').on('change', '#iNotAvailable', function (e) {
        e.preventDefault();

        if (this.checked) {
            Swal.fire({
                html: $('#iNotAvailableModal').html(),
                showCancelButton: false,
                showConfirmButton: false,
                customClass: {
                    content: 'p-0 text-left',
                },
                width: '40rem',
            });
        } else {
            handleOffline('', false);
        }
    });

    $('body').on('click', '.js-noticeboard-info', function (e) {
        const $this = $(this);

        const noticeboard_id = $this.attr('data-id');
        const card = $this.closest('.noticeboard-item');
        const title = card.find('.js-noticeboard-title').text();
        const time = card.find('.js-noticeboard-time').text();
        const message = card.find('.js-noticeboard-message').val();

        const modal = $('#noticeboardMessageModal');
        modal.find('.modal-title').text(title);
        modal.find('.modal-time').text(time);
        modal.find('.modal-message').html(message);


        Swal.fire({
            html: modal.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
        });

        if (!$this.hasClass('seen-at')) {
            $.get('/panel/noticeboard/' + noticeboard_id + '/saveStatus', function () {
            })
        }
    });

    $('body').on('click', '.js-save-offline-toggle', function (e) {
        const $this = $(this);

        const $card = $this.closest('.offline-modal');
        const textarea = $card.find('textarea');
        const message = textarea.val();

        handleOffline(message, true);
    });

    function handleOffline(message, toggle) {
        const action = '/panel/users/offlineToggle';

        const data = {
            message: message,
            toggle: toggle
        };

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + offlineSuccess + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000)
            } else {
                Swal.fire({
                    icon: 'error',
                    showConfirmButton: false,
                });
            }
        })
    }
})(jQuery)

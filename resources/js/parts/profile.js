$(function ($) {
    "use strict";

    function handleShowDay(unix) {
        var day = new persianDate(unix).day();
        var startDayTime = new persianDate(unix).startOf('day').unix();
        var endDayTime = new persianDate(unix).endOf('day').unix();

        var showThisDay = false;

        // availableDays is defined globally in blade

        for (var index2 in availableDays) {
            var disabled_day = Number(availableDays[index2]);
            if (disabled_day === day) {
                showThisDay = true;
            }
        }

        return showThisDay;
    }

    function handlePDatepicker() {
        $(".inline-reservation-calender").pDatepicker({
            inline: true,
            altField: '#inlineCalender',
            initialValue: true,
            calendarType: 'gregorian',
            initialValueType: true,
            autoClose: true,
            altFormat: 'DD MMMM YY',
            calendar: {
                gregorian: {
                    locale: 'en'
                }
            },
            toolbox: {
                calendarSwitch: {
                    enabled: false
                }
            },
            navigator: {
                scroll: {
                    enabled: false
                },
                text: {
                    btnNextText: '<',
                    btnPrevText: ">"
                }
            },
            minDate: new persianDate().subtract('day', 0).valueOf(),
            checkDate: function (unix) {
                return handleShowDay(unix);

            },
            timePicker: {
                enabled: false,
            },
            onSelect: function (unix) {
                const pDate = new persianDate(unix);
                const timestamp = pDate.startOf('day').unix();
                const dayLabel = pDate.format('dddd');
                const date = pDate.format('YYYY-MM-DD');

                $('#selectedDay').val(date);
                handleShowReservationTimes(timestamp, dayLabel, date);
            }
        });
    }

    if ($(".inline-reservation-calender").length) {
        handlePDatepicker();
    }

    function handleShowReservationTimes(timestamp, dayLabel, date) {
        const container = $('#PickTimeContainer');
        const body = $('#PickTimeBody');
        const availableTimes = $('#availableTimes');
        const loading = container.find('.loading-img');
        const user_id = container.attr('data-user-id');

        container.removeClass('d-none');

        $('html, body').animate({
            scrollTop: (container.offset().top - (window.innerHeight / 2))
        }, 600);

        body.addClass('d-none');
        loading.removeClass('d-none');
        body.find('.selected_date span').text($('#inlineCalender').val());

        const data = {
            timestamp: timestamp,
            day_label: dayLabel,
            date: date,
        };

        $.post('/users/' + user_id + '/availableTimes', data, function (result) {
            var html = '';
            if (result && typeof result.times !== "undefined") {
                Object.keys(result.times).forEach(key => {
                    const item = result.times[key];
                    html += '<div class="position-relative available-times ' + (item.can_reserve ? '' : 'disabled') + '">\n' +
                        '<input type="radio" name="time" id="availableTime' + item.id + '" value="' + item.id + '" ' + (item.can_reserve ? '' : 'disabled') + ' data-type="' + item.meeting_type + '">\n' +
                        '<label for="availableTime' + item.id + '">' + item.time + '</label>\n';
                    if (!item.can_reserve) {
                        html += '<span class="font-12 badge badge-danger text-white reserved-item">' + reservedLang + '</span>';
                    }
                    html += '<input type="hidden" class="js-time-description" value="' + item.description + '"/>';
                    html += '</div>';
                });

                availableTimes.html(html);
            }
        }).always(() => {
            body.removeClass('d-none');
            loading.addClass('d-none');
        });
    }

    $('body').on('change', 'input[name="time"]', function (e) {
        if (this.checked) {
            const body = $('#PickTimeBody');
            const $this = $(this);
            const type = $this.attr('data-type');
            const time = $this.parent().find('label').text();
            const date = body.find('.selected_date span').text();

            body.find('.selected-date-time span').text(date + ' | ' + time);

            $('.js-finalize-reserve').removeClass('d-none');

            const onlineTypeReserve = $('input[value="online"]').closest('.meeting-type-reserve');
            const inPersonTypeReserve = $('input[value="in_person"]').closest('.meeting-type-reserve');

            onlineTypeReserve.removeClass('disabled');
            onlineTypeReserve.find('input').prop('disabled', false);
            inPersonTypeReserve.removeClass('disabled');
            inPersonTypeReserve.find('input').prop('disabled', false);

            if (type === 'in_person') {
                onlineTypeReserve.addClass('disabled');
                onlineTypeReserve.find('input').prop('disabled', true);
            }

            if (type === 'online') {
                inPersonTypeReserve.addClass('disabled');
                inPersonTypeReserve.find('input').prop('disabled', true);
            }

            const $timeDescription = $this.parent().find('.js-time-description');
            const $timeDescriptionCard = $('.js-time-description-card');

            if ($timeDescription && $timeDescription.val() && $timeDescription.val() !== 'null' && $timeDescription.val() !== 'undefined') {
                $timeDescriptionCard.removeClass('d-none');
                $timeDescriptionCard.text($timeDescription.val());
            } else {
                $timeDescriptionCard.addClass('d-none');
            }
        }
    });

    $('body').on('change', 'input[name="meeting_type"]', function (e) {
        $('#withGroupMeetingSwitch').prop('checked', false);
        $('.js-group-meeting-options').addClass('d-none');
        $('input[name="student_count"]').val(1);
        $('.wrunner').remove();

        if (this.checked) {
            const $this = $(this);
            const value = $this.val();

            const $jsGroupMeetingSwitch = $('.js-group-meeting-switch');
            const $jsOnlineGroupAmount = $('.js-online-group-amount');
            const $jsInPersonGroupAmount = $('.js-in-person-group-amount');

            $jsGroupMeetingSwitch.removeClass('d-none').addClass('d-flex')

            if (value === 'in_person') {
                $jsOnlineGroupAmount.addClass('d-none');
                $jsInPersonGroupAmount.removeClass('d-none');
            }

            if (value === 'online') {
                $jsOnlineGroupAmount.removeClass('d-none');
                $jsInPersonGroupAmount.addClass('d-none');
            }

            $('.js-reserve-btn').removeClass('d-none').addClass('d-flex');
            $('.js-reserve-description').removeClass('d-none');
        }
    });

    $('body').on('change', '#withGroupMeetingSwitch', function (e) {
        const $jsGroupMeetingOptions = $('.js-group-meeting-options');

        $('input[name="student_count"]').val(1);
        $('.wrunner').remove();

        if (this.checked) {
            $jsGroupMeetingOptions.removeClass('d-none');

            handleStudentCountRange();
        } else {
            $jsGroupMeetingOptions.addClass('d-none');
        }
    });

    $('body').on('click', '.js-submit-form', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $this.closest('form');
        const action = $form.attr('action');
        const data = $form.serializeObject();

        $.post('/meetings/reserve', data, function (result) {
            $.toast({
                heading: result.title,
                text: result.msg,
                bgColor: result.status === 'success' ? '#43d477' : '#f63c3c',
                textColor: 'white',
                hideAfter: 10000,
                position: 'bottom-right',
                icon: result.status
            });

            if (result.status === 'success' && typeof result.redirect !== "undefined") {
                window.location.href = result.redirect;
            } else {
                $this.removeClass('loadingbar gray').prop('disabled', false);
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray').prop('disabled', false);
        });
    });

    $('body').on('click', '#followToggle', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        const user_id = $this.attr('data-user-id');

        const path = '/users/' + user_id + '/follow';

        $.get(path, function (result) {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            if (result && result.code === 200) {
                if (result.follow) {
                    $this.removeClass('btn-primary').addClass('btn-danger');
                    $this.text(unFollowLang);
                } else {
                    $this.removeClass('btn-danger').addClass('btn-primary');
                    $this.text(followLang);
                }
            }
        })
    });

    $('body').on('click', '.js-refresh-captcha', function (e) {
        e.preventDefault();

        refreshCaptcha();
    });

    $('body').on('click', '.js-send-message', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#sendMessageModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            onOpen: () => {
                refreshCaptcha();
            },
            width: '42rem',
        });
    });

    $('body').on('click', '.js-send-message-submit', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + messageSuccessSentLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + result.message + '</h3>',
                    showConfirmButton: false,
                });
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            refreshCaptcha();

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });

    function handleStudentCountRange() {
        var $studentCountRange = $('#studentCountRange');

        if ($studentCountRange && jQuery().wRunner) {
            const meeting_type = $('input[name="meeting_type"]:checked').val();

            if (meeting_type) {
                const minLimit = $studentCountRange.attr('data-minLimit');
                const maxLimit = meeting_type === 'in_person' ? $('#in_person_group_max_student').val() : $('#online_group_max_student').val();

                const $studentCountInput = $studentCountRange.find('input[name="student_count"]');


                var wtime = $studentCountRange.wRunner({
                    type: 'single',
                    limits: {
                        minLimit,
                        maxLimit,
                    },
                    singleValue: minLimit,
                    step: 1,
                });

                wtime.onValueUpdate(function (res) {
                    $studentCountInput.val(res.value);
                });
            }
        }
    }
});

@if(!empty($meeting) and !empty($meeting->meetingTimes) and $meeting->meetingTimes->count() > 0)
    @push('styles_top')
        <link rel="stylesheet" href="/assets/vendors/wrunner-html-range-slider-with-2-handles/css/wrunner-default-theme.css">
    @endpush

    <div class="mt-40">
        <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('site.view_available_times') }}</h3>

        <div class="mt-35">
            <div class="row align-items-center justify-content-center">
                <input type="hidden" id="inlineCalender" class="form-control">
                <div class="inline-reservation-calender"></div>
            </div>
        </div>
    </div>

    <div class="pick-a-time d-none" id="PickTimeContainer" data-user-id="{{ $user["id"] }}">

        @if(
               !empty(getFeaturesSettings("frontend_coupons_display_type")) and
               !empty($instructorDiscounts) and
               count($instructorDiscounts)
           )
            @foreach($instructorDiscounts as $instructorDiscount)
                @include('web.default.includes.discounts.instructor_discounts_card', ['discount' => $instructorDiscount, 'instructorDiscountClassName' => "my-40"])
            @endforeach
        @endif

        <div class="d-flex align-items-center my-40 rounded-lg border px-10 py-5">
            <div class="appointment-timezone-icon">
                <img src="/assets/default/img/icons/timezone.svg" alt="appointment timezone">
            </div>
            <div class="ml-15">
                <div class="font-16 font-weight-bold text-dark-blue">{{ trans('update.note') }}:</div>
                <p class="font-14 font-weight-500 text-gray">{{ trans('update.appointment_timezone_note_hint',['timezone' => $meetingTimezone .' '. toGmtOffset($meetingTimezone)]) }}</p>
            </div>
        </div>


        {{-- Cashback Alert --}}
        @include('web.default.includes.cashback_alert',['itemPrice' => $meeting->amount, 'classNames' => 'mt-0 mb-40', 'itemType' => 'meeting'])


        <div class="loading-img d-none text-center">
            <img src="/assets/default/img/loading.gif" width="80" height="80">
        </div>

        <form action="{{ (!$meeting->disabled) ? '/meetings/reserve' : '' }}" method="post" id="PickTimeBody" class="d-none">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="day" id="selectedDay" value="">

            <h3 class="font-16 font-weight-bold text-dark-blue">
                @if($meeting->disabled)
                    {{ trans('public.unavailable') }}
                @else
                    {{ trans('site.pick_a_time') }}
                    @if(!empty($meeting) and !empty($meeting->discount) and !empty($meeting->amount) and $meeting->amount > 0)
                        <span class="badge badge-danger text-white font-12">{{ $meeting->discount }}% {{ trans('public.off') }}</span>
                    @endif
                @endif
            </h3>

            <div class="d-flex flex-column mt-10">
                @if($meeting->disabled)
                    <span class="font-14 text-gray">{{ trans('public.unavailable_description') }}</span>
                @else
                    <span class="d-block font-14 text-gray font-weight-500">
                        {{ trans('site.instructor_hourly_charge') }}

                        @if(!empty($meeting->amount) and $meeting->amount > 0)
                            @if(!empty($meeting->discount))
                                <span class="text-decoration-line-through">{{ handlePrice($meeting->amount, true, true, false, null, true) }}</span>
                                <span class="text-primary">{{ handlePrice($meeting->amount - (($meeting->amount * $meeting->discount) / 100), true, true, false, null, true) }}</span>
                            @else
                                <span class="text-primary">{{ handlePrice($meeting->amount, true, true, false, null, true) }}</span>
                            @endif
                        @else
                            <span class="text-primary">{{ trans('public.free') }}</span>
                        @endif
                    </span>

                    @if($meeting->in_person)
                    <span class="d-block font-14 text-gray font-weight-500">
                        {{ trans('update.instructor_hourly_charge_in_person_amount') }}

                        @if(!empty($meeting->in_person_amount) and $meeting->in_person_amount > 0)
                            @if(!empty($meeting->discount))
                                <span class="text-decoration-line-through">{{ handlePrice($meeting->in_person_amount, true, true, false, null, true) }}</span>
                                <span class="text-primary">{{ handlePrice($meeting->in_person_amount - (($meeting->in_person_amount * $meeting->discount) / 100), true, true, false, null, true) }}</span>
                            @else
                                <span class="text-primary">{{ handlePrice($meeting->in_person_amount, true, true, false, null, true) }}</span>
                            @endif
                        @else
                            <span class="text-primary">{{ trans('public.free') }}</span>
                        @endif
                    </span>
                  @endif
                  @if($meeting->group_meeting)
                    <span class="d-block font-14 text-gray font-weight-500">{{ trans('update.instructor_conducts_group_meetings',['min' => $meeting->online_group_min_student,'max' => $meeting->online_group_max_student]) }}</span>
                  @endif

                @endif

                <span class="font-14 text-gray mt-5 selected_date font-weight-500">{{ trans('site.selected_date') }}: <span></span></span>
            </div>

            @if(!$meeting->disabled)
                <div id="availableTimes" class="d-flex flex-wrap align-items-center mt-25">

                </div>

                <div class="js-time-description-card d-none mt-25 rounded-sm border p-10">

                </div>

                <div class="mt-25 d-none js-finalize-reserve">
                    <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('update.finalize_your_meeting') }}</h3>
                    <span class="selected-date-time font-14 text-gray font-weight-500">{{ trans('update.meeting_time') }}: <span></span></span>

                    <div class="mt-15">
                        <span class="font-16 font-weight-500 text-dark-blue">{{ trans('update.meeting_type') }}</span>

                        <div class="d-flex align-items-center mt-5">
                            <div class="meeting-type-reserve position-relative">
                                <input type="radio" name="meeting_type" id="meetingTypeInPerson" value="in_person">
                                <label for="meetingTypeInPerson">{{ trans('update.in_person') }}</label>
                            </div>

                            <div class="meeting-type-reserve position-relative">
                                <input type="radio" name="meeting_type" id="meetingTypeOnline" value="online">
                                <label for="meetingTypeOnline">{{ trans('update.online') }}</label>
                            </div>
                        </div>
                    </div>

                    @if($meeting->group_meeting)
                        <div class="js-group-meeting-switch d-none align-items-center mt-20">
                            <label class="mb-0 mr-10 text-gray font-14 font-weight-500 cursor-pointer"
                                   for="withGroupMeetingSwitch">{{ trans('update.group_meeting') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="with_group_meeting" class="custom-control-input"
                                       id="withGroupMeetingSwitch">
                                <label class="custom-control-label" for="withGroupMeetingSwitch"></label>
                            </div>
                        </div>

                        <div class="js-group-meeting-options d-none mt-15">
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <div class="form-group">
                                        <input type="hidden" id="online_group_max_student" value="{{ $meeting->online_group_max_student }}">
                                        <input type="hidden" id="in_person_group_max_student" value="{{ $meeting->in_person_group_max_student }}">
                                        <label for="studentCountRange" class="form-label">{{ trans('update.participates') }}:</label>
                                        <div
                                            class="range"
                                            id="studentCountRange"
                                            data-minLimit="1"
                                        >
                                            <input type="hidden" name="student_count" value="1">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="js-online-group-amount d-none font-14 font-weight-500 mt-15">
                                <span class="text-gray d-block">{{ trans('update.online') }} {{ trans('update.group_meeting_hourly_rate_per_student',['amount' => handlePrice($meeting->online_group_amount, true, true, false, null, true)]) }}</span>
                                <span class="text-danger mt-5 d-block">{{ trans('update.group_meeting_student_count_hint',['min' => $meeting->online_group_min_student, 'max' => $meeting->online_group_max_student]) }}</span>
                                <span class="text-danger mt-5 d-block">{{ trans('update.group_meeting_max_student_count_hint',['max' => $meeting->online_group_max_student]) }}</span>
                            </div>

                            @if($meeting->in_person)
                            <div class="js-in-person-group-amount d-none font-14 font-weight-500 mt-15">
                                <span class="text-gray d-block">{{ trans('update.in_person') }} {{ trans('update.group_meeting_hourly_rate_per_student',['amount' => handlePrice($meeting->in_person_group_amount, true, true, false, null, true)]) }}</span>
                                <span class="text-danger mt-5 d-block">{{ trans('update.group_meeting_student_count_hint',['min' => $meeting->in_person_group_min_student, 'max' => $meeting->in_person_group_max_student]) }}</span>
                                <span class="text-danger mt-5 d-block">{{ trans('update.group_meeting_max_student_count_hint',['max' => $meeting->in_person_group_max_student]) }}</span>
                            </div>
                            @endif

                        </div>
                    @endif
                </div>

                <div class="js-reserve-description d-none form-group mt-30">
                    <label class="input-label">{{ trans('public.description') }}</label>
                    <textarea name="description" class="form-control" rows="5" placeholder="{{ trans('update.reserve_time_description_placeholder') }}"></textarea>
                </div>

                <div class="js-reserve-btn d-none align-items-center justify-content-end mt-30">
                    <button type="button" class="js-submit-form btn btn-primary">{{ trans('meeting.reserve_appointment') }}</button>
                </div>
            @endif
        </form>
    </div>

    @push('scripts_bottom')
        <script src="/assets/vendors/wrunner-html-range-slider-with-2-handles/js/wrunner-jquery.js"></script>
    @endpush
@else

    @include(getTemplate() . '.includes.no-result',[
       'file_name' => 'meet.png',
       'title' => trans('site.instructor_not_available'),
       'hint' => '',
    ])

@endif

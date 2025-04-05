@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-clockpicker/bootstrap-clockpicker.min.css">
@endpush

@section('content')

    <form action="/panel/meetings/{{ $meeting->id }}/update" method="post">
        {{ csrf_field() }}
        <section>
            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h2 class="section-title">{{ trans('panel.my_timesheet') }}</h2>

                <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="mb-0 mr-10 cursor-pointer font-14 text-gray font-weight-500" for="temporaryDisableMeetingsSwitch">{{ trans('panel.temporary_disable_meetings') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="disabled" class="custom-control-input" id="temporaryDisableMeetingsSwitch" {{ $meeting->disabled ? 'checked' : '' }}>
                        <label class="custom-control-label" for="temporaryDisableMeetingsSwitch"></label>
                    </div>
                </div>
            </div>

            <div class="panel-section-card time-sheet py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <td class="text-left text-gray font-weight-500">{{ trans('public.day') }}</td>
                                    <td class="text-left text-gray font-weight-500">{{ trans('public.availability_times') }}</td>
                                    <td class="text-right text-gray font-weight-500">{{ trans('public.controls') }}</td>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach(\App\Models\MeetingTime::$days as $day)
                                    <tr id="{{ $day }}TimeSheet" data-day="{{ $day }}">
                                        <td class="text-left">
                                            <span class="font-weight-500 text-dark-blue d-block">{{ trans('panel.'.$day) }}</span>
                                            <span class="font-12 text-gray">{{ isset($meetingTimes[$day]) ? $meetingTimes[$day]["hours_available"] : 0 }} {{ trans('home.hours') .' '. trans('public.available') }}</span>
                                        </td>
                                        <td class="time-sheet-items text-left align-middle">
                                            @if(isset($meetingTimes[$day]))
                                                @foreach($meetingTimes[$day]["times"] as $time)
                                                    <div class="position-relative selected-time px-15 py-5 mb-10 mb-lg-0 bg-gray200 rounded d-inline-block mr-10">
                                                        <span class="inner-time text-gray font-12">
                                                            {{ $time->time }}

                                                            <span class="mx-5">-</span>

                                                            <span class="font-12 text-gray">{{ trans('update.'.($time->meeting_type == 'all' ? 'both' : $time->meeting_type)) }}</span>
                                                        </span>

                                                        <span data-time-id="{{ $time->id }}" class="remove-time rounded-circle bg-danger">
                                                            <i data-feather="x" width="12" height="12"></i>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </td>
                                        <td class="text-right align-middle">
                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <button type="button" class="add-time btn-transparent webinar-actions d-block mt-10">{{ trans('public.add_time') }}</button>

                                                    @if(isset($meetingTimes[$day]) and !empty($meetingTimes[$day]["hours_available"]) and $meetingTimes[$day]["hours_available"] > 0)
                                                        <button type="button" class="clear-all btn-transparent webinar-actions d-block mt-10">{{ trans('public.clear_all') }}</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-30">
            <h2 class="section-title after-line">{{ trans('panel.my_hourly_charge') }}</h2>

            <div class="row align-items-center mt-20">

                <div class="col-12 col-md-3">
                    <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('panel.amount') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-white font-16">
                                {{ $currency }}
                            </span>
                        </div>
                        <input type="number" name="amount" value="{{ !empty($meeting) ? convertPriceToUserCurrency($meeting->amount) : old('amount') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('panel.discount') }} (%)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-white font-16">%</span>
                        </div>
                        <input type="number" name="discount" value="{{ !empty($meeting) ? $meeting->discount : old('discount') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-30">
            <h2 class="section-title after-line">{{ trans('update.in_person_meetings') }}</h2>

            <div class="row">
                <div class="col-12 col-lg-3 mt-15">
                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="inPersonMeetingSwitch">{{ trans('update.available_for_in_person_meetings') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="in_person" class="custom-control-input" id="inPersonMeetingSwitch" {{ ((!empty($meeting) and $meeting->in_person) or old('in_person') == 'on') ? 'checked' :  '' }}>
                            <label class="custom-control-label" for="inPersonMeetingSwitch"></label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3 {{ ((!empty($meeting) and $meeting->in_person) or old('in_person') == 'on') ? '' :  'd-none' }}" id="inPersonMeetingAmount">
                    <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.hourly_amount') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-white font-16">
                                {{ $currency }}
                            </span>
                        </div>

                        <input type="number" name="in_person_amount" value="{{ !empty($meeting) ? convertPriceToUserCurrency($meeting->in_person_amount) : old('in_person_amount') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-30">
            <h2 class="section-title after-line">{{ trans('update.group_meeting') }}</h2>

            <div class="row">
                <div class="col-12 col-lg-3 mt-15">
                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="groupMeetingSwitch">{{ trans('update.available_for_group_meeting') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="group_meeting" class="custom-control-input" id="groupMeetingSwitch" {{ ((!empty($meeting) and $meeting->group_meeting) or old('group_meeting') == 'on') ? 'checked' :  '' }}>
                            <label class="custom-control-label" for="groupMeetingSwitch"></label>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5 {{ ((!empty($meeting) and $meeting->group_meeting) or old('group_meeting') == 'on') ? '' :  'd-none' }}" id="onlineGroupMeetingOptions">
                    <h4 class="font-16 text-gray font-weight-bold">{{ trans('update.online_group_meeting_options') }}</h4>

                    <div class="row mt-15">
                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.minimum_students') }}</label>
                            <input type="number" min="2" name="online_group_min_student" value="{{ !empty($meeting) ? $meeting->online_group_min_student : old('online_group_min_student') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.maximum_students') }}</label>
                            <input type="number" name="online_group_max_student" value="{{ !empty($meeting) ? $meeting->online_group_max_student : old('online_group_max_student') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.hourly_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-white font-16">
                                        {{ $currency }}
                                    </span>
                                </div>

                                <input type="text" name="online_group_amount" value="{{ !empty($meeting) ? convertPriceToUserCurrency($meeting->online_group_amount) : old('online_group_amount') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-20 {{ ((!empty($meeting) and $meeting->group_meeting and $meeting->in_person) or (old('group_meeting') == 'on' and old('in_person') == 'on')) ? '' :  'd-none' }}" id="inPersonGroupMeetingOptions">
                    <h4 class="font-16 text-gray font-weight-bold">{{ trans('update.in_person_group_meeting_options') }}</h4>

                    <div class="row mt-15">
                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.minimum_students') }}</label>
                            <input type="number" min="2" name="in_person_group_min_student" value="{{ !empty($meeting) ? $meeting->in_person_group_min_student : old('in_person_group_min_student') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.maximum_students') }}</label>
                            <input type="number" name="in_person_group_max_student" value="{{ !empty($meeting) ? $meeting->in_person_group_max_student : old('in_person_group_max_student') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="font-weight-500 font-14 text-dark-blue d-block">{{ trans('update.hourly_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text text-white font-16">
                                        {{ $currency }}
                                    </span>
                                </div>

                                <input type="text" name="in_person_group_amount" value="{{ !empty($meeting) ? convertPriceToUserCurrency($meeting->in_person_group_amount) : old('in_person_group_amount') }}" class="form-control" placeholder="{{ trans('panel.number_only') }}"/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-15">
            <button type="button" id="meetingSettingFormSubmit" class="btn btn-sm btn-primary mt-30">{{ trans('public.submit') }}</button>
        </div>
    </form>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/bootstrap-clockpicker/bootstrap-clockpicker.min.js"></script>
    <script type="text/javascript">
        var saveLang = '{{ trans('public.save') }}';
        var closeLang = '{{ trans('public.close') }}';
        var successDeleteTime = '{{ trans('meeting.success_delete_time') }}';
        var errorDeleteTime = '{{ trans('meeting.error_delete_time') }}';
        var successSavedTime = '{{ trans('meeting.success_save_time') }}';
        var errorSavingTime = '{{ trans('meeting.error_saving_time') }}';
        var noteToTimeMustGreater = '{{ trans('meeting.note_to_time_must_greater_from_time') }}';
        var requestSuccess = '{{ trans('public.request_success') }}';
        var requestFailed = '{{ trans('public.request_failed') }}';
        var saveMeetingSuccessLang = '{{ trans('meeting.save_meeting_setting_success') }}';
        var meetingTypeLang = '{{ trans('update.meeting_type') }}';
        var inPersonLang = '{{ trans('update.in_person') }}';
        var onlineLang = '{{ trans('update.online') }}';
        var bothLang = '{{ trans('update.both') }}';
        var descriptionLng = '{{ trans('public.description') }}';
        var saveTimeDescriptionPlaceholder = '{{ trans('update.save_time_description_placeholder') }}';

        var toTimepicker, fromTimepicker;
    </script>
    <script src="/assets/default/js/panel/meeting/meeting.min.js"></script>
@endpush

@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp

@push('styles_top')

@endpush

<div class="tab-pane mt-3 fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">

    <form action="{{ getAdminPanelUrl() }}/settings/reminders" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="general">
        <input type="hidden" name="reminders" value="reminders">

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.webinar_reminder_schedule') }}</label>
                    <input type="number" name="value[webinar_reminder_schedule]" id="webinar_reminder_schedule" value="{{ (!empty($itemValue) and !empty($itemValue['webinar_reminder_schedule'])) ? $itemValue['webinar_reminder_schedule'] : 1 }}" class="form-control"/>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.webinar_reminder_hint') }}</p>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('update.meeting_reminder_schedule') }}</label>
                    <input type="number" name="value[meeting_reminder_schedule]" id="meeting_reminder_schedule" value="{{ (!empty($itemValue) and !empty($itemValue['meeting_reminder_schedule'])) ? $itemValue['meeting_reminder_schedule'] : 1 }}" class="form-control"/>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.meeting_reminder_hint') }}</p>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('update.subscribe_reminder_schedule') }}</label>
                    <input type="number" name="value[subscribe_reminder_schedule]" id="subscribe_reminder_schedule" value="{{ (!empty($itemValue) and !empty($itemValue['subscribe_reminder_schedule'])) ? $itemValue['subscribe_reminder_schedule'] : 1 }}" class="form-control"/>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.subscribe_reminder_hint') }}</p>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>

@push('scripts_bottom')

@endpush

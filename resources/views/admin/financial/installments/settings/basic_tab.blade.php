@php
    $basicSetting = $settings->where('name', \App\Models\Setting::$installmentsSettingsName)->first();
    $basicValue = !empty($basicSetting) ? $basicSetting->value : null;

    if (!empty($basicValue)) {
        $basicValue = json_decode($basicValue, true);
    }
@endphp

<div class="row">
    <div class="col-12 col-md-6">
        <form action="{{ getAdminPanelUrl('/financial/installments/settings') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="page" value="general">
            <input type="hidden" name="name" value="{{ \App\Models\Setting::$installmentsSettingsName }}">
            <input type="hidden" name="locale" value="{{ \App\Models\Setting::$defaultSettingsLocale }}">

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[status]" value="0">
                    <input type="checkbox" name="value[status]" id="installmentStatusSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['status']) and $basicValue['status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="installmentStatusSwitch">{{ trans('admin/main.active') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.installment_setting_active_hint') }}</div>
            </div>

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[disable_course_access_when_user_have_an_overdue_installment]" value="0">
                    <input type="checkbox" name="value[disable_course_access_when_user_have_an_overdue_installment]" id="disableCourseSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['disable_course_access_when_user_have_an_overdue_installment']) and $basicValue['disable_course_access_when_user_have_an_overdue_installment']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="disableCourseSwitch">{{ trans('update.disable_course_access_when_user_have_an_overdue_installment') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.disable_course_access_when_user_have_an_overdue_installment_hint') }}</div>
            </div>

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[disable_all_courses_access_when_user_have_an_overdue_installment]" value="0">
                    <input type="checkbox" name="value[disable_all_courses_access_when_user_have_an_overdue_installment]" id="disableAllCourseSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['disable_all_courses_access_when_user_have_an_overdue_installment']) and $basicValue['disable_all_courses_access_when_user_have_an_overdue_installment']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="disableAllCourseSwitch">{{ trans('update.disable_all_courses_access_when_user_have_an_overdue_installment') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.disable_all_courses_access_when_user_have_an_overdue_installment_hint') }}</div>
            </div>

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[disable_instalments_when_the_user_have_an_overdue_installment]" value="0">
                    <input type="checkbox" name="value[disable_instalments_when_the_user_have_an_overdue_installment]" id="disableWhenOverdueSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['disable_instalments_when_the_user_have_an_overdue_installment']) and $basicValue['disable_instalments_when_the_user_have_an_overdue_installment']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="disableWhenOverdueSwitch">{{ trans('update.disable_instalments_when_the_user_have_an_overdue_installment') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.disable_instalments_when_the_user_have_an_overdue_installment_hint') }}</div>
            </div>

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[allow_cancel_verification]" value="0">
                    <input type="checkbox" name="value[allow_cancel_verification]" id="allowCancelVerificationSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['allow_cancel_verification']) and $basicValue['allow_cancel_verification']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="allowCancelVerificationSwitch">{{ trans('update.allow_cancel_verification') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.allow_cancel_verification_hint') }}</div>
            </div>

            <div class="form-group custom-switches-stacked">
                <label class="custom-switch pl-0 d-flex align-items-center">
                    <input type="hidden" name="value[display_installment_button]" value="0">
                    <input type="checkbox" name="value[display_installment_button]" id="displayInstallmentButtonSwitch" value="1" {{ (!empty($basicValue) and !empty($basicValue['display_installment_button']) and $basicValue['display_installment_button']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                    <span class="custom-switch-indicator"></span>
                    <label class="custom-switch-description mb-0 cursor-pointer" for="displayInstallmentButtonSwitch">{{ trans('update.display_installment_button') }}</label>
                </label>
                <div class="text-muted text-small">{{ trans('update.display_installment_button_hint') }}</div>
            </div>

            <div class="form-group">
                <label>{{ trans('update.overdue_interval_days') }}</label>
                <input type="number" name="value[overdue_interval_days]" value="{{ (!empty($basicValue) and !empty($basicValue['overdue_interval_days'])) ? $basicValue['overdue_interval_days'] : old('overdue_interval_days') }}" class="form-control text-center"/>
                <div class="text-muted text-small mt-1">{{ trans('update.overdue_interval_days_hint') }}</div>
            </div>

            <div class="form-group">
                <label>{{ trans('update.installment_plans_position') }}</label>
                <select name="value[installment_plans_position]" class="form-control">
                    <option value="top_of_page" {{ (!empty($basicValue) and !empty($basicValue['installment_plans_position']) and $basicValue['installment_plans_position'] == "top_of_page") ? 'selected' : '' }}>{{ trans('update.top_of_page') }}</option>
                    <option value="bottom_of_page" {{ (!empty($basicValue) and !empty($basicValue['installment_plans_position']) and $basicValue['installment_plans_position'] == "bottom_of_page") ? 'selected' : '' }}>{{ trans('update.bottom_of_page') }}</option>
                </select>
                <div class="text-muted text-small mt-1">{{ trans('update.installment_plans_position_hint') }}</div>
            </div>

            <div class="form-group">
                <label>{{ trans('update.reminder_before_overdue_days') }}</label>
                <input type="number" name="value[reminder_before_overdue_days]" value="{{ (!empty($basicValue) and !empty($basicValue['reminder_before_overdue_days'])) ? $basicValue['reminder_before_overdue_days'] : old('reminder_before_overdue_days') }}" class="form-control text-center"/>
                <div class="text-muted text-small mt-1">{{ trans('update.reminder_before_overdue_days_hint') }}</div>
            </div>

            <div class="form-group">
                <label>{{ trans('update.reminder_after_overdue_days') }}</label>
                <input type="number" name="value[reminder_after_overdue_days]" value="{{ (!empty($basicValue) and !empty($basicValue['reminder_after_overdue_days'])) ? $basicValue['reminder_after_overdue_days'] : old('reminder_after_overdue_days') }}" class="form-control text-center"/>
                <div class="text-muted text-small mt-1">{{ trans('update.reminder_after_overdue_days_hint') }}</div>
            </div>

            <button type="submit" class="btn btn-primary mt-1">{{ trans('admin/main.submit') }}</button>
        </form>
    </div>
</div>

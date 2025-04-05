

<div class="tab-pane mt-3 fade " id="instructors" role="tabpanel" aria-labelledby="instructors-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="{{ \App\Models\Setting::$registrationPackagesInstructorsName }}">

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0 d-flex align-items-center">
                        <input type="hidden" name="value[status]" value="0">
                        <input type="checkbox" name="value[status]" id="instructorsStatusSwitch" value="1" {{ (!empty($instructorsSettings) and !empty($instructorsSettings['status']) and $instructorsSettings['status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="instructorsStatusSwitch">{{ trans('update.registration_packages_instructors_status') }}</label>
                    </label>
                    <div class="text-muted text-small">{{ trans('update.registration_packages_instructors_status_hint') }}</div>
                </div>
                <h2 class="section-title">{{ trans('update.instructor_default_values') }}</h2>

                @foreach(['courses_capacity','courses_count','meeting_count','product_count'] as $str)
                    <div class="form-group">
                        <label>{{ trans('update.'.$str) }}</label>
                        <input type="text" class="form-control" name="value[{{ $str }}]" value="{{ (!empty($instructorsSettings) and isset($instructorsSettings[$str])) ? $instructorsSettings[$str] : '' }}">
                    </div>
                @endforeach
                <div class="text-muted text-small mt-1">{{ trans('update.saas_package_condition_hint') }}</div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>

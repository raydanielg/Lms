
<div class="tab-pane mt-3 fade" id="organizations" role="tabpanel" aria-labelledby="organizations-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="{{ \App\Models\Setting::$registrationPackagesOrganizationsName }}">

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0 d-flex align-items-center">
                        <input type="hidden" name="value[status]" value="0">
                        <input type="checkbox" name="value[status]" id="organizationsStatusSwitch" value="1" {{ (!empty($organizationsSettings) and !empty($organizationsSettings['status']) and $organizationsSettings['status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="organizationsStatusSwitch">{{ trans('update.registration_packages_organizations_status') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">{{ trans('update.registration_packages_organizations_status_hint') }}</div>
                </div>
                <h2 class="section-title">{{ trans('update.organization_default_values') }}</h2>
                @foreach(['instructors_count','students_count','courses_capacity','courses_count','meeting_count','product_count'] as $str)
                    <div class="form-group">
                        <label>{{ trans('update.'.$str) }}</label>
                        <input type="text" class="form-control" name="value[{{ $str }}]" value="{{ (!empty($organizationsSettings) and isset($organizationsSettings[$str])) ? $organizationsSettings[$str] : '' }}">
                        <div class="text-muted text-small mt-1">{{ trans('update.saas_package_condition_hint') }}</div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>

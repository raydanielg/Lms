@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp

@push('styles_top')

@endpush

<div class="tab-pane mt-3 fade" id="security" role="tabpanel" aria-labelledby="security-tab">

    <form action="{{ getAdminPanelUrl() }}/settings/security" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="general">
        <input type="hidden" name="security" value="security">

        <div class="row">
            <div class="col-12 col-md-6">

            <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[content_translate]" value="0">
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentTranslate">{{ trans('update.device_limit') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">{{ trans('update.device_limit_hint') }}</div>
                    <div class="text-muted text-small mt-1">Paid Plugin</div>
                </div>

                <div class="js-device-limit-number {{ (!empty($itemValue) and !empty($itemValue['login_device_limit']) and $itemValue['login_device_limit']) ? '' : 'd-none' }}">
                    <div class="form-group">
                        <label class="input-label">{{ trans('update.number_of_allowed_devices') }}</label>
                        <input type="number" name="value[number_of_allowed_devices]" id="number_of_allowed_devices"
                               value="{{ (!empty($itemValue) and !empty($itemValue['number_of_allowed_devices'])) ? $itemValue['number_of_allowed_devices'] : 1 }}"
                               class="form-control"/>
                        <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.number_of_allowed_devices_hint') }}</p>
                    </div>


                    @include('admin.includes.delete_button',[
                        'url' => getAdminPanelUrl("/settings/reset-users-login-count"),
                        'noBtnTransparent' => true,
                        'btnClass' => 'btn btn-danger text-white',
                        'btnText' => trans('update.reset_users_login_count'),
                    ])

                </div>

                <h5 class="mt-5">{{ trans('update.captcha_settings') }}</h5>
                @php
                    $captchaSwitchs = ['captcha_for_admin_login', 'captcha_for_admin_forgot_pass', 'captcha_for_login', 'captcha_for_register', 'captcha_for_forgot_pass']
                @endphp

                @foreach($captchaSwitchs as $captchaSwitch)
                    <div class="form-group custom-switches-stacked">
                        <label class="custom-switch pl-0 mb-0">
                            <input type="hidden" name="value[{{ $captchaSwitch }}]" value="0">
                            <input type="checkbox" name="value[{{ $captchaSwitch }}]"
                                   id="captchaSwitch{{ $captchaSwitch }}" value="1"
                                   {{ (!empty($itemValue) and !empty($itemValue[$captchaSwitch]) and $itemValue[$captchaSwitch]) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                            <span class="custom-switch-indicator"></span>
                            <label class="custom-switch-description mb-0 cursor-pointer"
                                   for="captchaSwitch{{ $captchaSwitch }}">{{ trans('update.'.$captchaSwitch) }}</label>
                        </label>
                        <div class="text-muted text-small">{{ trans('update.'.$captchaSwitch.'_hint') }}</div>
                    </div>
                @endforeach


                <h5 class="mt-5">{{ trans('update.admin_panel_url') }}</h5>

                <div class="form-group mt-2">
                    <label class="input-label">{{ trans('admin/main.url') }}</label>
                    <input type="text" name="value[admin_panel_url]" id="admin_panel_url"
                           value="{{ (!empty($itemValue) and !empty($itemValue['admin_panel_url'])) ? $itemValue['admin_panel_url'] : 'admin' }}"
                           class="form-control" required/>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.admin_panel_url_hint') }}</p>
                </div>


            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>

@push('scripts_bottom')

@endpush

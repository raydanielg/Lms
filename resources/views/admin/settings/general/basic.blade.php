@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp

<div class="tab-pane mt-3 fade @if(empty($social)) show active @endif" id="basic" role="tabpanel" aria-labelledby="basic-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="general">
                <input type="hidden" name="name" value="general">

                <div class="form-group">
                    <label>{{ trans('admin/main.site_name') }}</label>
                    <input type="text" name="value[site_name]" value="{{ (!empty($itemValue) and !empty($itemValue['site_name'])) ? $itemValue['site_name'] : old('site_name') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.site_email') }}</label>
                    <input type="text" name="value[site_email]" value="{{ (!empty($itemValue) and !empty($itemValue['site_email'])) ? $itemValue['site_email'] : old('site_email') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.site_phone') }}</label>
                    <input type="text" name="value[site_phone]" value="{{ (!empty($itemValue) and !empty($itemValue['site_phone'])) ? $itemValue['site_phone'] : old('site_phone') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.site_language') }}</label>
                    <select name="value[site_language]" class="form-control select2" data-placeholder="{{ trans('admin/main.site_language') }}">
                        <option value=""></option>
                        @foreach(getLanguages() as $key => $language)
                            <option value="{{ $key }}" @if((!empty($itemValue) and !empty($itemValue['site_language'])) and $itemValue['site_language'] == $key) selected @endif >{{ $language }}</option>
                        @endforeach
                    </select>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.default_language_hint') }}</div>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.register_method') }}</label>
                    <select name="value[register_method]" class="form-control">
                        <option value="email" @if(!empty($itemValue) and !empty($itemValue['register_method']) and $itemValue['register_method'] == 'email') selected @endif>{{ trans('admin/main.email') }}</option>
                        <option>{{ trans('admin/main.sms') }} (Paid Plugin)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.default_time_zone') }}</label>
                    <select name="value[default_time_zone]" class="form-control select2">
                        <option value="" disabled @if(empty($itemValue) or empty($itemValue['default_time_zone'])) selected @endif>{{ trans('admin/main.select') }}</option>
                        @foreach(getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}" @if(!empty($itemValue) and !empty($itemValue['default_time_zone']) and $itemValue['default_time_zone'] == $timezone) selected @endif>{{ $timezone }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.date_format') }}</label>
                    <select name="value[date_format]" class="form-control">
                        <option value="textual" @if(!empty($itemValue) and !empty($itemValue['date_format']) and $itemValue['date_format'] == 'textual') selected @endif>{{ trans('update.textual') }} (18 Dec 2021)</option>
                        <option value="numerical" @if(!empty($itemValue) and !empty($itemValue['date_format']) and $itemValue['date_format'] == 'numerical') selected @endif>{{ trans('update.numerical') }} (18/12/2021)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.time_format') }}</label>
                    <select name="value[time_format]" class="form-control">
                        <option value="24_hours" @if(!empty($itemValue) and !empty($itemValue['time_format']) and $itemValue['time_format'] == '24_hours') selected @endif>{{ trans('update.24_hours') }}</option>
                        <option value="12_hours" @if(!empty($itemValue) and !empty($itemValue['time_format']) and $itemValue['time_format'] == '12_hours') selected @endif>{{ trans('update.12_hours') }} (AM/PM)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.user_languages_lists') }}</label>
                    <select name="value[user_languages][]" multiple class="form-control select2" data-placeholder="{{ trans('admin/main.user_languages_lists') }}">
                        <option value=""></option>
                        @foreach(getLanguages() as $key => $language)
                            <option value="{{ $key }}" @if((!empty($itemValue) and !empty($itemValue['user_languages']) and is_array($itemValue['user_languages'])) and in_array($key, $itemValue['user_languages'])) selected @endif >{{ $language }}</option>
                        @endforeach
                    </select>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.user_languages_lists_hint') }}</div>
                </div>


                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.rtl_languages') }}</label>
                    <select name="value[rtl_languages][]" multiple class="form-control select2" data-placeholder="{{ trans('admin/main.rtl_languages') }}">
                        <option value=""></option>
                        @foreach(getLanguages() as $key => $language)
                            <option value="{{ $key }}" @if((!empty($itemValue) and !empty($itemValue['rtl_languages']) and is_array($itemValue['rtl_languages'])) and in_array($key, $itemValue['rtl_languages'])) selected @endif >{{ $language }}</option>
                        @endforeach
                    </select>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.rtl_languages_hint') }}</div>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.fav_icon') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="fav_icon" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="value[fav_icon]" id="fav_icon" value="{{ (!empty($itemValue) and !empty($itemValue['fav_icon'])) ? $itemValue['fav_icon'] : old('fav_icon') }}" class="form-control" placeholder="{{ trans('admin/main.fav_icon_placeholder') }}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.logo') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="logo" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="value[logo]" id="logo" value="{{ (!empty($itemValue) and !empty($itemValue['logo'])) ? $itemValue['logo'] : old('logo') }}" class="form-control" placeholder="{{ trans('admin/main.logo_placeholder') }}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.footer_logo') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="footer_logo" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="value[footer_logo]" id="footer_logo" value="{{ (!empty($itemValue) and !empty($itemValue['footer_logo'])) ? $itemValue['footer_logo'] : old('footer_logo') }}" class="form-control" placeholder="{{ trans('admin/main.footer_logo_placeholder') }}"/>
                    </div>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[rtl_layout]" value="0">
                        <input type="checkbox" name="value[rtl_layout]" id="rtlSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['rtl_layout']) and $itemValue['rtl_layout']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="rtlSwitch">{{ trans('admin/main.rtl_layout') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[preloading]" value="0">
                        <input type="checkbox" name="value[preloading]" id="preloadingSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['preloading']) and $itemValue['preloading']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="preloadingSwitch">{{ trans('admin/main.preloading') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[hero_section1]" value="0">
                        <input type="checkbox" name="value[hero_section1]" id="heroSection1" value="1" {{ (!empty($itemValue) and !empty($itemValue['hero_section1']) and $itemValue['hero_section1']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="heroSection1">{{ trans('admin/main.main_home_hero') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[hero_section2]" value="0">
                        <input type="checkbox" name="value[hero_section2]" id="heroSection2" value="1" {{ (!empty($itemValue) and !empty($itemValue['hero_section2']) and $itemValue['hero_section2']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="heroSection2">{{ trans('admin/main.main_home_hero2') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[content_translate]" value="0">
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentTranslate">{{ trans('update.multi_language_content') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">Paid Plugin</div>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[app_debugbar]" value="0">
                        <input type="checkbox" name="value[app_debugbar]" id="appDebugbarSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['app_debugbar']) and $itemValue['app_debugbar']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="appDebugbarSwitch">{{ trans('update.app_debugbar') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">{{ trans('update.app_debugbar_hint') }}</div>
                </div>


                <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>


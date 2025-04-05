<div class="tab-pane mt-3 fade @if(empty($becomeInstructor) and (empty(request()->get('tab')))) active show @endif" id="general" role="tabpanel" aria-labelledby="general-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/update' }}" method="Post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label>{{ trans('/admin/main.full_name') }}</label>
                    <input type="text" name="full_name"
                           class="form-control  @error('full_name') is-invalid @enderror"
                           value="{{ !empty($user) ? $user->full_name : old('full_name') }}"
                           placeholder="{{ trans('admin/main.create_field_full_name_placeholder') }}"/>
                    @error('full_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                @can('admin_update_user_role_in_edit_page')
                    <div class="form-group">
                        <label>{{ trans('/admin/main.role_name') }}</label>
                        <select class="form-control @error('role_id') is-invalid @enderror" id="roleId" name="role_id">
                            <option disabled {{ empty($user) ? 'selected' : '' }}>{{ trans('admin/main.select_role') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ (!empty($user) and $user->role_id == $role->id) ? 'selected' :''}}>{{ $role->caption }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                @endcan

                <div class="form-group">
                    <label class="input-label">{{ trans('update.timezone') }}</label>
                    <select name="timezone" class="form-control select2" data-allow-clear="false">
                        <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>{{ trans('public.select') }}</option>
                        @foreach(getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}" @if(!empty($user) and $user->timezone == $timezone) selected @endif>{{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                @if(!empty($currencies) and count($currencies))
                    @php
                        $userCurrency = currency($user);
                    @endphp

                    <div class="form-group">
                        <label class="input-label">{{ trans('update.currency') }}</label>
                        <select name="currency" class="form-control select2" data-allow-clear="false">
                            @foreach($currencies as $currencyItem)
                                <option value="{{ $currencyItem->currency }}" {{ ($userCurrency == $currencyItem->currency) ? 'selected' : '' }}>{{ currenciesLists($currencyItem->currency) }} ({{ currencySign($currencyItem->currency) }})</option>
                            @endforeach
                        </select>
                        @error('currency')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                @endif

                @if($user->isUser() || $user->isTeacher())
                    <div class="form-group">
                        <label class="input-label">{{ trans('admin/main.organization') }}</label>
                        <select name="organ_id" data-search-option="just_organization_role" class="form-control search-user-select2"
                                data-placeholder="{{ trans('admin/main.search') }} {{ trans('admin/main.organization') }}">

                            @if(!empty($user) and !empty($user->organization))
                                <option value="{{ $user->organization->id }}" selected>{{ $user->organization->full_name }}</option>
                            @endif
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label for="username">{{ trans('admin/main.email') }}:</label>
                    <input name="email" type="text" id="username" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">{{ trans('admin/main.mobile') }}:</label>
                    <input name="mobile" type="text" value="{{ $user->mobile }}" class="form-control @error('mobile') is-invalid @enderror">
                    @error('mobile')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.password') }}</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"/>
                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.bio') }}</label>
                    <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ $user->bio }}</textarea>
                    @error('bio')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('site.about') }}</label>
                    <textarea name="about" rows="6" class="form-control @error('about') is-invalid @enderror">{{ $user->about }}</textarea>
                    @error('about')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('update.certificate_additional') }}</label>
                    <input name="certificate_additional" value="{{ $user->certificate_additional }}" class="form-control @error('certificate_additional') is-invalid @enderror"/>
                    @error('certificate_additional')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ trans('/admin/main.status') }}</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                        <option disabled {{ empty($user) ? 'selected' : '' }}>{{ trans('admin/main.select_status') }}</option>

                        @foreach (\App\User::$statuses as $status)
                            <option value="{{ $status }}" {{ !empty($user) && $user->status === $status ? 'selected' :''}}>{{  $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="language" class="form-control">
                        <option value="">{{ trans('auth.language') }}</option>
                        @foreach($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if(!empty($user) and mb_strtolower($user->language) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                        @endforeach
                    </select>
                    @error('language')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="ban" value="0">
                        <input type="checkbox" name="ban" id="banSwitch" value="1" {{ (!empty($user) and $user->ban) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="banSwitch">{{ trans('admin/main.ban') }}</label>
                    </label>
                </div>

                <div class="row {{ (($user->ban) or (old('ban') == 'on')) ? '' : 'd-none' }}" id="banSection">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.from') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt"></i>
                                                                    </span>
                                </div>
                                <input type="text" name="ban_start_at" class="form-control datepicker @error('ban_start_at') is-invalid @enderror" value="{{ !empty($user->ban_start_at) ? dateTimeFormat($user->ban_start_at,'Y/m/d') :'' }}"/>
                                @error('ban_start_at')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.to') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt"></i>
                                                                    </span>
                                </div>
                                <input type="text" name="ban_end_at" class="form-control datepicker @error('ban_end_at') is-invalid @enderror" value="{{ !empty($user->ban_end_at) ? dateTimeFormat($user->ban_end_at,'Y/m/d') :'' }}"/>
                                @error('ban_end_at')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="verified" value="0">
                        <input type="checkbox" name="verified" id="verified" value="1" {{ (!empty($user) and $user->verified) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="verified">{{ trans('admin/main.enable_blue_badge') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="affiliate" value="0">
                        <input type="checkbox" name="affiliate" id="affiliateSwitch" value="1" {{ (!empty($user) and $user->affiliate) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="affiliateSwitch">{{ trans('panel.affiliate') }}</label>
                    </label>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="can_create_store" value="0">
                        <input type="checkbox" name="can_create_store" id="canCreateStoreSwitch" value="1" {{ (!empty($user) and $user->can_create_store) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="canCreateStoreSwitch">{{ trans('update.store') }}</label>
                    </label>
                    <div class="text-muted text-small">{{ trans('update.admin_user_edit_can_create_store_hint') }}</div>
                </div>

                <div class="form-group custom-switches-stacked mt-2">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="access_content" value="1">
                        <input type="checkbox" name="access_content" id="contentAccessLimitationSwitch" value="0" {{ (!empty($user) and !$user->access_content) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentAccessLimitationSwitch">{{ trans('update.content_access_limitation') }}</label>
                    </label>
                    <div class="text-muted text-small">{{ trans('update.admin_user_edit_content_access_limitation_hint') }}</div>
                </div>

                @if(!empty($user) and !$user->isUser())
                    <div class="form-group custom-switches-stacked mt-2">
                        <label class="custom-switch pl-0">
                            <input type="hidden" name="enable_ai_content" value="0">
                            <input type="checkbox" name="enable_ai_content" id="aiContentLimitationSwitch" value="1" {{ (!empty($user) and $user->enable_ai_content) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                            <span class="custom-switch-indicator"></span>
                            <label class="custom-switch-description mb-0 cursor-pointer" for="aiContentLimitationSwitch">{{ trans('update.enable_ai_content') }}</label>
                        </label>
                        <div class="text-muted text-small">{{ trans('update.admin_user_edit_enable_ai_content_hint') }}</div>
                    </div>
                @endif

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

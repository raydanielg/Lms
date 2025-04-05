<div class="row">
    <div class="col-12 col-md-5">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control {{ !empty($form) ? 'js-edit-content-locale' : '' }}">
                    @foreach($userLanguages as $lang => $language)
                        <option value="{{ $lang }}"
                                @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }} {{ (!empty($definedLanguage) and is_array($definedLanguage) and in_array(mb_strtolower($lang), $definedLanguage)) ? '('. trans('panel.content_defined') .')' : '' }}</option>
                    @endforeach
                </select>
                @error('locale')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        @else
            <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
        @endif

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" value="{{ !empty($form) ? $form->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('admin/main.url') }}</label>
            <input type="text" name="url" value="{{ !empty($form) ? $form->url : old('url') }}" class="form-control @error('url')  is-invalid @enderror" placeholder=""/>
            @error('url')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.cover_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="cover_image" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="cover" id="cover_image" value="{{ !empty($form) ? $form->cover : old('cover') }}" class="form-control @error('cover')  is-invalid @enderror"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="cover_image">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('cover')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="image" id="image" value="{{ !empty($form) ? $form->image : old('image') }}" class="form-control @error('image')  is-invalid @enderror"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="image">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('image')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('update.heading_title') }}</label>
            <input type="text" name="heading_title" value="{{ !empty($form) ? $form->heading_title : old('heading_title') }}" class="form-control @error('heading_title')  is-invalid @enderror" placeholder=""/>
            @error('heading_title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea id="summernote" name="description" class="summernote form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('forms.webinar_description_placeholder') }}">{!! !empty($form) ? $form->description : old('description')  !!}</textarea>
            @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12 col-md-5">

        <div class="form-group d-flex align-items-center">
            <label class="" for="enableLoginSwitch">{{ trans('update.enable_login') }}</label>
            <div class="custom-control custom-switch ml-3">
                <input type="checkbox" name="enable_login" class="custom-control-input" id="enableLoginSwitch" {{ (!empty($form) and $form->enable_login) ? 'checked' : '' }}>
                <label class="custom-control-label" for="enableLoginSwitch"></label>
            </div>
        </div>


        @php
            $selectedRoleIds = !empty($form) ? $form->roles->pluck('id')->toArray() : [];
        @endphp

        <div class="js-enable-login-fields {{ (!empty($form) and $form->enable_login) ? '' : 'd-none' }}">

            <div class="form-group ">
                <label class="input-label">{{ trans('admin/main.role') }}</label>

                <select name="role_ids[]" class="custom-select select2" multiple data-placeholder="{{ trans('update.select_user_roles') }}">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ in_array($role->id, $selectedRoleIds) ? 'selected' : '' }}>{{ $role->caption }}</option>
                    @endforeach
                </select>

                <div class="text-muted text-small mt-1">{{ trans('update.forms_user_roles_hint') }}</div>
                @error('role_ids')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group ">
                <label class="input-label">{{ trans('admin/main.users') }}</label>

                <select name="users_ids[]" class="custom-select search-user-select2" multiple data-placeholder="{{ trans('public.search_user') }}">

                    @if(!empty($form) and !empty($form->users))
                        @foreach($form->users as $formUser)
                            <option value="{{ $formUser->id }}" selected>{{ $formUser->full_name }}</option>
                        @endforeach
                    @endif
                </select>
                <div class="text-muted text-small mt-1">{{ trans('update.cashback_users_hint') }}</div>
                @error('users_ids')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            @php
                $selectedGroupIds = !empty($form) ? $form->userGroups->pluck('id')->toArray() : [];
            @endphp

            <div class="form-group ">
                <label class="input-label">{{ trans('admin/main.user_group') }}</label>

                <select name="group_ids[]" class="custom-select select2" multiple data-placeholder="{{ trans('admin/main.select_users_group') }}">

                    @foreach($userGroups as $userGroup)
                        <option value="{{ $userGroup->id }}" {{ in_array($userGroup->id, $selectedGroupIds) ? 'selected' : '' }}>{{ $userGroup->name }}</option>
                    @endforeach
                </select>
                <div class="text-muted text-small mt-1">{{ trans('update.cashback_user_groups_hint') }}</div>
                @error('group_ids')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group d-flex align-items-center">
                <label class="" for="enableResubmissionSwitch">{{ trans('update.enable_resubmission') }}</label>
                <div class="custom-control custom-switch ml-3">
                    <input type="checkbox" name="enable_resubmission" class="custom-control-input" id="enableResubmissionSwitch" {{ (!empty($form) and $form->enable_resubmission) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="enableResubmissionSwitch"></label>
                </div>
            </div>
            
        </div>




        <div class="form-group">
            <label class="input-label">{{ trans('admin/main.start_date') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="dateRangeLabel">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>

                <input type="text" name="start_date" class="form-control text-center datetimepicker"
                       aria-describedby="dateRangeLabel" autocomplete="off"
                       value="{{ (!empty($form) and !empty($form->start_date)) ? dateTimeFormat($form->start_date, 'Y-m-d H:i', false) : old('start_date') }}"/>
                @error('start_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="text-muted text-small mt-1">{{ trans('update.cashback_start_date_hint') }}</div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('admin/main.end_date') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="dateRangeLabel">
                        <i class="fa fa-calendar"></i>
                    </span>
                </div>

                <input type="text" name="end_date" class="form-control text-center datetimepicker"
                       aria-describedby="dateRangeLabel" autocomplete="off"
                       value="{{ (!empty($form) and !empty($form->end_date)) ? dateTimeFormat($form->end_date, 'Y-m-d H:i', false) : old('end_date') }}"/>

                @error('end_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="text-muted text-small mt-1">{{ trans('update.cashback_end_date_hint') }}</div>
        </div>

    </div>
</div>

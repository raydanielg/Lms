<div class="row">
    <div class="col-12 col-md-5">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control {{ !empty($rule) ? 'js-edit-content-locale' : '' }}">
                    @foreach($userLanguages as $lang => $language)
                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }} {{ (!empty($definedLanguage) and is_array($definedLanguage) and in_array(mb_strtolower($lang), $definedLanguage)) ? '('. trans('panel.content_defined') .')' : '' }}</option>
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
            <input type="text" name="title" value="{{ !empty($rule) ? $rule->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>


        <div class="form-group ">
            <label class="input-label">{{ trans('admin/main.users') }}</label>

            <select name="users_ids[]" class="custom-select search-user-select2" multiple data-placeholder="{{ trans('public.search_user') }}">

                @if(!empty($rule) and !empty($rule->users))
                    @foreach($rule->users as $ruleUser)
                        <option value="{{ $ruleUser->id }}" selected>{{ $ruleUser->full_name }}</option>
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
            $selectedGroupIds = !empty($rule) ? $rule->userGroups->pluck('id')->toArray() : [];
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
                       value="{{ (!empty($rule) and !empty($rule->start_date)) ? dateTimeFormat($rule->start_date, 'Y-m-d H:i', false) : dateTimeFormat(time(), 'Y-m-d H:i', false) }}"/>
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
                       value="{{ (!empty($rule) and !empty($rule->end_date)) ? dateTimeFormat($rule->end_date, 'Y-m-d H:i', false) : old('end_date') }}"/>
                
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

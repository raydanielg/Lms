<div class="row">
    <div class="col-12 col-md-5">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control {{ !empty($installment) ? 'js-edit-content-locale' : '' }}">
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
            <input type="text" name="title" value="{{ !empty($installment) ? $installment->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_title_hint') }}</div>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('update.main_title') }}</label>
            <input type="text" name="main_title" value="{{ !empty($installment) ? $installment->main_title : old('main_title') }}" class="form-control @error('main_title')  is-invalid @enderror" placeholder=""/>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_main_title_hint') }}</div>
            @error('main_title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea name="description" rows="5" class="form-control @error('description')  is-invalid @enderror">{{ !empty($installment) ? $installment->description : old('description') }}</textarea>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_description_hint') }}</div>
            @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('update.banner') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="banner" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="banner" id="banner" value="{{ !empty($installment) ? $installment->banner : old('banner') }}" class="form-control @error('banner')  is-invalid @enderror"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="banner">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>

                @error('banner')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="text-muted text-small mt-1">{{ trans('update.installment_banner_hint') }}</div>
        </div>


        {{-- Options --}}
        <div id="installmentOptionsCard" class="mt-3">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="fs-15">{{ trans('update.options') }}</h5>

                <button type="button" class="js-add-btn btn btn-success">
                    <i class="fa fa-plus"></i>
                    {{ trans('update.add_option') }}
                </button>
            </div>

            @if(!empty($installment) and !empty($installment->options))
                @php
                    $installmentOptions = explode(\App\Models\Installment::$optionsExplodeKey, $installment->options);
                @endphp
                @foreach($installmentOptions as $k => $option)
                    <div class="input-group mt-2">
                        <input type="text" name="installment_options[]"
                               class="form-control w-auto flex-grow-1" value="{{ $option }}"/>

                        <div class="input-group-append">
                            <button type="button" class="js-remove-btn btn btn-danger"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                @endforeach
            @endif

            <div id="installmentOptionsMainRow" class="d-none">
                <div class="input-group mt-2">
                    <input type="text" name="installment_options[]"
                           class="form-control w-auto flex-grow-1"/>

                    <div class="input-group-append">
                        <button type="button" class="js-remove-btn btn btn-danger"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>

        </div>
        <div class="text-muted text-small mt-1">{{ trans('update.installment_options_hint') }}</div>
        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.capacity') }}</label>
            <input type="number" name="capacity" value="{{ !empty($installment) ? $installment->capacity : old('capacity') }}" class="form-control @error('capacity')  is-invalid @enderror" placeholder="Empty means inactive this mode"/>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_capacity_hint') }}</div>
            @error('capacity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        @php
            $selectedGroupIds = !empty($installment) ? $installment->userGroups->pluck('group_id')->toArray() : [];
        @endphp

        <div class="form-group ">
            <label class="input-label">{{ trans('admin/main.user_group') }}</label>

            <select name="group_ids[]" class="custom-select select2" multiple data-placeholder="{{ trans('admin/main.select_users_group') }}">

                @foreach($userGroups as $userGroup)
                    <option value="{{ $userGroup->id }}" {{ in_array($userGroup->id, $selectedGroupIds) ? 'selected' : '' }}>{{ $userGroup->name }}</option>
                @endforeach
            </select>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_user_group_hint') }}</div>
            @error('group_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
            <p class="text-muted font-12 mt-1">{{ trans('') }}</p>
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
                       value="{{ (!empty($installment) and !empty($installment->start_date)) ? dateTimeFormat($installment->start_date, 'Y-m-d H:i', false) : old('start_date') }}"/>


               @error('start_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_start_date_hint') }}</div>
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
                       value="{{ (!empty($installment) and !empty($installment->end_date)) ? dateTimeFormat($installment->end_date, 'Y-m-d H:i', false) : old('end_date') }}"/>


               @error('end_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <div class="text-muted text-small mt-1">{{ trans('update.installment_end_date_hint') }}</div>
        </div>

    </div>
</div>

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.css">
@endpush

<div class="row">
    <div class="col-12 col-md-6 mt-15">


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.capacity') }}</label>
            <input type="number" name="capacity" value="{{ (!empty($webinar) and !empty($webinar->capacity)) ? $webinar->capacity : old('capacity') }}" class="form-control @error('capacity')  is-invalid @enderror" placeholder="{{ trans('forms.capacity_placeholder') }}"/>
            @error('capacity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
            <p class="text-gray mt-5 font-12">{{  trans('forms.empty_means_unlimited')  }}</p>
        </div>

        <div class="row mt-15">

            @if($webinar->isWebinar())
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="input-label">{{ trans('public.start_date') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text" id="dateInputGroupPrepend">
                                <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                            </span>
                            </div>
                            <input type="text" name="start_date" value="{{ (!empty($webinar) and $webinar->start_date) ? dateTimeFormat($webinar->start_date, 'Y-m-d H:i', false, false, $webinar->timezone) : old('start_date') }}"
                                   class="form-control @error('start_date')  is-invalid @enderror datetimepicker" aria-describedby="dateInputGroupPrepend"/>
                            @error('start_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12 @if($webinar->isWebinar()) col-md-6 @endif">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.duration') }} ({{ trans('public.minutes') }})</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="timeInputGroupPrepend">
                                <i data-feather="clock" width="18" height="18" class="text-white"></i>
                            </span>
                        </div>


                        <input type="text" name="duration" value="{{ (!empty($webinar) and !empty($webinar->duration)) ? $webinar->duration : old('duration') }}" class="form-control @error('duration')  is-invalid @enderror"/>
                        @error('duration')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        @if($webinar->isWebinar() and getFeaturesSettings('timezone_in_create_webinar'))
            @php
                $selectedTimezone = getGeneralSettings('default_time_zone');

                if (!empty($webinar->timezone)) {
                    $selectedTimezone = $webinar->timezone;
                } elseif (!empty($authUser) and !empty($authUser->timezone)) {
                    $selectedTimezone = $authUser->timezone;
                }
            @endphp

            <div class="form-group">
                <label class="input-label">{{ trans('update.timezone') }}</label>
                <select name="timezone" class="form-control select2" data-allow-clear="false">
                    @foreach(getListOfTimezones() as $timezone)
                        <option value="{{ $timezone }}" @if($selectedTimezone == $timezone) selected @endif>{{ $timezone }}</option>
                    @endforeach
                </select>
                @error('timezone')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        @endif

        @if(!empty(getFeaturesSettings("course_forum_status")))
            <div class="form-group mt-30 d-flex align-items-center justify-content-between mb-5">
                <label class="cursor-pointer input-label" for="forumSwitch">{{ trans('update.course_forum') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="forum" class="custom-control-input" id="forumSwitch" {{ !empty($webinar) && $webinar->forum ? 'checked' : (old('forum') ? 'checked' : '')  }}>
                    <label class="custom-control-label" for="forumSwitch"></label>
                </div>
            </div>

            <div>
                <p class="font-12 text-gray">- {{ trans('update.panel_course_forum_hint') }}</p>
            </div>
        @endif

        <div class="form-group mt-30 d-flex align-items-center justify-content-between">
            <label class="cursor-pointer input-label" for="supportSwitch">{{ trans('webinars.support') }}</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" name="support" class="custom-control-input" id="supportSwitch" {{ ((!empty($webinar) && $webinar->support) or old('support') == 'on') ? 'checked' :  '' }}>
                <label class="custom-control-label" for="supportSwitch"></label>
            </div>
        </div>

        @if(!empty(getCertificateMainSettings("status")))
            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label" for="certificateSwitch">{{ trans('update.include_certificate') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="certificate" class="custom-control-input" id="certificateSwitch" {{ ((!empty($webinar) && $webinar->certificate) or old('certificate') == 'on') ? 'checked' :  '' }}>
                    <label class="custom-control-label" for="certificateSwitch"></label>
                </div>
            </div>

            <div>
                <p class="font-12 text-gray">- {{ trans('update.certificate_completion_hint') }}</p>
            </div>
        @endif

        <div class="form-group mt-30 d-flex align-items-center justify-content-between">
            <label class="cursor-pointer input-label" for="downloadableSwitch">{{ trans('home.downloadable') }}</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" name="downloadable" class="custom-control-input" id="downloadableSwitch" {{ ((!empty($webinar) && $webinar->downloadable) or old('downloadable') == 'on') ? 'checked' : '' }}>
                <label class="custom-control-label" for="downloadableSwitch"></label>
            </div>
        </div>

        <div class="form-group mt-30 d-flex align-items-center justify-content-between">
            <label class="cursor-pointer input-label" for="partnerInstructorSwitch">{{ trans('public.partner_instructor') }}</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" name="partner_instructor" class="custom-control-input" id="partnerInstructorSwitch" {{ ((!empty($webinar) && $webinar->partner_instructor) or old('partner_instructor') == 'on') ? 'checked' : ''  }}>
                <label class="custom-control-label" for="partnerInstructorSwitch"></label>
            </div>
        </div>


        <div id="partnerInstructorInput" class="form-group mt-15 {{ ((!empty($webinar) && $webinar->partner_instructor) or old('partner_instructor') == 'on') ? '' : 'd-none' }}">
            <label class="input-label d-block">{{ trans('public.select_a_partner_teacher') }}</label>

            <select name="partners[]" class="form-control panel-search-user-select2 @error('partners')  is-invalid @enderror" multiple="" data-search-option="just_teachers" data-placeholder="{{ trans('public.search_instructor') }}">
                @if(!empty($webinar->webinarPartnerTeacher))
                    @foreach($webinar->webinarPartnerTeacher as $partnerTeacher)
                        <option selected value="{{ $partnerTeacher->teacher->id }}">{{ $partnerTeacher->teacher->full_name }}</option>
                    @endforeach
                @endif
            </select>
            <div class="text-gray font-12 mt-1">{{ trans('admin/main.invited_instructor_hint') }}</div>
            @error('partners')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label d-block">{{ trans('public.tags') }}</label>
            <input type="text" name="tags" data-max-tag="5" value="{{ !empty($webinar) ? implode(',',$webinarTags) : '' }}" class="form-control inputtags" placeholder="{{ trans('public.type_tag_name_and_press_enter') }} ({{ trans('forms.max') }} : 5)"/>
        </div>


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.category') }}</label>

            <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror" name="category_id" required>
                <option {{ (!empty($webinar) and !empty($webinar->category_id)) ? '' : 'selected' }} disabled>{{ trans('public.choose_category') }}</option>
                @foreach($categories as $category)
                    @if(!empty($category->subCategories) and $category->subCategories->count() > 0)
                        <optgroup label="{{  $category->title }}">
                            @foreach($category->subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ ((!empty($webinar) and $webinar->category_id == $subCategory->id) or old('category_id') == $subCategory->id) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $category->id }}" {{ ((!empty($webinar) and $webinar->category_id == $category->id) or old('category_id') == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
                    @endif
                @endforeach
            </select>
            @error('category_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

    </div>
</div>

<div class="form-group mt-15 {{ (!empty($webinarCategoryFilters) and count($webinarCategoryFilters)) ? '' : 'd-none' }}" id="categoriesFiltersContainer">
    <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
    <div id="categoriesFiltersCard" class="row mt-20">

        @if(!empty($webinarCategoryFilters) and count($webinarCategoryFilters))
            @foreach($webinarCategoryFilters as $filter)
                <div class="col-12 col-md-3">
                    <div class="webinar-category-filters">
                        <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                        <div class="py-10"></div>

                        @php
                            $webinarFilterOptions = $webinar->filterOptions->pluck('filter_option_id')->toArray();

                            if (!empty(old('filters'))) {
                                $webinarFilterOptions = array_merge($webinarFilterOptions, old('filters'));
                            }
                        @endphp

                        @foreach($filter->options as $option)
                            <div class="form-group mt-10 d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer font-14 text-gray" for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="filters[]" value="{{ $option->id }}" {{ ((!empty($webinarFilterOptions) && in_array($option->id, $webinarFilterOptions)) ? 'checked' : '') }} class="custom-control-input" id="filterOptions{{ $option->id }}">
                                    <label class="custom-control-label" for="filterOptions{{ $option->id }}"></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
@endpush

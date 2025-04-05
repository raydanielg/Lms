<div class="row">
    <div class="col-12 col-md-6">

        <div class="form-group mt-3">
            <label class="input-label">{{ trans('update.estimated_publish_date') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="dateInputGroupPrepend">
                        <i class="fa fa-calendar-alt "></i>
                    </span>
                </div>
                <input type="text" name="publish_date" value="{{ (!empty($upcomingCourse) and $upcomingCourse->publish_date) ? dateTimeFormat($upcomingCourse->publish_date, 'Y-m-d H:i', false, false, $upcomingCourse->timezone) : old('publish_date') }}" class="form-control @error('publish_date')  is-invalid @enderror datetimepicker" aria-describedby="dateInputGroupPrepend"/>
                @error('publish_date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        @php
            $selectedTimezone = getGeneralSettings('default_time_zone');

            if (!empty($upcomingCourse->timezone)) {
                $selectedTimezone = $upcomingCourse->timezone;
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

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.capacity') }}</label>
            <input type="number" name="capacity" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->capacity)) ? $upcomingCourse->capacity : old('capacity') }}" class="form-control @error('capacity')  is-invalid @enderror" placeholder="{{ trans('forms.capacity_placeholder') }}"/>
            <div class="text-muted text-small mt-1">{{ trans('admin/main.class_url_hint') }}</div>
            @error('capacity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.price') }} ({{ $currency }})</label>
            <input type="text" name="price" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->price)) ? convertPriceToUserCurrency($upcomingCourse->price) : old('price') }}" class="form-control @error('price')  is-invalid @enderror"/>
            @error('price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.duration') }} ({{ trans('public.minutes') }})</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="timeInputGroupPrepend">
                        <i class="fa fa-clock"></i>
                    </span>
                </div>

                <input type="number" name="duration" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->duration)) ? $upcomingCourse->duration : old('duration') }}" class="form-control @error('duration')  is-invalid @enderror" min="0"/>
                @error('duration')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('update.sections') }}</label>
            <input type="number" min="0" name="sections" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->sections)) ? $upcomingCourse->sections : old('sections') }}" class="form-control @error('sections')  is-invalid @enderror"/>
            <div class="text-muted text-small mt-1">{{ trans('update.upcoming_sections_hint') }}</div>
            @error('sections')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.parts') }}</label>
            <input type="number" min="0" name="parts" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->parts)) ? $upcomingCourse->parts : old('parts') }}" class="form-control @error('parts')  is-invalid @enderror"/>
            <div class="text-muted text-small mt-1">{{ trans('update.upcoming_parts_hint') }}</div>
            @error('parts')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('update.course_progress') }}</label>
            <input type="number" min="0" name="course_progress" value="{{ (!empty($upcomingCourse) and !empty($upcomingCourse->course_progress)) ? $upcomingCourse->course_progress : old('course_progress') }}" class="form-control @error('course_progress')  is-invalid @enderror" placeholder="{{ trans('update.progress_placeholder') }}"/>
            <div class="text-muted text-small mt-1">{{ trans('update.upcoming_progress_hint') }}</div>
            @error('course_progress')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="row">
            <div class="col-12 col-md-6">

                <div class="form-group d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="supportSwitch">{{ trans('webinars.support') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="support" class="custom-control-input" id="supportSwitch" {{ ((!empty($upcomingCourse) && $upcomingCourse->support) or old('support') == 'on') ? 'checked' :  '' }}>
                        <label class="custom-control-label" for="supportSwitch"></label>
                    </div>
                </div>

                <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="certificateSwitch">{{ trans('update.include_certificate') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="certificate" class="custom-control-input" id="certificateSwitch" {{ ((!empty($upcomingCourse) && $upcomingCourse->certificate) or old('certificate') == 'on') ? 'checked' :  '' }}>
                        <label class="custom-control-label" for="certificateSwitch"></label>
                    </div>
                </div>

                <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="quizzesSwitch">{{ trans('update.certificate_included') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="include_quizzes" class="custom-control-input" id="quizzesSwitch" {{ ((!empty($upcomingCourse) && $upcomingCourse->include_quizzes) or old('quizzes') == 'on') ? 'checked' :  '' }}>
                        <label class="custom-control-label" for="quizzesSwitch"></label>
                    </div>
                </div>

                <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="downloadableSwitch">{{ trans('home.downloadable') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="downloadable" class="custom-control-input" id="downloadableSwitch" {{ ((!empty($upcomingCourse) && $upcomingCourse->downloadable) or old('downloadable') == 'on') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="downloadableSwitch"></label>
                    </div>
                </div>

                <div class="form-group mt-30 d-flex align-items-center justify-content-between ">
                    <label class="cursor-pointer input-label" for="forumSwitch">{{ trans('update.course_forum') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="forum" class="custom-control-input" id="forumSwitch" {{ !empty($upcomingCourse) && $upcomingCourse->forum ? 'checked' : (old('forum') ? 'checked' : '')  }}>
                        <label class="custom-control-label" for="forumSwitch"></label>
                    </div>
                </div>

            </div>
        </div>

        {{-- Product Badges --}}
        @if(!empty($upcomingCourse))
            @include('admin.product_badges.content_include', ['itemTarget' => $upcomingCourse])
        @endif


        <div class="form-group mt-15">
            <label class="input-label d-block">{{ trans('public.tags') }}</label>
            <input type="text" name="tags" data-max-tag="5" value="{{ !empty($upcomingCourse) ? implode(',', $upcomingCourseTags) : '' }}" class="form-control inputtags" placeholder="{{ trans('public.type_tag_name_and_press_enter') }} ({{ trans('admin/main.max') }} : 5)"/>
        </div>


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.category') }}</label>

            <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror" name="category_id" required>
                <option {{ !empty($upcomingCourse) ? '' : 'selected' }} disabled>{{ trans('public.choose_category') }}</option>
                @foreach($categories as $category)
                    @if(!empty($category->subCategories) and count($category->subCategories))
                        <optgroup label="{{  $category->title }}">
                            @foreach($category->subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ (!empty($upcomingCourse) and $upcomingCourse->category_id == $subCategory->id) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $category->id }}" {{ (!empty($upcomingCourse) and $upcomingCourse->category_id == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
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

<div class="form-group mt-15 {{ (!empty($upcomingCourseCategoryFilters) and count($upcomingCourseCategoryFilters)) ? '' : 'd-none' }}" id="categoriesFiltersContainer">
    <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
    <div id="categoriesFiltersCard" class="row mt-3">

        @if(!empty($upcomingCourseCategoryFilters) and count($upcomingCourseCategoryFilters))
            @foreach($upcomingCourseCategoryFilters as $filter)
                <div class="col-12 col-md-3">
                    <div class="webinar-category-filters">
                        <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                        <div class="py-10"></div>

                        @php
                            $upcomingCourseFilterOptions = $upcomingCourse->filterOptions->pluck('filter_option_id')->toArray();

                            if (!empty(old('filters'))) {
                                $upcomingCourseFilterOptions = array_merge($upcomingCourseFilterOptions, old('filters'));
                            }
                        @endphp

                        @foreach($filter->options as $option)
                            <div class="form-group mt-3 d-flex align-items-center justify-content-between">
                                <label class="text-gray font-14" for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="filters[]" value="{{ $option->id }}" {{ ((!empty($upcomingCourseFilterOptions) && in_array($option->id, $upcomingCourseFilterOptions)) ? 'checked' : '') }} class="custom-control-input" id="filterOptions{{ $option->id }}">
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

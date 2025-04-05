
<div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">

    <div class="">
        <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('public.type') }}</h3>

        <div class="pt-10">
            @foreach(['webinar','course','text_lesson'] as $typeOption)
                <div class="d-flex align-items-center justify-content-between mt-20">
                    <label class="cursor-pointer" for="filterLanguage{{ $typeOption }}">{{ trans('webinars.'.$typeOption) }}</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="type[]" id="filterLanguage{{ $typeOption }}" value="{{ $typeOption }}" @if(in_array($typeOption, request()->get('type', []))) checked="checked" @endif class="custom-control-input">
                        <label class="custom-control-label" for="filterLanguage{{ $typeOption }}"></label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-25 pt-25 border-top border-gray300">
        <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('site.more_options') }}</h3>

        <div class="pt-10">
            @foreach(['supported_courses', 'quiz_included', 'certificate_included'] as $moreOption)
                <div class="d-flex align-items-center justify-content-between mt-20">
                    <label class="cursor-pointer" for="filterLanguage{{ $moreOption }}">{{ trans('update.show_only_'.$moreOption) }}</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="moreOptions[]" id="filterLanguage{{ $moreOption }}" value="{{ $moreOption }}" @if(in_array($moreOption, request()->get('moreOptions', []))) checked="checked" @endif class="custom-control-input">
                        <label class="custom-control-label" for="filterLanguage{{ $moreOption }}"></label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('site.filter_items') }}</button>
</div>

@php
    $urlParams = request()->all();
@endphp

@if(!empty($categoriesLists))
    @if(!empty($selectedCategory))
        <input type="hidden" name="category_id" value="{{ $selectedCategory->id }}">
    @endif

    <div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">

        <div class="">
            <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('categories.categories') }}</h3>

            <div class="pt-10">
                @foreach($categoriesLists as $categoryItem)
                    @if(!empty($categoryItem->subCategories) and count($categoryItem->subCategories))

                        <span class="d-block font-14 font-weight-bold  mt-20">{{ $categoryItem->title }}</span>

                        <div class="pl-10">
                            @foreach($categoryItem->subCategories as $subCategory)
                                @php
                                    $urlParams['category_id'] = $subCategory->id;
                                @endphp

                                <a href="/upcoming_courses?{{ http_build_query($urlParams) }}" class="d-flex align-items-center font-14 font-weight-normal mt-20 {{ (!empty($selectedCategory) and $selectedCategory->id == $subCategory->id) ? 'text-primary' : '' }}">
                                    @if(!empty($selectedCategory) and $selectedCategory->id == $subCategory->id)
                                        <i data-feather="chevron-right" width="20" height="20" class="mr-5"></i>
                                    @endif

                                    <span>{{ $subCategory->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        @php
                            $urlParams['category_id'] = $categoryItem->id;
                        @endphp

                        <a href="/upcoming_courses?{{ http_build_query($urlParams) }}" class="d-flex align-items-center font-14 font-weight-bold mt-20 {{ (!empty($selectedCategory) and $selectedCategory->id == $categoryItem->id) ? 'text-primary' : '' }}">
                            @if(!empty($selectedCategory) and $selectedCategory->id == $categoryItem->id)
                                <i data-feather="chevron-right" width="20" height="20" class="mr-5"></i>
                            @endif

                            <span>{{ $categoryItem->title }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

@if(!empty($selectedCategory) and !empty($selectedCategory->filters))
    <div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">

        @foreach($selectedCategory->filters as $filter)
            <div class="mt-25 pt-25 border-top border-gray300">
                <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ $filter->title }}</h3>

                @if(!empty($filter->options))
                    <div class="pt-10">
                        @foreach($filter->options as $option)
                            <div class="d-flex align-items-center justify-content-between mt-20">
                                <label class="cursor-pointer" for="filterLanguage{{ $option->id }}">{{ $option->title }}</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="filter_option[]" id="filterLanguage{{ $option->id }}" value="{{ $option->id }}" @if(in_array($option->id, request()->get('filter_option', []))) checked="checked" @endif class="custom-control-input">
                                    <label class="custom-control-label" for="filterLanguage{{ $option->id }}"></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('site.filter_items') }}</button>
    </div>
@endif

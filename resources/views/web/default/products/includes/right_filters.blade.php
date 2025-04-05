@if(!empty($authUser) and ($authUser->isOrganization() or $authUser->isTeacher()))
    <a href="/panel/store/products/new" class="mt-20 btn btn-primary btn-flex align-items-center w-100">
        <i data-feather="shopping-bag" width="20" height="20" class="mr-5"></i>
        <span>{{ trans('update.add_new_product') }}</span>
    </a>
@endif

<div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">
    <div class="">
        <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('public.type') }}</h3>

        <div class="pt-10">
            @foreach(['virtual','physical'] as $typeOption)
                <div class="d-flex align-items-center justify-content-between mt-20">
                    <label class="cursor-pointer" for="filterTypes{{ $typeOption }}">{{ trans('update.product_type_'.$typeOption) }}</label>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="type[]" id="filterTypes{{ $typeOption }}" value="{{ $typeOption }}" @if(in_array($typeOption, request()->get('type', []))) checked="checked" @endif class="custom-control-input">
                        <label class="custom-control-label" for="filterTypes{{ $typeOption }}"></label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('site.filter_items') }}</button>
</div>


<div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">
    <div class="">
        <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('update.options') }}</h3>

        <div class="pt-10">

            <div class="d-flex align-items-center justify-content-between mt-20">
                <label class="cursor-pointer" for="filterOptionsOnlyAvailableProducts">{{ trans('update.only_available_products') }}</label>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="options[]" id="filterOptionsOnlyAvailableProducts" value="only_available" @if(in_array('only_available', request()->get('options', []))) checked="checked" @endif class="custom-control-input">
                    <label class="custom-control-label" for="filterOptionsOnlyAvailableProducts"></label>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-20">
                <label class="cursor-pointer" for="filterOptionsWithPoint">{{ trans('update.products_with_points') }}</label>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="options[]" id="filterOptionsWithPoint" value="with_point" @if(in_array('with_point', request()->get('options', []))) checked="checked" @endif class="custom-control-input">
                    <label class="custom-control-label" for="filterOptionsWithPoint"></label>
                </div>
            </div>

        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary btn-block mt-30">{{ trans('site.filter_items') }}</button>
</div>

@if(!empty($productCategories))
    @if(!empty($selectedCategory))
        <input type="hidden" name="category_id" value="{{ $selectedCategory->id }}">
    @endif

    <div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">

        <div class="">
            <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('categories.categories') }}</h3>

            <div class="pt-10">
                @foreach($productCategories as $productCategory)
                    @if(!empty($productCategory->subCategories) and count($productCategory->subCategories))

                        <span class="d-block font-14 font-weight-bold  mt-20">{{ $productCategory->title }}</span>

                        <div class="pl-10">
                            @foreach($productCategory->subCategories as $subCategory)
                                <a href="{{ $subCategory->getUrl() }}" class="d-flex align-items-center font-14 font-weight-normal mt-20 {{ (!empty($selectedCategory) and $selectedCategory->id == $subCategory->id) ? 'text-primary' : '' }}">
                                    @if(!empty($selectedCategory) and $selectedCategory->id == $subCategory->id)
                                        <i data-feather="chevron-right" width="20" height="20" class="mr-5"></i>
                                    @endif

                                    <span>{{ $subCategory->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ $productCategory->getUrl() }}" class="d-flex align-items-center font-14 font-weight-bold mt-20 {{ (!empty($selectedCategory) and $selectedCategory->id == $productCategory->id) ? 'text-primary' : '' }}">
                            @if(!empty($selectedCategory) and $selectedCategory->id == $productCategory->id)
                                <i data-feather="chevron-right" width="20" height="20" class="mr-5"></i>
                            @endif

                            <span>{{ $productCategory->title }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

@if(!empty($selectedCategory) and !empty($selectedCategory->filters) and count($selectedCategory->filters))
    <div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">
        @foreach($selectedCategory->filters as $filter)
            <div class="{{ ($loop->iteration > 1) ? 'border-gray300 border-top mt-25 pt-25' : '' }}">
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

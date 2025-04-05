@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

<div class="row">
    <div class="col-12 col-md-6 mt-15">

        <div class="form-group">
            <label class="input-label">{{ trans('public.price') }} ({{ $currency }})</label>
            <input type="number" name="price" value="{{ (!empty($product) and !empty($product->price)) ? convertPriceToUserCurrency($product->price) : old('price') }}" class="form-control @error('price')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
            @error('price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        @if($product->isPhysical())
            <div class="form-group">
                <label class="input-label">{{ trans('update.delivery_fee') }} ({{ currencySign() }})</label>
                <input type="number" name="delivery_fee" value="{{ (!empty($product) and !empty($product->delivery_fee)) ? convertPriceToUserCurrency($product->delivery_fee) : old('delivery_fee') }}" class="form-control @error('delivery_fee')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
                @error('delivery_fee')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('update.delivery_estimated_time') }} ({{ trans('public.day') }})</label>
                <input type="number" name="delivery_estimated_time" value="{{ !empty($product) ? $product->delivery_estimated_time : old('delivery_estimated_time') }}" class="form-control @error('delivery_estimated_time')  is-invalid @enderror" placeholder=""/>
                @error('delivery_estimated_time')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        @endif

        <div class="form-group js-inventory-inputs {{ (!empty($product) and $product->unlimited_inventory) ? 'd-none' : '' }}">
            <label class="input-label">{{ trans('update.inventory') }}</label>
            <input type="number" name="inventory" value="{{ (!empty($product) and $product->getAvailability() != 99999) ? $product->getAvailability() : old('inventory') }}" class="form-control @error('inventory')  is-invalid @enderror" placeholder=""/>
            @error('inventory')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group js-inventory-inputs {{ (!empty($product) and $product->unlimited_inventory) ? 'd-none' : '' }}">
            <label class="input-label">{{ trans('update.inventory_warning') }}</label>
            <input type="number" name="inventory_warning" value="{{ !empty($product) ? $product->inventory_warning : old('inventory_warning') }}" class="form-control @error('inventory_warning')  is-invalid @enderror" placeholder=""/>
            @error('inventory_warning')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-30 mb-10 d-flex align-items-center">
            <label class="cursor-pointer mb-0 input-label" for="unlimitedInventorySwitch">{{ trans('update.unlimited_inventory') }}</label>
            <div class="ml-30 custom-control custom-switch">
                <input type="checkbox" name="unlimited_inventory" class="custom-control-input" id="unlimitedInventorySwitch" {{ (!empty($product) and $product->unlimited_inventory) ? 'checked' :  '' }}>
                <label class="custom-control-label" for="unlimitedInventorySwitch"></label>
            </div>
        </div>
        <p class="text-gray font-12">{{ trans('update.create_product_unlimited_inventory_hint') }}</p>

    </div>
</div>

<div class="row">
    <div class="col-12 col-md-6 mt-30">

        <div class="form-group">
            <label class="input-label">{{ trans('public.category') }}</label>

            <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror" name="category_id" required>
                <option {{ (!empty($product) and !empty($product->category_id)) ? '' : 'selected' }} disabled>{{ trans('public.choose_category') }}</option>
                @foreach($productCategories as $productCategory)
                    @if(!empty($productCategory->subCategories) and $productCategory->subCategories->count() > 0)
                        <optgroup label="{{  $productCategory->title }}">
                            @foreach($productCategory->subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ ((!empty($product) and $product->category_id == $subCategory->id) or old('category_id') == $subCategory->id) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $productCategory->id }}" {{ ((!empty($product) and $product->category_id == $productCategory->id) or old('category_id') == $productCategory->id) ? 'selected' : '' }}>{{ $productCategory->title }}</option>
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

    <div class="col-12 mt-20">
        <div class="form-group {{ (!empty($productCategoryFilters) and count($productCategoryFilters)) ? '' : 'd-none' }}" id="categoriesFiltersContainer">
            <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
            <div id="categoriesFiltersCard" class="row">

                @if(!empty($productCategoryFilters) and count($productCategoryFilters))
                    @foreach($productCategoryFilters as $filter)
                        <div class="col-12 col-md-3 mt-20">
                            <div class="webinar-category-filters">
                                <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                                <div class="py-10"></div>

                                @php
                                    $productFilterOptions = $product->selectedFilterOptions->pluck('filter_option_id')->toArray();

                                    if (!empty(old('filters'))) {
                                        $productFilterOptions = array_merge($productFilterOptions, old('filters'));
                                    }
                                @endphp

                                @foreach($filter->options as $option)
                                    <div class="form-group mt-10 d-flex align-items-center justify-content-between">
                                        <label class="cursor-pointer font-14 text-gray" for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="filters[]" value="{{ $option->id }}" {{ ((!empty($productFilterOptions) && in_array($option->id, $productFilterOptions)) ? 'checked' : '') }} class="custom-control-input" id="filterOptions{{ $option->id }}">
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
    </div>
</div>


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('update.related_courses') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddRelatedCourses" data-bundle-id="{{ $product->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_related_courses') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="relatedCoursesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($product->relatedCourses) and count($product->relatedCourses))
                    <ul class="draggable-lists" data-order-table="relatedCourses">
                        @foreach($product->relatedCourses as $relatedCourseInfo)
                            @include('web.default.panel.store.products.create_includes.accordions.related_courses',['product' => $product,'relatedCourse' => $relatedCourseInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'comment.png',
                        'title' => trans('update.related_courses_no_result'),
                        'hint' => trans('update.related_courses_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<div id="newRelatedCourseForm" class="d-none">
    @include('web.default.panel.store.products.create_includes.accordions.related_courses',['product' => $product])
</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>

    <script src="/assets/default/js/panel/webinar.min.js"></script>
@endpush

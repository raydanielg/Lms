<li data-id="{{ !empty($selectedSpecification) ? $selectedSpecification->id :'' }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="specification_{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseSpecification{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" aria-controls="collapseSpecification{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" data-parent="#specificationsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="file" class=""></i>
            </span>

            <div class="font-weight-bold text-dark-blue d-block">{{ !empty($selectedSpecification) ? $selectedSpecification->specification->title : trans('update.add_new_specification') }}</div>
        </div>

        <div class="d-flex align-items-center">
            @if(!empty($selectedSpecification) and $selectedSpecification->status != 'active')
                <span class="disabled-content-badge mr-10">{{ trans('public.disabled') }}</span>
            @endif

            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($selectedSpecification))
                <a href="/panel/store/products/specifications/{{ $selectedSpecification->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseSpecification{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" aria-controls="collapseSpecification{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" data-parent="#specificationsAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseSpecification{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" aria-labelledby="specification_{{ !empty($selectedSpecification) ? $selectedSpecification->id :'record' }}" class=" collapse @if(empty($selectedSpecification)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-content-form specification-form" data-action="/panel/store/products/specifications/{{ !empty($selectedSpecification) ? $selectedSpecification->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][product_id]" value="{{ !empty($product) ? $product->id :'' }}">
                <input type="hidden" class="js-input-type" name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][input_type]" value="{{ !empty($selectedSpecification) ? $selectedSpecification->type :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">

                        @if(!empty(getGeneralSettings('content_translate')))
                            <div class="form-group">
                                <label class="input-label">{{ trans('auth.language') }}</label>
                                <select name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][locale]"
                                        class="form-control {{ !empty($selectedSpecification) ? 'js-product-content-locale' : '' }}"
                                        data-product-id="{{ !empty($product) ? $product->id : '' }}"
                                        data-id="{{ !empty($selectedSpecification) ? $selectedSpecification->id : '' }}"
                                        data-relation="selectedSpecifications"
                                        data-fields="value"
                                >
                                    @foreach(getUserLanguagesLists() as $lang => $language)
                                        <option value="{{ $lang }}" {{ (!empty($selectedSpecification) and !empty($selectedSpecification->value) and !empty($selectedSpecification->locale)) ? (mb_strtolower($selectedSpecification->locale) == mb_strtolower($lang) ? 'selected' : '') : ($locale == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                        @endif

                        <div class="form-group mt-15">
                            <label class="input-label d-block">{{ trans('update.specification') }}</label>

                            <select name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][specification_id]"
                                    class="js-ajax-specification_id form-control {{ !empty($selectedSpecification) ? '' : 'specification-select2' }}"
                                    data-placeholder="{{ trans('update.search_and_select_specifications') }}"
                                    data-allow-clear="false"
                                    data-category="{{ !empty($product) ? $product->category_id : '' }}"
                                {{ !empty($selectedSpecification) ? 'disabled' : '' }}
                            >

                                @if(!empty($productSpecifications))
                                    <option value="">{{ trans('update.search_and_select_specifications') }}</option>
                                    @foreach($productSpecifications as $productSpecification)
                                        <option value="{{ $productSpecification->id }}" {{ (!empty($selectedSpecification) and $selectedSpecification->product_specification_id == $productSpecification->id) ? 'selected' : '' }}>{{ $productSpecification->title }}</option>
                                    @endforeach
                                @elseif(!empty($selectedSpecification))
                                    <option value="{{ $selectedSpecification->specification->id }}" selected>{{ $selectedSpecification->specification->title }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>

                            @if(!empty($selectedSpecification))
                                <input type="hidden" name="ajax[{{ $selectedSpecification->id }}][specification_id]" value="{{ $selectedSpecification->specification->id }}">
                            @endif
                        </div>

                        <div class="form-group js-multi-values-input  {{ (!empty($selectedSpecification) and $selectedSpecification->type == 'multi_value') ? '' : 'd-none' }}">
                            <label class="input-label d-block">{{ trans('update.parameters') }}</label>

                            @php
                                $selectedMultiValues = [];

                                if (!empty($selectedSpecification)) {
                                    $selectedMultiValues = $selectedSpecification->selectedMultiValues->pluck('specification_multi_value_id')->toArray();
                                }
                            @endphp

                            <select name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][multi_values][]"
                                    class="js-ajax-multi_values form-control {{ !empty($selectedSpecification) ? 'select-multi-values-select2' : 'multi_values-select' }}"
                                    multiple
                                    data-placeholder="{{ trans('update.select_specification_params') }}"
                                    data-allow-clear="false"
                                    data-search="false"
                            >

                                @if(!empty($selectedSpecification->specification) and !empty($selectedSpecification->specification->multiValues))
                                    @foreach($selectedSpecification->specification->multiValues as $multiValue)
                                        <option value="{{ $multiValue->id }}" {{ in_array($multiValue->id,$selectedMultiValues) ? 'selected' : '' }}>{{ $multiValue->title }}</option>
                                    @endforeach
                                @endif
                            </select>

                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group js-summery-input {{ (!empty($selectedSpecification) and $selectedSpecification->type == 'textarea') ? '' : 'd-none' }}">
                            <label class="input-label d-block">{{ trans('update.product_summary') }}</label>
                            <textarea name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][summary]" rows="4" class="js-ajax-summary form-control ">{{ (!empty($selectedSpecification) and $selectedSpecification->type == 'textarea') ? $selectedSpecification->value : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mt-20 js-allow-selection-input {{ (!empty($selectedSpecification) and $selectedSpecification->type == 'multi_value') ? '' : 'd-none' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer input-label" for="specificationAllowSelectionSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}">{{ trans('update.allow_user_selection') }}</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][allow_selection]" class="custom-control-input" id="specificationAllowSelectionSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}" {{ (!empty($selectedSpecification) and $selectedSpecification->allow_selection) ? 'checked' : ''  }}>
                                    <label class="custom-control-label" for="specificationAllowSelectionSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}"></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-20">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer input-label" for="specificationStatusSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}">{{ trans('public.active') }}</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="ajax[{{ !empty($selectedSpecification) ? $selectedSpecification->id : 'new' }}][status]" class="custom-control-input" id="specificationStatusSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}" {{ (empty($selectedSpecification) or $selectedSpecification->status == \App\Models\ProductSelectedSpecification::$Active) ? 'checked' : ''  }}>
                                    <label class="custom-control-label" for="specificationStatusSwitch{{ !empty($selectedSpecification) ? $selectedSpecification->id : '_record' }}"></label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-specification btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($selectedSpecification))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>

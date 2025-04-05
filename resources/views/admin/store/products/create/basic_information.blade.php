<section>
    <h2 class="section-title after-line">{{ trans('public.basic_information') }}</h2>

    <div class="row">
        <div class="col-12 col-md-5">
            @if(!empty(getGeneralSettings('content_translate')))
                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="locale" class="form-control {{ !empty($product) ? 'js-edit-content-locale' : '' }}">
                        @foreach($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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

            <div class="form-group mt-15 ">
                <label class="input-label d-block">{{ trans('update.select_a_creator') }}</label>

                <select name="creator_id"
                        class="form-control search-user-select2 @error('creator_id')  is-invalid @enderror"
                        data-search-option="except_user"
                        data-allow-clear="false"
                        data-placeholder="{{ trans('public.search_user') }}">

                    @if(!empty($product))
                        <option value="{{ $product->creator->id }}" selected>{{ $product->creator->full_name }}</option>
                    @elseif(!empty(request()->get('in_house_product')))
                        <option value="{{ $authUser->id }}" selected>{{ $authUser->full_name }}</option>
                    @endif
                </select>

                @error('creator_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="form-group ">
                <label class="input-label d-block">{{ trans('public.type') }}</label>

                <select name="type" class="custom-select @error('type')  is-invalid @enderror">
                    <option value="physical" @if(!empty($product) and $product->isPhysical()) selected @endif>{{ trans('update.physical') }}</option>
                    <option value="virtual" @if(!empty($product) and $product->isVirtual()) selected @endif>{{ trans('update.virtual') }}</option>
                </select>

                @error('type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('public.title') }}</label>
                <input type="text" name="title" value="{{ (!empty($product) and !empty($product->translate($locale))) ? $product->translate($locale)->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
                @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('update.product_url') }}</label>
                <input type="text" name="slug" value="{{ !empty($product) ? $product->slug : old('slug') }}" class="form-control @error('slug')  is-invalid @enderror" placeholder=""/>
                <div class="text-muted text-small mt-1">{{ trans('update.product_url_hint') }}</div>
                @error('slug')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('update.required_points') }}</label>
                <input type="number" name="point" value="{{ !empty($product) ? $product->point : old('point') }}" class="form-control @error('point')  is-invalid @enderror"/>
                <div class="text-muted text-small mt-1">{{ trans('update.product_points_hint') }}</div>
                @error('point')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('admin/main.tax') }}</label>
                <input type="text" name="tax" value="{{ !empty($product) ? $product->tax : old('tax') }}" class="form-control @error('tax')  is-invalid @enderror" placeholder=""/>
                <div class="text-muted text-small mt-1">{{ trans('update.product_tax_hint') }}</div>
                @error('tax')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('admin/main.commission') }}</label>

                <div class="row">
                    <div class="col-6">
                        <label class="">{{ trans("admin/main.type") }}</label>
                        <select name="commission_type" class="js-commission-type-input form-control" data-currency="{{ $currency }}">
                            <option value="percent" {{ (!empty($product) and $product->commission_type == "percent") ? 'selected' : '' }}>{{ trans('update.percent') }}</option>
                            <option value="fixed_amount" {{ (!empty($product) and $product->commission_type == "fixed_amount") ? 'selected' : '' }}>{{ trans('update.fixed_amount') }}</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <div class="">
                            <label class="">
                                {{ trans("update.value") }}

                                <span class="ml-1 js-commission-value-span">({{ !empty($product) ? (($product->commission_type == "percent") ? '%' : $currency) : '%' }})</span>
                            </label>

                            <input type="number" name="commission" value="{{ !empty($product) ? $product->commission : old('commission') }}" class="js-commission-value-input form-control text-center" {{ (!empty($product) and $product->commission_type == "percent") ? 'maxlength="3" min="0" max="100"' : '' }}/>
                        </div>
                    </div>
                </div>

                <div class="text-muted text-small mt-1">{{ trans('update.product_commission_hint') }}</div>
                @error('commission')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('public.seo_description') }}</label>
                <input type="text" name="seo_description" value="{{ (!empty($product) and !empty($product->translate($locale))) ? $product->translate($locale)->seo_description : old('seo_description') }}" class="form-control @error('seo_description')  is-invalid @enderror " placeholder="{{ trans('forms.50_160_characters_preferred') }}"/>
                @error('seo_description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('public.summary') }}</label>
                <textarea name="summary" rows="6" class="form-control @error('summary')  is-invalid @enderror " placeholder="{{ trans('update.product_summary_placeholder') }}">{{ (!empty($product) and !empty($product->translate($locale))) ? $product->translate($locale)->summary : old('summary') }}</textarea>
                @error('summary')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="input-label">{{ trans('public.description') }}</label>
                <textarea id="summernote" name="description" class="form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('forms.webinar_description_placeholder') }}">{!! (!empty($product) and !empty($product->translate($locale))) ? $product->translate($locale)->description : old('description')  !!}</textarea>
                @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-6">

            <div class="form-group mb-1 d-flex align-items-center">
                <label class="cursor-pointer mb-0 input-label mr-2" for="orderingSwitch">{{ trans('update.enable_ordering') }}</label>
                <div class="custom-control custom-switch d-inline-block">
                    <input type="checkbox" name="ordering" class="custom-control-input" id="orderingSwitch" {{ (!empty($product) and $product->ordering) ? 'checked' :  '' }}>
                    <label class="custom-control-label" for="orderingSwitch"></label>
                </div>
            </div>

            <p class="text-gray font-12">{{ trans('update.create_product_enable_ordering_hint') }}</p>
        </div>
    </div>
</section>

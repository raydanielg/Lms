<div class="row">
    <div class="col-12 col-md-5">

        <div class="form-group mt-15 ">
            <label class="input-label d-block">{{ trans('update.target_types') }}</label>

            <select name="target_type" class="js-target-types-input custom-select @error('target_types')  is-invalid @enderror">
                @foreach(\App\Models\CashbackRule::$targetTypes as $type)
                    <option value="{{ $type }}" @if(!empty($rule) and $rule->target_type == $type) selected @endif>{{ trans('update.target_types_'.$type) }}</option>
                @endforeach
            </select>

            @error('target_types')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15 js-select-target-field {{ (empty($rule) or in_array($rule->target_type, ['all', 'recharge_wallet'])) ? 'd-none' : '' }}">
            <label class="input-label d-block">{{ trans('update.select_target') }}</label>

            @php
                $targets = [
                    'courses' => \App\Models\CashbackRule::$courseTargets,
                    'store_products' => \App\Models\CashbackRule::$productTargets,
                    'bundles' => \App\Models\CashbackRule::$bundleTargets,
                    'meetings' => \App\Models\CashbackRule::$meetingTargets,
                    'subscription_packages' => \App\Models\CashbackRule::$subscriptionTargets,
                    'registration_packages' => \App\Models\CashbackRule::$registrationPackagesTargets,
                ];
            @endphp

            <select name="target" class="js-target-input custom-select  @error('target')  is-invalid @enderror">
                <option value="">{{ trans('update.select_target') }}</option>

                @foreach($targets as $targetKey => $targetItems)
                    @foreach($targetItems as $target)
                        <option value="{{ $target }}" class="js-target-option js-target-option-{{ $targetKey }} {{ (!empty($rule) and $rule->target_type == $targetKey) ? '' : 'd-none' }}" @if(!empty($rule) and $rule->target == $target) selected @endif>{{ trans('update.'.$target) }}</option>
                    @endforeach
                @endforeach
            </select>

            @error('target')
            <div class="invalid-feedback d-none">
                {{ $message }}
            </div>
            @enderror
        </div>

        @php
            $selectedCategoryIds = !empty($rule) ? $rule->categories->pluck('id')->toArray() : [];
        @endphp

        <div class="form-group js-specific-categories-field {{ (!empty($rule) and $rule->target == "specific_categories") ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('update.specific_categories') }}</label>

            <select name="category_ids[]" id="categories" class="custom-select select2" multiple data-placeholder="{{ trans('public.choose_category') }}">

                @foreach($categories as $category)
                    @if(!empty($category->subCategories) and count($category->subCategories))
                        <optgroup label="{{  $category->title }}">
                            @foreach($category->subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ in_array($subCategory->id, $selectedCategoryIds) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                            @endforeach
                        </optgroup>
                    @else
                        <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds) ? 'selected' : '' }}>{{ $category->title }}</option>
                    @endif
                @endforeach
            </select>

            @error('category_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>


        <div class="form-group js-specific-instructors-field {{ (!empty($rule) and $rule->target == "specific_instructors") ? '' : 'd-none' }}">
            <label class="input-label">{{trans('update.specific_instructors')}}</label>

            <select name="instructor_ids[]" multiple="multiple" data-search-option="just_teacher_role" class="form-control search-user-select2"
                    data-placeholder="{{trans('public.search_instructors')}}">

                @if(!empty($rule) and count($rule->instructors))
                    @foreach($rule->instructors as $instructor)
                        <option value="{{ $instructor->id }}" selected>{{ $instructor->full_name }}</option>
                    @endforeach
                @endif
            </select>

            @error('instructor_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group js-specific-sellers-field {{ (!empty($rule) and $rule->target == "specific_sellers") ? '' : 'd-none' }}">
            <label class="input-label">{{trans('update.specific_sellers')}}</label>

            <select name="seller_ids[]" multiple="multiple" data-search-option="except_user" class="form-control search-user-select2"
                    data-placeholder="{{trans('public.search_instructors')}}">

                @if(!empty($rule) and count($rule->sellers))
                    @foreach($rule->sellers as $seller)
                        <option value="{{ $seller->id }}" selected>{{ $seller->full_name }}</option>
                    @endforeach
                @endif
            </select>

            @error('seller_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>


        <div class="form-group js-specific-courses-field {{ (!empty($rule) and $rule->target == "specific_courses") ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('update.specific_courses') }}</label>
            <select name="webinar_ids[]" multiple="multiple" class="form-control search-webinar-select2"
                    data-placeholder="Search classes">

                @if(!empty($rule) and count($rule->webinars))
                    @foreach($rule->webinars as $webinar)
                        <option value="{{ $webinar->id }}" selected>{{ $webinar->title }}</option>
                    @endforeach
                @endif
            </select>

            @error('webinar_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group js-specific-products-field {{ (!empty($rule) and $rule->target == "specific_products") ? '' : 'd-none' }}">
            <label class="input-label">{{trans('update.specific_products')}}</label>
            <select name="product_ids[]" multiple="multiple" class="form-control search-product-select2"
                    data-placeholder="{{trans('update.search_product')}}">

                @if(!empty($rule) and count($rule->products))
                    @foreach($rule->products as $product)
                        <option value="{{ $product->id }}" selected>{{ $product->title }}</option>
                    @endforeach
                @endif
            </select>

            @error('product_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group js-specific-bundles-field {{ (!empty($rule) and $rule->target == "specific_bundles") ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('update.specific_bundles') }}</label>
            <select name="bundle_ids[]" multiple="multiple" class="form-control search-bundle-select2 " data-placeholder="Search bundles">

                @if(!empty($rule) and count($rule->bundles))
                    @foreach($rule->bundles as $bundle)
                        <option value="{{ $bundle->id }}" selected>{{ $bundle->title }}</option>
                    @endforeach
                @endif
            </select>

            @error('bundle_ids')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

        @php
            $selectedSubscriptionPackagesIds = !empty($rule) ? $rule->subscribes->pluck('id')->toArray() : [];
        @endphp
        {{-- Subscription Specific Package --}}
        <div class="form-group js-subscription-specific-packages-field {{ (!empty($rule) and $rule->target_type == "subscription_packages" and $rule->target == "specific_packages") ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('update.specific_packages') }}</label>
            <select name="subscribe_ids[]" multiple="multiple" class="form-control select2" data-placeholder="{{ trans('update.select_packages') }}">

                @if(!empty($subscriptionPackages) and $subscriptionPackages->count() > 0)
                    @foreach($subscriptionPackages as $subscriptionPackage)
                        <option value="{{ $subscriptionPackage->id }}" {{ in_array($subscriptionPackage->id, $selectedSubscriptionPackagesIds) ? 'selected' : '' }}>{{ $subscriptionPackage->title }}</option>
                    @endforeach
                @endif
            </select>

            @error('subscription_packages')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>


        @php
            $selectedRegistrationPackagesIds = !empty($rule) ? $rule->registrationPackages->pluck('id')->toArray() : [];
        @endphp
        {{-- Registration Specific Package --}}
        <div class="form-group js-registration-specific-packages-field {{ (!empty($rule) and $rule->target_type == "registration_packages" and $rule->target == "specific_packages") ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('update.specific_packages') }}</label>
            <select name="registration_package_ids[]" multiple="multiple" class="form-control select2" data-placeholder="{{ trans('update.select_packages') }}">

                @if(!empty($registrationPackages) and $registrationPackages->count() > 0)
                    @foreach($registrationPackages as $registrationPackage)
                        <option value="{{ $registrationPackage->id }}" {{ in_array($registrationPackage->id, $selectedRegistrationPackagesIds) ? 'selected' : '' }}>{{ $registrationPackage->title }} ({{ $registrationPackage->role }})</option>
                    @endforeach
                @endif
            </select>

            @error('registration_packages')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

    </div>
</div>

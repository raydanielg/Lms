@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.discounts') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 col-lg-6">
                            <form action="{{ getAdminPanelUrl() }}/financial/discounts/{{ !empty($discount) ? $discount->id.'/update' : 'store' }}" method="Post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label>{{ trans('admin/main.title') }}</label>
                                    <input type="text" name="title"
                                           class="form-control  @error('title') is-invalid @enderror"
                                           value="{{ !empty($discount) ? $discount->title : old('title') }}"/>
                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('update.discount_type') }}</label>
                                    <select name="discount_type" class="js-discount-type form-control @error('discount_type') is-invalid @enderror">
                                        <option value="percentage"{{ (empty($discount) or (!empty($discount) and $discount->discount_type == 'percentage')) ? 'selected' : '' }}>{{ trans('admin/main.percentage') }}</option>
                                        <option value="fixed_amount"{{ (!empty($discount) and $discount->discount_type == 'fixed_amount') ? 'selected' : '' }}>{{ trans('update.fixed_amount') }}</option>
                                    </select>
                                    <div class="invalid-feedback">@error('discount_type') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('update.source') }}</label>
                                    <select name="source" class="js-discount-source form-control @error('source') is-invalid @enderror">
                                        @foreach(\App\Models\Discount::$discountSource as $source)
                                            <option value="{{ $source }}" {{ (!empty($discount) and $discount->source == $source) ? 'selected' : '' }}>{{ trans('update.discount_source_'.$source) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">@error('source') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group js-courses-input {{ (empty($discount) or $discount->source != \App\Models\Discount::$discountSourceCourse) ? 'd-none' : '' }}">
                                    <label class="input-label">{{ trans('admin/main.courses') }}</label>
                                    <select name="webinar_ids[]" multiple="multiple" class="form-control search-webinar-select2 " data-placeholder="{{ trans('admin/main.search_webinar') }}">

                                        @if(!empty($discount) and !empty($discount->discountCourses))
                                            @foreach($discount->discountCourses as $discountCourse)
                                                @if(!empty($discountCourse->course))
                                                    <option value="{{ $discountCourse->course->id }}" selected>{{ $discountCourse->course->title }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group js-bundles-input {{ (!empty($discount) and $discount->source == \App\Models\Discount::$discountSourceBundle) ? '' : 'd-none' }}">
                                    <label class="input-label">{{ trans('update.bundles') }}</label>
                                    <select name="bundle_ids[]" multiple="multiple" class="form-control search-bundle-select2 " data-placeholder="{{ trans('update.search_bundle') }}">

                                        @if(!empty($discount) and !empty($discount->discountBundles))
                                            @foreach($discount->discountBundles as $discountBundle)
                                                @if(!empty($discountBundle->bundle))
                                                    <option value="{{ $discountBundle->bundle->id }}" selected>{{ $discountBundle->bundle->title }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group js-categories-input {{ (empty($discount) or $discount->source != \App\Models\Discount::$discountSourceCategory) ? 'd-none' : '' }}">
                                    <label class="input-label">{{ trans('admin/main.categories') }}</label>
                                    <select name="category_ids[]" multiple="multiple" class="form-control search-category-select2 " data-placeholder="{{ trans('update.search_categories') }}">

                                        @if(!empty($discount) and !empty($discount->discountCategories))
                                            @foreach($discount->discountCategories as $discountCategory)
                                                @if(!empty($discountCategory->category))
                                                    <option value="{{ $discountCategory->category->id }}" selected>{{ $discountCategory->category->title }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group js-products-input {{ (empty($discount) or $discount->source != \App\Models\Discount::$discountSourceProduct) ? 'd-none' : '' }}">
                                    <label class="input-label d-block">{{ trans('update.product_type') }}</label>
                                    <select name="product_type" class="form-control">
                                        <option value="all">{{ trans('admin/main.all') }}</option>
                                        <option value="physical" {{ (!empty($discount) and $discount->product_type == 'physical') ? 'selected' : '' }}>{{ trans('update.physical') }}</option>
                                        <option value="virtual" {{ (!empty($discount) and $discount->product_type == 'virtual') ? 'selected' : '' }}>{{ trans('update.virtual') }}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('admin/main.users') }}</label>
                                    <select name="user_id" class="form-control search-user-select2"
                                            data-placeholder="{{ trans('update.discount_users_placeholder') }}">

                                        @if(!empty($userDiscounts) && $userDiscounts->count() > 0)
                                            @foreach($userDiscounts as $userDiscount)
                                                <option value="{{ $userDiscount->user_id }}" selected>{{ $userDiscount->user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('admin/main.groups') }}</label>
                                    <select name="group_ids[]" class="form-control select2 @error('group_ids') is-invalid @enderror" multiple data-placeholder="{{ trans('update.discount_user_group_placeholder') }}">
                                        @if(!empty($userGroups))
                                            @foreach($userGroups as $userGroup)
                                                <option value="{{ $userGroup->id }}" @if(!empty($discountGroupIds) and in_array($userGroup->id, $discountGroupIds)) selected @endif>{{ $userGroup->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">@error('group_ids') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group js-percentage-inputs {{ (!empty($discount) and $discount->discount_type == 'fixed_amount') ? 'd-none' : '' }}">
                                    <label>{{ trans('admin/main.discount_percentage') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-percentage" href=""></i>
                                            </div>
                                        </div>

                                        <input type="number" name="percent"
                                               class="form-control text-center  @error('percent') is-invalid @enderror"
                                               value="{{ !empty($discount) ? $discount->percent : old('percent') }}"
                                               placeholder="0"/>
                                        @error('percent')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group js-percentage-inputs {{ (!empty($discount) and $discount->discount_type == 'fixed_amount') ? 'd-none' : '' }}">
                                    <label>{{ trans('admin/main.max_amount') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ $currency }}
                                            </div>
                                        </div>

                                        <input type="number" name="max_amount"
                                               class="form-control text-center @error('max_amount') is-invalid @enderror"
                                               value="{{ (!empty($discount) and !empty($discount->max_amount)) ? convertPriceToUserCurrency($discount->max_amount) : old('max_amount') }}"
                                               placeholder="{{ trans('update.discount_max_amount_placeholder') }}"/>
                                        @error('max_amount')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group js-fixed-amount-inputs {{ (empty($discount) or $discount->discount_type == 'percentage') ? 'd-none' : '' }}">
                                    <label>{{ trans('admin/main.amount') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ $currency }}
                                            </div>
                                        </div>

                                        <input type="number" name="amount"
                                               class="form-control text-center @error('amount') is-invalid @enderror"
                                               value="{{ (!empty($discount) and !empty($discount->amount)) ? convertPriceToUserCurrency($discount->amount) : old('amount') }}"
                                               placeholder="{{ trans('update.discount_amount_placeholder') }}"/>
                                        @error('amount')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('update.minimum_order') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ $currency }}
                                            </div>
                                        </div>

                                        <input type="number" name="minimum_order"
                                               class="form-control text-center @error('minimum_order') is-invalid @enderror"
                                               value="{{ (!empty($discount) and !empty($discount->minimum_order)) ? convertPriceToUserCurrency($discount->minimum_order) : old('minimum_order') }}"
                                               placeholder="{{ trans('update.discount_minimum_order_placeholder') }}"/>
                                        @error('minimum_order')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.usable_times') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-users" href=""></i>
                                            </div>
                                        </div>

                                        <input type="number" name="count"
                                               class="form-control text-center @error('count') is-invalid @enderror"
                                               value="{{ !empty($discount) ? $discount->count : old('count') }}"
                                               placeholder="{{ trans('admin/main.count_placeholder') }}"/>
                                        @error('count')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="inputDefault">{{ trans('admin/main.discount_code') }}</label>
                                    <input type="text" name="code"
                                           value="{{ !empty($discount) ? $discount->code : old('code') }}"
                                           class="form-control text-center @error('code') is-invalid @enderror">
                                    @error('code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <div class="text-muted text-small mt-1">{{ trans('admin/main.discount_code_hint') }}</div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.expiration') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="expired_at" class="form-control datetimepicker @error('expired_at') is-invalid @enderror"
                                               aria-describedby="dateRangeLabel" autocomplete="off"
                                               value="{{ !empty($discount) ? dateTimeFormat($discount->expired_at, 'Y-m-d H:i', false) : '' }}"/>

                                        @error('expired_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="form-group custom-switches-stacked">
                                    <label class="custom-switch pl-0">
                                        <input type="hidden" name="for_first_purchase" value="0">
                                        <input type="checkbox" name="for_first_purchase" id="forFirstPurchaseSwitch" value="1" {{ (!empty($discount) and $discount->for_first_purchase) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                        <span class="custom-switch-indicator"></span>
                                        <label class="custom-switch-description mb-0 cursor-pointer" for="forFirstPurchaseSwitch">{{ trans('update.apply_only_for_the_first_purchase') }}</label>
                                    </label>
                                    <div class="text-muted text-small mt-1">{{ trans('update.apply_only_for_the_first_purchase_hint') }}</div>
                                </div>

                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/discount.min.js"></script>
@endpush

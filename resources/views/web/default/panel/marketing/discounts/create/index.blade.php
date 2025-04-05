@extends('web.default.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <div class="">
        <form action="/panel/marketing/discounts/{{ !empty($discount) ? $discount->id.'/update' : 'store' }}" method="post" class="">
            {{ csrf_field() }}

            <section>
                <h2 class="section-title after-line">{{ !empty($discount) ? (trans('public.edit').' ('. $discount->title .')') : trans('update.new_coupon') }}</h2>

                <div class="row  mt-25">
                    <div class="col-12 col-md-5">

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="title" class="js-ajax-title form-control @error('title') is-invalid @enderror" value="{{ !empty($discount) ? $discount->title : old('title') }}"/>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="font-12 text-gray mt-5">{{ trans('update.this_title_will_be_displayed_on_the_product_or_profile_page') }}</p>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.subtitle') }}</label>
                            <input type="text" name="subtitle" class="js-ajax-subtitle form-control @error('subtitle') is-invalid @enderror" value="{{ !empty($discount) ? $discount->subtitle : old('subtitle') }}"/>
                            @error('subtitle')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="font-12 text-gray mt-5">{{ trans('update.this_subtitle_will_be_displayed_on_the_product_or_profile_page') }}</p>
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
                                @foreach(\App\Models\Discount::$panelDiscountSource as $source)
                                    <option value="{{ $source }}" {{ (!empty($discount) and $discount->source == $source) ? 'selected' : '' }}>{{ trans('update.discount_source_'.$source) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">@error('source') {{ $message }} @enderror</div>
                        </div>

                        @php
                            $selectedCourseIds = (!empty($discount) and !empty($discount->discountCourses)) ? $discount->discountCourses->pluck('course_id')->toArray() : [];
                            $selectedBundleIds = (!empty($discount) and !empty($discount->discountBundles)) ? $discount->discountBundles->pluck('bundle_id')->toArray() : [];
                        @endphp

                        <div class="form-group js-courses-input {{ (empty($discount) or $discount->source != \App\Models\Discount::$discountSourceCourse) ? 'd-none' : '' }}">
                            <label class="input-label">{{ trans('admin/main.courses') }}</label>
                            <select name="webinar_ids[]" multiple="multiple" class="form-control select2 " data-placeholder="{{ trans('update.select_a_course') }}">
                                @foreach($webinars as $webinar)
                                    <option value="{{ $webinar->id }}" {{ in_array($webinar->id, $selectedCourseIds) ? 'selected' : '' }}>{{ $webinar->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group js-bundles-input {{ (!empty($discount) and $discount->source == \App\Models\Discount::$discountSourceBundle) ? '' : 'd-none' }}">
                            <label class="input-label">{{ trans('update.bundles') }}</label>
                            <select name="bundle_ids[]" multiple="multiple" class="form-control select2 " data-placeholder="{{ trans('update.select_a_bundle') }}">
                                @foreach($bundles as $bundle)
                                    <option value="{{ $bundle->id }}" {{ in_array($bundle->id, $selectedCourseIds) ? 'selected' : '' }}>{{ $bundle->title }}</option>
                                @endforeach
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

                        <div class="form-group js-percentage-inputs {{ (!empty($discount) and $discount->discount_type == 'fixed_amount') ? 'd-none' : '' }}">
                            <label class="input-label">{{ trans('admin/main.discount_percentage') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="text-white font-16">%</span>
                                    </div>
                                </div>

                                <input type="number" name="percent"
                                       class="form-control text-center  @error('percent') is-invalid @enderror"
                                       value="{{ !empty($discount) ? $discount->percent : old('percent') }}"
                                       placeholder="0" min="0"/>
                                @error('percent')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group js-percentage-inputs {{ (!empty($discount) and $discount->discount_type == 'fixed_amount') ? 'd-none' : '' }}">
                            <label class="input-label">{{ trans('admin/main.max_amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="text-white">{{ $currency }}</span>
                                    </div>
                                </div>

                                <input type="number" name="max_amount"
                                       class="form-control text-center @error('max_amount') is-invalid @enderror"
                                       value="{{ !empty($discount) ? $discount->max_amount : old('max_amount') }}"
                                       placeholder="{{ trans('update.discount_max_amount_placeholder') }}" min="0"/>
                                @error('max_amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group js-fixed-amount-inputs {{ (empty($discount) or $discount->discount_type == 'percentage') ? 'd-none' : '' }}">
                            <label class="input-label">{{ trans('admin/main.amount') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="text-white">{{ $currency }}</span>
                                    </div>
                                </div>

                                <input type="number" name="amount"
                                       class="form-control text-center @error('amount') is-invalid @enderror"
                                       value="{{ !empty($discount) ? $discount->amount : old('amount') }}"
                                       placeholder="{{ trans('update.discount_amount_placeholder') }}" min="0"/>
                                @error('amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.minimum_order') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <span class="text-white">{{ $currency }}</span>
                                    </div>
                                </div>

                                <input type="number" name="minimum_order"
                                       class="form-control text-center @error('minimum_order') is-invalid @enderror"
                                       value="{{ !empty($discount) ? $discount->minimum_order : old('minimum_order') }}"
                                       placeholder="{{ trans('update.discount_minimum_order_placeholder') }}" min="0"/>
                                @error('minimum_order')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.usable_times') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i data-feather="users" width="18" height="18" class="text-white"></i>
                                    </span>
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

                            <p class="font-12 text-gray mt-5">{{ trans('update.the_coupon_will_be_expired_after_reaching_this_parameter_leave_it_blank_for_unlimited') }}</p>
                        </div>

                        <div class="form-group">
                            <label class="input-label" for="inputDefault">{{ trans('update.coupon_code') }}</label>
                            <input type="text" name="code"
                                   value="{{ !empty($discount) ? $discount->code : old('code') }}"
                                   class="form-control text-center @error('code') is-invalid @enderror">
                            @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.expiry_date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i data-feather="calendar" width="18" height="18" class="text-white"></i>
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

                        <div class="form-group mt-2 d-flex align-items-center justify-content-between ">
                            <div class="">
                                <label class="input-label cursor-pointer" for="private">{{ trans('update.private_coupon') }}</label>
                                <p class="font-12 text-gray font-weight-500"> {{ trans('update.the_coupon_will_be_hidden_in_frontend') }}</p>
                            </div>

                            <div class="custom-control custom-switch">
                                <input id="private" type="checkbox" name="private" class="custom-control-input" {{ (!empty($discount) and $discount->private) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="private"></label>
                            </div>
                        </div>


                        <div class=" mt-25">
                            <button class="btn btn-primary">{{ trans('admin/main.save') }}</button>
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/admin/discount.min.js"></script>
@endpush

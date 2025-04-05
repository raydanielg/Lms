@extends('web.default.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')

    <section>
        <h2 class="section-title">{{ trans('update.coupons_overview') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/upcoming.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalCoupons }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.total_coupons') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $activeCoupons }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.active_coupons') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $couponPurchases }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.coupon_purchases') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/49.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $purchaseAmount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.purchase_amount') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="mt-25">
        <h2 class="section-title">{{ trans('update.filter_coupons') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="/panel/marketing/discounts" method="get" class="row">
                <div class="col-12 col-lg-5">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.from') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="from" autocomplete="off" value="{{ request()->get('from') }}" class="form-control {{ !empty(request()->get('from')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.to') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="to" autocomplete="off" value="{{ request()->get('to') }}" class="form-control {{ !empty(request()->get('to')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label class="input-label">{{ trans('update.source') }}</label>
                        <select name="source" class="form-control">
                            <option value="">{{ trans('public.all') }}</option>

                            @foreach(\App\Models\Discount::$panelDiscountSource as $source)
                                <option value="{{ $source }}" {{ (request()->get('source') == $source) ? 'selected' : '' }}>{{ trans('update.discount_source_'.$source) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-12 col-lg-2">
                    <div class="form-group">
                        <label class="input-label">{{ trans('admin/main.status') }}</label>
                        <select name="status" class="form-control">
                            <option value="">{{ trans('public.all') }}</option>

                            <option value="active" {{ (request()->get('status') == "active") ? 'selected' : '' }}>{{ trans('public.active') }}</option>
                            <option value="expired" {{ (request()->get('status') == "expired") ? 'selected' : '' }}>{{ trans('panel.expired') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
                </div>
            </form>
        </div>
    </section>

    @if(!empty($discounts) and $discounts->count() > 0)

        <section class="mt-35">
            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h2 class="section-title">{{ trans('update.coupons_list') }}</h2>

                <form action="" method="get" class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="cursor-pointer mb-0 mr-10 text-gray font-14 font-weight-500" for="activeDiscountsSwitch">{{ trans('update.show_only_active_coupons') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="status" class="js-panel-list-switch-filter custom-control-input" {{ (request()->get('status') == 'active') ? 'checked' : '' }} value="active" id="activeDiscountsSwitch">
                        <label class="custom-control-label" for="activeDiscountsSwitch"></label>
                    </div>
                </form>
            </div>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center">
                                <thead>
                                <tr>
                                    <th class="text-left text-gray">{{ trans('public.title') }}</th>
                                    <th class="text-left text-gray">{{ trans('update.source') }}</th>
                                    <th class="text-center text-gray">{{ trans('panel.amount') }}</th>
                                    <th class="text-center text-gray">{{ trans('admin/main.usable_times') }}</th>
                                    <th class="text-center text-gray">{{ trans('update.min_amount') }}</th>
                                    <th class="text-center text-gray">{{ trans('update.max_amount') }}</th>
                                    <th class="text-center text-gray">{{ trans('admin/main.sales') }}</th>
                                    <th class="text-center text-gray">{{ trans('admin/main.created_date') }}</th>
                                    <th class="text-center text-gray">{{ trans('update.expiry_date') }}</th>
                                    <th class="text-center text-gray">{{ trans('public.status') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($discounts as $discount)
                                    <tr>
                                        <td class="text-left align-middle text-dark-blue font-weight-500">
                                            {{ $discount->title }}
                                        </td>

                                        <td class="align-middle">
                                            {{ trans("update.discount_source_{$discount->source}") }}
                                        </td>

                                        <td class="align-middle">
                                            {{  $discount->amount ?  handlePrice($discount->amount) : '-' }}
                                        </td>

                                        <td class="align-middle">
                                            <div class="font-16 font-weight-500 text-secondary">{{ $discount->count }}</div>
                                            <div class="text-12 text-gray mt-5">{{ trans('admin/main.remain') }} : {{ $discount->discountRemain() }}</div>
                                        </td>

                                        <td class="align-middle">
                                            {{  $discount->minimum_order ?  handlePrice($discount->minimum_order) : '-' }}
                                        </td>

                                        <td class="align-middle">
                                            {{  $discount->max_amount ?  handlePrice($discount->max_amount) : '-' }}
                                        </td>

                                        @php
                                            $salesStats = $discount->salesStats();
                                        @endphp

                                        <td class="align-middle">
                                            <div class="font-16 font-weight-500 text-secondary">{{ $salesStats['count'] }}</div>

                                            @if(!empty($salesStats['amount']))
                                                <div class="text-12 text-gray mt-5">{{ handlePrice($salesStats['amount']) }}</div>
                                            @endif
                                        </td>

                                        <td class="align-middle">
                                            {{ dateTimeFormat($discount->created_at, 'Y M d') }}
                                        </td>

                                        <td class="align-middle">
                                            {{ dateTimeFormat($discount->expired_at, 'Y M d - H:i') }}
                                        </td>

                                        <td class="align-middle">
                                            @if($discount->expired_at < time())
                                                <span class="text-danger">{{ trans('panel.expired') }}</span>
                                            @else
                                                <span class="text-primary">{{ trans('admin/main.active') }}</span>
                                            @endif
                                        </td>

                                        <td class="text-right align-middle">
                                            @if($discount->status != \App\Models\SpecialOffer::$inactive)
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i data-feather="more-vertical" height="20"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="/panel/marketing/discounts/{{ $discount->id }}/edit" type="button"
                                                           class="btn-transparent text-gray d-block">{{ trans('public.edit') }}</a>

                                                        @can('panel_marketing_delete_coupon')
                                                            <a href="/panel/marketing/discounts/{{ $discount->id }}/delete" type="button"
                                                               class="delete-action btn-transparent text-danger mt-5 d-block">{{ trans('public.delete') }}</a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <div class="my-30">
            {{ $discounts->appends(request()->input())->links('vendor.pagination.panel') }}
        </div>

    @else

        @include(getTemplate() . '.includes.no-result',[
            'file_name' => 'offer.png',
            'title' => trans('panel.discount_no_result'),
            'hint' =>  nl2br(trans('panel.discount_no_result_hint')) ,
        ])

    @endif

@endsection


@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

@endpush

@extends(getTemplate().'.layouts.app')


@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-white font-weight-bold">{{ trans('cart.shopping_cart') }}</h1>
        <span class="payment-hint font-20 text-white d-block"> {{ handlePrice($subTotal, true, true, false, null, true) . ' ' . trans('cart.for_items',['count' => $carts->count()]) }}</span>
    </section>

    <div class="container">

        @if(!empty($totalCashbackAmount))
            <div class="d-flex align-items-center mt-45 p-15 success-transparent-alert">
                <div class="success-transparent-alert__icon d-flex align-items-center justify-content-center">
                    <i data-feather="credit-card" width="18" height="18" class=""></i>
                </div>

                <div class="ml-10">
                    <div class="font-14 font-weight-bold ">{{ trans('update.get_cashback') }}</div>
                    <div class="font-12 ">{{ trans('update.by_purchasing_this_cart_you_will_get_amount_as_cashback',['amount' => handlePrice($totalCashbackAmount)]) }}</div>
                </div>
            </div>
        @endif

        @if(!empty($cartDiscount))
            @include('web.default.cart.includes.cart_discount', ['cartDiscountClassName' => 'is-cart-page'])
        @endif

        <section class="mt-45">
            <h2 class="section-title">{{ trans('cart.cart_items') }}</h2>

            <div class="rounded-sm shadow mt-20 py-25 px-10 px-md-30">
                @if($carts->count() > 0)
                    <div class="row d-none d-md-flex">
                        <div class="col-12 col-lg-8"><span
                                class="text-gray font-weight-500">{{ trans('cart.item') }}</span></div>
                        <div class="col-6 col-lg-2 text-center"><span
                                class="text-gray font-weight-500">{{ trans('public.price') }}</span></div>
                        <div class="col-6 col-lg-2 text-center"><span
                                class="text-gray font-weight-500">{{ trans('public.remove') }}</span></div>
                    </div>
                @endif
                @foreach($carts as $cart)
                    <div class="row mt-5 cart-row">
                        <div class="col-12 col-lg-8 mb-15 mb-md-0">
                            <div class="webinar-card webinar-list-cart row">
                                <div class="col-4">
                                    <div class="image-box">
                                        @php
                                            $cartItemInfo = $cart->getItemInfo();
                                            $cartTaxType = !empty($cartItemInfo['isProduct']) ? 'store' : 'general';
                                        @endphp
                                        <img src="{{ $cartItemInfo['imgPath'] }}" class="img-cover" alt="user avatar">
                                    </div>
                                </div>

                                <div class="col-8">
                                    <div class="webinar-card-body p-0 w-100 h-100 d-flex flex-column">
                                        <div class="d-flex flex-column">
                                            <a href="{{ $cartItemInfo['itemUrl'] ?? '#!' }}" target="_blank">
                                                <h3 class="font-16 font-weight-bold text-dark-blue">{{ $cartItemInfo['title'] }}</h3>
                                            </a>

                                            @if(!empty($cart->gift_id) and !empty($cart->gift))
                                                <span class="d-block mt-5 text-gray font-12">{!! trans('update.a_gift_for_name_on_date',['name' => $cart->gift->name, 'date' => (!empty($cart->gift->date) ? dateTimeFormat($cart->gift->date, 'j M Y H:i') : trans('update.instantly'))]) !!}</span>
                                            @endif
                                        </div>

                                        @if(!empty($cart->reserve_meeting_id))
                                            <div class="mt-10">
                                                <span class="text-gray font-12 border rounded-pill py-5 px-10">{{ $cart->reserveMeeting->day .' '. $cart->reserveMeeting->meetingTime->time }} ({{ $cart->reserveMeeting->meeting->getTimezone() }})</span>
                                            </div>

                                            @if($cart->reserveMeeting->meeting->getTimezone() != getTimezone())
                                                <div class="mt-10">
                                                    <span class="text-danger font-12 border border-danger rounded-pill py-5 px-10">{{ $cart->reserveMeeting->day .' '. dateTimeFormat($cart->reserveMeeting->start_at,'h:iA',false).'-'.dateTimeFormat($cart->reserveMeeting->end_at,'h:iA',false) }} ({{ getTimezone() }})</span>
                                                </div>
                                            @endif
                                        @endif

                                        @if(!empty($cartItemInfo['profileUrl']) and !empty($cartItemInfo['teacherName']))
                                            <span class="text-gray font-14 mt-auto">
                                                {{ trans('public.by') }}
                                                <a href="{{ $cartItemInfo['profileUrl'] }}" target="_blank" class="text-gray text-decoration-underline">{{ $cartItemInfo['teacherName'] }}</a>
                                            </span>
                                        @endif

                                        @if(!empty($cartItemInfo['extraHint']))
                                            <span class="text-gray font-14 mt-auto">{{ $cartItemInfo['extraHint'] }}</span>
                                        @endif

                                        @if(!is_null($cartItemInfo['rate']))
                                            @include('web.default.includes.webinar.rate',['rate' => $cartItemInfo['rate']])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-2 d-flex flex-md-column align-items-center justify-content-center">
                            <span class="text-gray d-inline-block d-md-none">{{ trans('public.price') }} :</span>

                            @if(!empty($cartItemInfo['discountPrice']))
                                <span class="text-gray text-decoration-line-through mx-10 mx-md-0">{{ handlePrice($cartItemInfo['price'], true, true, false, null, true, $cartTaxType) }}</span>
                                <span class="font-20 text-primary mt-0 mt-md-5 font-weight-bold">{{ handlePrice($cartItemInfo['discountPrice'], true, true, false, null, true, $cartTaxType) }}</span>
                            @else
                                <span class="font-20 text-primary mt-0 mt-md-5 font-weight-bold">{{ handlePrice($cartItemInfo['price'], true, true, false, null, true, $cartTaxType) }}</span>
                            @endif

                            @if(!empty($cartItemInfo['quantity']))
                                <span class="font-12 text-warning font-weight-500 mt-0 mt-md-5">({{ $cartItemInfo['quantity'] }} {{ trans('update.product') }})</span>
                            @endif

                            @if(!empty($cartItemInfo['extraPriceHint']))
                                <span class="font-12 text-gray font-weight-500 mt-0 mt-md-5">{{ $cartItemInfo['extraPriceHint'] }}</span>
                            @endif
                        </div>

                        <div class="col-6 col-lg-2 d-flex flex-md-column align-items-center justify-content-center">
                            <span class="text-gray d-inline-block d-md-none mr-10 mr-md-0">{{ trans('public.remove') }} :</span>

                            <a href="/cart/{{ $cart->id }}/delete" class="delete-action btn-cart-list-delete d-flex align-items-center justify-content-center">
                                <i data-feather="x" width="20" height="20" class=""></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                <button type="button" onclick="window.history.back()" class="btn btn-sm btn-primary mt-25">{{ trans('cart.continue_shopping') }}</button>
            </div>
        </section>

        <form action="/cart/checkout" method="post" id="cartForm">
            {{ csrf_field() }}
            <input type="hidden" name="discount_id" value="">

            @if($hasPhysicalProduct)
                @include('web.default.cart.includes.shipping_and_delivery')
            @endif

            <div class="row mt-30">
                <div class="col-12 col-lg-6">
                    <section class="mt-45">
                        <h3 class="section-title">{{ trans('cart.coupon_code') }}</h3>
                        <div class="rounded-sm shadow mt-20 py-25 px-20">
                            <p class="text-gray font-14">{{ trans('cart.coupon_code_hint') }}</p>

                            @if(!empty($userGroup) and !empty($userGroup->discount))
                                <p class="text-gray mt-25">{{ trans('cart.in_user_group',['group_name' => $userGroup->name , 'percent' => $userGroup->discount]) }}</p>
                            @endif

                            <form action="/carts/coupon/validate" method="Post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <input type="text" name="coupon" id="coupon_input" class="form-control mt-25"
                                           placeholder="{{ trans('cart.enter_your_code_here') }}">
                                    <span class="invalid-feedback">{{ trans('cart.coupon_invalid') }}</span>
                                    <span class="valid-feedback">{{ trans('cart.coupon_valid') }}</span>
                                </div>

                                <button type="submit" id="checkCoupon"
                                        class="btn btn-sm btn-primary mt-50">{{ trans('cart.validate') }}</button>
                            </form>
                        </div>
                    </section>
                </div>

                <div class="col-12 col-lg-6">
                    <section class="mt-45">
                        <h3 class="section-title">{{ trans('cart.cart_totals') }}</h3>
                        <div class="rounded-sm shadow mt-20 pb-20 px-20">

                            <div class="cart-checkout-item">
                                <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.sub_total') }}</h4>
                                <span class="font-14 text-gray font-weight-bold">{{ handlePrice($subTotal) }}</span>
                            </div>

                            <div class="cart-checkout-item">
                                <h4 class="text-secondary font-14 font-weight-500">{{ trans('public.discount') }}</h4>
                                <span class="font-14 text-gray font-weight-bold">
                                <span id="totalDiscount">{{ handlePrice($totalDiscount) }}</span>
                            </span>
                            </div>

                            <div class="cart-checkout-item">
                                <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.tax') }}
                                    @if(!$taxIsDifferent)
                                        <span class="font-14 text-gray ">({{ $tax }}%)</span>
                                    @endif
                                </h4>
                                <span class="font-14 text-gray font-weight-bold"><span id="taxPrice">{{ handlePrice($taxPrice) }}</span></span>
                            </div>

                            @if(!empty($productDeliveryFee))
                                <div class="cart-checkout-item">
                                    <h4 class="text-secondary font-14 font-weight-500">
                                        {{ trans('update.delivery_fee') }}
                                    </h4>
                                    <span class="font-14 text-gray font-weight-bold"><span id="taxPrice">{{ handlePrice($productDeliveryFee) }}</span></span>
                                </div>
                            @endif

                            <div class="cart-checkout-item border-0">
                                <h4 class="text-secondary font-14 font-weight-500">{{ trans('cart.total') }}</h4>
                                <span class="font-14 text-gray font-weight-bold"><span id="totalAmount">{{ handlePrice($total) }}</span></span>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary mt-15">{{ trans('cart.checkout') }}</button>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts_bottom')
    <script>
        var couponInvalidLng = '{{ trans('cart.coupon_invalid') }}';
        var selectProvinceLang = '{{ trans('update.select_province') }}';
        var selectCityLang = '{{ trans('update.select_city') }}';
        var selectDistrictLang = '{{ trans('update.select_district') }}';
    </script>

    <script src="/assets/default/js/parts/get-regions.min.js"></script>
    <script src="/assets/default/js/parts/cart.min.js"></script>
@endpush

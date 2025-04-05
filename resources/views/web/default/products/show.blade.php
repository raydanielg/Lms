@extends('web.default.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/css/css-stars.css">
@endpush

@section('content')

    {{-- Cashback Alert --}}
    @if(!empty($cashbackRules) and count($cashbackRules))
        <div class="container position-relative mt-30">
            @include('web.default.includes.cashback_alert',['itemPrice' => $product->price])
        </div>
    @endif

    <div class="container product-show-special-offer position-relative mt-30">
        @if(!empty($activeSpecialOffer))
            @include('web.default.course.special_offer')
        @endif
    </div>

    <div class="container {{ !empty($activeSpecialOffer) ? 'mt-50' : 'mt-30' }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="lazyImage product-show-image-card position-relative">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->title }}" class="main-s-image img-cover rounded-lg" loading="lazy">

                    @if(!empty($product->video_demo))
                        <button id="productDemoVideoBtn"
                                data-video-path="{{ url($product->video_demo) }}"
                                class="product-video-demo-icon cursor-pointer btn-transparent d-flex align-items-center justify-content-center">
                            <img src="/assets/default/img/icons/play-bold.svg" alt="play icon" class=""/>
                        </button>
                    @endif
                </div>


                <div class="product-show-thumbnail-card d-flex align-items-center mt-20">
                    <div class="thumbnail-card is-first-thumbnail-card cursor-pointer position-relative">
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->title }}" class="img-cover rounded-sm">

                        @if(!empty($product->video_demo))
                            <span class="product-video-demo-thumb-icon d-flex align-items-center justify-content-center">
                                <img src="/assets/default/img/icons/play-bold.svg" alt="play icon" class=""/>
                            </span>
                        @endif
                    </div>

                    @if(!empty($product->images) and count($product->images))
                        @foreach($product->images as $image)
                            <div class="thumbnail-card cursor-pointer ml-20 ml-lg-35">
                                <img src="{{ $image->path }}" alt="{{ $product->title }}" class="img-cover rounded-sm">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6 mt-20 mt-lg-0">
                <form action="/cart/store" method="post" id="productAddToCartForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="item_id" value="{{ $product->id }}">
                    <input type="hidden" name="item_name" value="product_id">

                    <div class="product-show-info-card bg-info p-15 p-md-25 rounded-lg">
                        <h1 class="font-30">
                            {{ clean($product->title, 't') }}
                        </h1>

                        <span class="d-block font-16 mt-10">{{ trans('public.in') }} <a href="{{ $product->category->getUrl() }}" target="_blank" class="font-weight-500 text-decoration-underline">{{ $product->category->title }}</a></span>

                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between mt-20">
                            <div class="d-flex align-items-center">
                                @include('web.default.includes.webinar.rate',['rate' => $product->getRate(),'className' => 'mt-0'])
                                <span class="ml-10 font-14">({{ $product->reviews->pluck('creator_id')->count() }} {{ trans('product.reviews') }})</span>
                            </div>

                            <div class="d-flex align-items-center mt-15 mt-md-0">
                                <span class="mr-5">{{ trans('update.availability') }}</span>
                                @if(($product->getAvailability() > 0))
                                    @if(!empty($product->inventory) and !empty($product->inventory_warning) and $product->inventory_warning > $product->getAvailability())
                                        <span class="product-availability-badge badge-warning">{{ trans('update.only_n_left',['count' => $product->getAvailability()]) }}</span>
                                    @else
                                        <span class="product-availability-badge badge-primary">{{ trans('update.in_stock') }}</span>
                                    @endif
                                @else
                                    <span class="product-availability-badge badge-danger">{{ trans('update.out_of_stock') }}</span>
                                @endif
                            </div>
                        </div>

                        @if(!empty($selectableSpecifications) and count($selectableSpecifications))
                            @foreach($selectableSpecifications as $selectableSpecification)
                                <div class="product-show-selectable-specification mt-10">
                                    <span class="font-14 font-weight-bold text-dark">{{ $selectableSpecification->specification->title }}</span>

                                    <div class="d-flex align-items-center flex-wrap">
                                        @foreach($selectableSpecification->selectedMultiValues as $specificationValue)
                                            @if(!empty($specificationValue->multiValue))
                                                <div class="selectable-specification-item mr-5 mt-5">
                                                    <input type="radio" name="specifications[{{ $selectableSpecification->specification->createName() }}]" value="{{ $specificationValue->multiValue->createName() }}" id="{{ $specificationValue->multiValue->createName() }}" class="" {{ ($loop->iteration == 1) ? 'checked' : '' }}>
                                                    <label class="font-12 cursor-pointer px-10 py-5" for="{{ $specificationValue->multiValue->createName() }}">{{ $specificationValue->multiValue->title }}</label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="product-show-price-box mt-15">
                            @if(!empty($product->price) and $product->price > 0)
                                @if($product->getPriceWithActiveDiscountPrice() < $product->price)
                                    <span class="real">{{ handlePrice($product->getPriceWithActiveDiscountPrice(), true, true, false, null, true, 'store') }}</span>
                                    <span class="off ml-10">{{ handlePrice($product->price, true, true, false, null, true, 'store') }}</span>
                                @else
                                    <span class="real">{{ handlePrice($product->price, true, true, false, null, true, 'store') }}</span>
                                @endif
                            @else
                                <span class="real">{{ trans('public.free') }}</span>
                            @endif

                            @if($product->isPhysical())
                                @if(!empty($product->delivery_fee) and $product->delivery_fee > 0)
                                    <span class="shipping-price d-block mt-5">+ {{ handlePrice($product->delivery_fee) }} {{ trans('update.shipping') }}</span>
                                @else
                                    <span class="text-warning d-block font-14 font-weight-500 mt-5">{{ trans('update.free_shipping') }}</span>
                                @endif
                            @endif
                        </div>

                        <div class="product-show-cart-actions d-flex align-items-center flex-wrap ">
                            <div class="cart-quantity d-flex align-items-center mt-20 mr-15">
                                <input type="hidden" id="productAvailabilityCount" value="{{ $product->getAvailability() }}">
                                <button type="button" class="minus d-flex align-items-center justify-content-center" {{ ($product->getAvailability() < 1) ? 'disabled' : '' }}>
                                    <i data-feather="minus" class="" width="20" height="20"></i>
                                </button>

                                <input type="number" name="quantity" value="1" {{ ($product->getAvailability() < 1) ? 'disabled' : '' }}>

                                <button type="button" class="plus d-flex align-items-center justify-content-center" {{ ($product->getAvailability() < 1) ? 'disabled' : '' }}>
                                    <i data-feather="plus" class="" width="20" height="20"></i>
                                </button>
                            </div>

                            @php
                                $productAvailability = $product->getAvailability();
                            @endphp

                            <div class="d-flex flex-column flex-md-row flex-md-wrap align-items-md-center w-100">
                                <button type="submit" class="btn mt-20 {{ ($productAvailability > 0) ? 'btn-primary' : 'btn-dark' }}" {{ ($productAvailability < 1) ? 'disabled' : '' }}>
                                    <i data-feather="shopping-cart" class="mr-5" width="20" height="20"></i>
                                    {{ ($productAvailability > 0) ? trans('public.add_to_cart') : trans('update.out_of_stock') }}
                                </button>

                                @if($productAvailability > 0 and !empty($product->point) and $product->point > 0)
                                    <input type="hidden" class="js-product-points" value="{{ $product->point }}">

                                    <a href="{{ !(auth()->check()) ? '/login' : '#!' }}" class="{{ (auth()->check()) ? 'js-buy-with-point' : '' }} js-buy-with-point-show-btn btn btn-outline-warning mt-20 ml-0 ml-md-10" rel="nofollow">
                                        {!! trans('update.buy_with_n_points',['points' => $product->point]) !!}
                                    </a>
                                @endif

                                @if($productAvailability > 0 and !empty(getFeaturesSettings('direct_products_payment_button_status')))
                                    <button type="button" class="btn btn-outline-danger mt-20 ml-0 ml-md-10 js-product-direct-payment">
                                        {{ trans('update.buy_now') }}
                                    </button>
                                @endif

                                @if($productAvailability > 0 and $hasInstallments)
                                    <a href="/products/{{ $product->slug }}/installments" class="js-installments-btn btn btn-outline-primary mt-20 ml-0 ml-md-10">
                                        {{ trans('update.installments') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row align-items-md-center w-100 mt-35">
                            @if($product->isPhysical() and !empty($product->delivery_estimated_time))
                                <div class="product-show-info-footer-items d-flex align-items-center mb-10 mb-md-0 mr-0 mr-md-10">
                                    <div class="icon-box">
                                        <i data-feather="package" class="" width="20" height="20"></i>
                                    </div>
                                    <div class="ml-5">
                                        <span class="d-block font-14 font-weight-bold text-dark">{{ trans('update.physical_product') }}</span>
                                        <span class="d-block font-12 text-gray">{{ trans('update.delivery_estimated_time_days_alert',['days' => $product->delivery_estimated_time]) }}</span>
                                    </div>
                                </div>
                            @elseif($product->isVirtual())
                                <div class="product-show-info-footer-items d-flex align-items-center mb-10 mb-md-0 mr-0 mr-md-10">
                                    <div class="icon-box">
                                        <i data-feather="package" class="" width="20" height="20"></i>
                                    </div>
                                    <div class="ml-5">
                                        <span class="d-block font-14 font-weight-bold text-dark">{{ trans('update.virtual_product') }}</span>
                                        <span class="d-block font-12 text-gray">{{ trans('update.download_all_files_after_payment') }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="js-share-product product-show-info-footer-items d-flex align-items-center cursor-pointer">
                                <div class="icon-box">
                                    <i data-feather="share-2" class="" width="20" height="20"></i>
                                </div>
                                <div class="ml-5">
                                    <span class="d-block font-14 font-weight-bold text-dark">{{ trans('public.share') }}</span>
                                    <span class="d-block font-12 text-gray">{{ trans('update.product_share_text') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Gift Card --}}
                        @if($product->isVirtual() and $productAvailability > 0 and !empty(getGiftsGeneralSettings('status')) and !empty(getGiftsGeneralSettings('allow_sending_gift_for_products')))
                            <a href="/gift/product/{{ $product->slug }}" class="d-flex align-items-center mt-15 rounded-lg border p-15">
                                <div class="size-40 d-flex-center rounded-circle bg-gray200">
                                    <i data-feather="gift" class="text-gray" width="20" height="20"></i>
                                </div>
                                <div class="ml-5">
                                    <h4 class="font-14 font-weight-bold text-gray">{{ trans('update.gift_this_product') }}</h4>
                                    <p class="font-12 text-gray">{{ trans('update.gift_this_product_hint') }}</p>
                                </div>
                            </a>
                        @endif

                    </div>
                </form>
            </div>
        </div>

        @if(
               !empty(getFeaturesSettings("frontend_coupons_display_type")) and
               getFeaturesSettings("frontend_coupons_display_type") == "before_content" and
               !empty($instructorDiscounts) and
               count($instructorDiscounts)
           )
            @foreach($instructorDiscounts as $instructorDiscount)
                @include('web.default.includes.discounts.instructor_discounts_card', ['discount' => $instructorDiscount, 'instructorDiscountClassName' => "mt-30"])
            @endforeach
        @endif

        <div class="mt-30">
            <ul class="product-show__nav-tabs nav nav-tabs p-15 d-flex align-items-center" id="tabs-tab" role="tablist">
                <li class="nav-item mr-20 mr-lg-30">
                    <a class="position-relative font-14 {{ (empty(request()->get('tab')) or request()->get('tab') == 'description') ? 'active' : '' }}" id="description-tab"
                       data-toggle="tab" href="#description" role="tab" aria-controls="description"
                       aria-selected="true">{{ trans('public.description') }}</a>
                </li>
                <li class="nav-item mr-20 mr-lg-30">
                    <a class="position-relative font-14 {{ (request()->get('tab') == 'seller') ? 'active' : '' }}" id="seller-tab" data-toggle="tab"
                       href="#seller" role="tab" aria-controls="seller"
                       aria-selected="false">{{ trans('update.seller') }}</a>
                </li>
                <li class="nav-item mr-20 mr-lg-30">
                    <a class="position-relative font-14 {{ (request()->get('tab') == 'specifications') ? 'active' : '' }}" id="specifications-tab" data-toggle="tab"
                       href="#specifications" role="tab" aria-controls="specifications"
                       aria-selected="false">{{ trans('update.specifications') }}</a>
                </li>

                @if(!empty($product->files) and count($product->files) and $product->checkUserHasBought())
                    <li class="nav-item mr-20 mr-lg-30">
                        <a class="position-relative font-14 {{ (request()->get('tab') == 'files') ? 'active' : '' }}" id="files-tab" data-toggle="tab"
                           href="#files" role="tab" aria-controls="files"
                           aria-selected="false">{{ trans('public.files') }}</a>
                    </li>
                @endif

                <li class="nav-item mr-20 mr-lg-30">
                    <a class="position-relative font-14 {{ (request()->get('tab') == 'reviews') ? 'active' : '' }}" id="reviews-tab" data-toggle="tab"
                       href="#reviews" role="tab" aria-controls="reviews"
                       aria-selected="false">{{ trans('product.reviews') }}</a>
                </li>
            </ul>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade {{ (empty(request()->get('tab')) or request()->get('tab') == 'description') ? 'show active' : '' }} " id="description" role="tabpanel"
                     aria-labelledby="description-tab">
                    @include('web.default.products.includes.tabs.description')
                </div>

                <div class="tab-pane fade {{ (request()->get('tab') == 'seller') ? 'show active' : '' }} " id="seller" role="tabpanel"
                     aria-labelledby="seller-tab">
                    @include('web.default.products.includes.tabs.seller')
                </div>

                <div class="tab-pane fade {{ (request()->get('tab') == 'specifications') ? 'show active' : '' }} " id="specifications" role="tabpanel"
                     aria-labelledby="specifications-tab">
                    @include('web.default.products.includes.tabs.specifications')
                </div>

                <div class="tab-pane fade {{ (request()->get('tab') == 'files') ? 'show active' : '' }} " id="files" role="tabpanel"
                     aria-labelledby="files-tab">
                    @include('web.default.products.includes.tabs.files')
                </div>

                <div class="tab-pane fade {{ (request()->get('tab') == 'reviews') ? 'show active' : '' }} " id="reviews" role="tabpanel"
                     aria-labelledby="reviews-tab">
                    @include('web.default.products.includes.tabs.reviews')
                </div>
            </div>

        </div>

        @if(
               !empty(getFeaturesSettings("frontend_coupons_display_type")) and
               getFeaturesSettings("frontend_coupons_display_type") == "after_content" and
               !empty($instructorDiscounts) and
               count($instructorDiscounts)
           )
            @foreach($instructorDiscounts as $instructorDiscount)
                @include('web.default.includes.discounts.instructor_discounts_card', ['discount' => $instructorDiscount, 'instructorDiscountClassName' => "mt-30"])
            @endforeach
        @endif

        {{-- Ads Bannaer --}}
        @if(!empty($advertisingBanners) and count($advertisingBanners))
            <div class="mt-30 mt-md-50">
                <div class="row">
                    @foreach($advertisingBanners as $banner)
                        <div class="col-{{ $banner->size }}">
                            <a href="{{ $banner->link }}">
                                <img src="{{ $banner->image }}" class="img-cover rounded-sm" alt="{{ $banner->title }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        {{-- ./ Ads Bannaer --}}

    </div>

    @include('web.default.products.includes.share_modal')
    @include('web.default.user.send_message_modal',['user' => $seller])
    @include('web.default.products.includes.buy_with_point_modal')
@endsection

@push('scripts_bottom')
    <script>
        var replyLang = '{{ trans('panel.reply') }}';
        var closeLang = '{{ trans('public.close') }}';
        var saveLang = '{{ trans('public.save') }}';
        var reportLang = '{{ trans('panel.report') }}';
        var reportSuccessLang = '{{ trans('panel.report_success') }}';
        var reportFailLang = '{{ trans('panel.report_fail') }}';
        var messageToReviewerLang = '{{ trans('public.message_to_reviewer') }}';
        var unFollowLang = '{{ trans('panel.unfollow') }}';
        var followLang = '{{ trans('panel.follow') }}';
        var messageSuccessSentLang = '{{ trans('site.message_success_sent') }}';
        var productDemoLang = '{{ trans('update.product_demo') }}';
        var onlineViewerModalTitleLang = '{{ trans('update.online_viewer') }}';
        var copyLang = '{{ trans('public.copy') }}';
        var copiedLang = '{{ trans('public.copied') }}';
    </script>

    <script src="/assets/default/js/parts/time-counter-down.min.js"></script>
    <script src="/assets/default/vendors/barrating/jquery.barrating.min.js"></script>
    <script src="/assets/default/js/parts/comment.min.js"></script>
    <script src="/assets/default/js/parts/profile.min.js"></script>
    <script src="/assets/default/js/parts/video_player_helpers.min.js"></script>
    <script src="/assets/default/js/parts/product_show.min.js"></script>
@endpush

<div class="product-card">
    <figure>
        <div class="image-box">
            <a href="{{ $product->getUrl() }}" class="image-box__a">
                @php
                    $hasDiscount = $product->getActiveDiscount();
                @endphp

                <div class="badges-lists">
                    @if($product->getAvailability() < 1)
                        <span class="out-of-stock-badge">
                            <span>{{ trans('update.out_of_stock') }}</span>
                        </span>
                    @elseif($hasDiscount)
                        <span class="badge badge-danger">{{ trans('public.offer',['off' => $hasDiscount->percent]) }}</span>
                    @elseif($product->isPhysical() and empty($product->delivery_fee))
                        <span class="badge badge-warning">{{ trans('update.free_shipping') }}</span>
                    @endif

                        @include('web.default.includes.product_custom_badge', ['itemTarget' => $product])
                </div>

                <img src="{{ $product->thumbnail }}" class="img-cover" alt="{{ $product->title }}">
            </a>

            @if($product->getAvailability() > 0)
                <div class="hover-card-action">
                    <button type="button" data-id="{{ $product->id }}" class="btn-add-product-to-cart d-flex align-items-center justify-content-center border-0 cursor-pointer">
                        <i data-feather="shopping-cart" width="20" height="20" class=""></i>
                    </button>
                </div>
            @endif
        </div>

        <figcaption class="product-card-body">
            <div class="user-inline-avatar d-flex align-items-center">
                <div class="avatar bg-gray200">
                    <img src="{{ $product->creator->getAvatar() }}" class="img-cover" alt="{{ $product->creator->full_name }}">
                </div>
                <a href="{{ $product->creator->getProfileUrl() }}" target="_blank" class="user-name ml-5 font-14">{{ $product->creator->full_name }}</a>
            </div>

            <a href="{{ $product->getUrl() }}">
                <h3 class="mt-15 product-title font-weight-bold font-16 text-dark-blue">{{ clean($product->title,'title') }}</h3>
            </a>

            @if(!empty($product->category))
                <span class="d-block font-14 mt-10">{{ trans('public.in') }} <a href="/products?category_id={{ $product->category->id }}" target="_blank" class="text-decoration-underline">{{ $product->category->title }}</a></span>
            @endif

            @include('web.default.includes.webinar.rate',['rate' => $product->getRate()])


            <div class="product-price-box mt-25">
                @if(!empty($isRewardProducts) and !empty($product->point))
                    <span class="text-warning real font-14">{{ $product->point }} {{ trans('update.points') }}</span>
                @elseif($product->price > 0)
                    @if($product->getPriceWithActiveDiscountPrice() < $product->price)
                        <span class="real">{{ handlePrice($product->getPriceWithActiveDiscountPrice(), true, true, false, null, true, 'store') }}</span>
                        <span class="off ml-10">{{ handlePrice($product->price, true, true, false, null, true, 'store') }}</span>
                    @else
                        <span class="real">{{ handlePrice($product->price, true, true, false, null, true, 'store') }}</span>
                    @endif
                @else
                    <span class="real">{{ trans('public.free') }}</span>
                @endif
            </div>
        </figcaption>
    </figure>
</div>

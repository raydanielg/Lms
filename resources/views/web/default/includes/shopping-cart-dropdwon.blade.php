<div class="dropdown">

    @if((empty($userCarts) or count($userCarts) < 1) and !empty($userCartDiscount))
        <a href="/cart" class="btn btn-transparent">
            <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>
        </a>
    @else
        <button type="button" {{ (empty($userCarts) or count($userCarts) < 1) ? 'disabled' : '' }} class="btn btn-transparent dropdown-toggle" id="navbarShopingCart" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>

            @if(!empty($userCarts) and count($userCarts))
                <span class="badge badge-circle-primary d-flex align-items-center justify-content-center">{{ count($userCarts) }}</span>
            @endif
        </button>
    @endif

    <div class="dropdown-menu" aria-labelledby="navbarShopingCart">
        <div class="d-md-none border-bottom mb-20 pb-10 text-right">
            <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
        </div>
        <div class="h-100">
            <div class="navbar-shopping-cart h-100" data-simplebar>
                @if(!empty($userCarts) and count($userCarts) > 0)
                    <div class="mb-auto">
                        @foreach($userCarts as $cart)
                            @php
                                $cartItemInfo = $cart->getItemInfo();
                                $cartTaxType = !empty($cartItemInfo['isProduct']) ? 'store' : 'general';
                            @endphp

                            @if(!empty($cartItemInfo))
                                <div class="navbar-cart-box d-flex align-items-center">

                                    <a href="{{ $cartItemInfo['itemUrl'] }}" target="_blank" class="navbar-cart-img">
                                        <img src="{{ $cartItemInfo['imgPath'] }}" alt="product title" class="img-cover"/>
                                    </a>

                                    <div class="navbar-cart-info">
                                        <a href="{{ $cartItemInfo['itemUrl'] }}" target="_blank">
                                            <h4>{{ $cartItemInfo['title'] }}</h4>
                                        </a>
                                        <div class="price mt-10">
                                            @if(!empty($cartItemInfo['discountPrice']))
                                                <span class="text-primary font-weight-bold">{{ handlePrice($cartItemInfo['discountPrice'], true, true, false, null, true, $cartTaxType) }}</span>
                                                <span class="off ml-15">{{ handlePrice($cartItemInfo['price'], true, true, false, null, true, $cartTaxType) }}</span>
                                            @else
                                                <span class="text-primary font-weight-bold">{{ handlePrice($cartItemInfo['price'], true, true, false, null, true, $cartTaxType) }}</span>
                                            @endif

                                            @if(!empty($cartItemInfo['quantity']))
                                                <span class="font-12 text-warning font-weight-500 ml-10">({{ $cartItemInfo['quantity'] }} {{ trans('update.product') }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="navbar-cart-actions">
                        <div class="navbar-cart-total mt-15 border-top d-flex align-items-center justify-content-between">
                            <strong class="total-text">{{ trans('cart.total') }}</strong>
                            <strong class="text-primary font-weight-bold">{{ !empty($totalCartsPrice) ? handlePrice($totalCartsPrice, true, true, false, null, true, $cartTaxType) : 0 }}</strong>
                        </div>

                        <a href="/cart/" class="btn btn-sm btn-primary btn-block mt-50 mt-md-15">{{ trans('cart.go_to_cart') }}</a>
                    </div>
                @else
                    <div class="d-flex align-items-center text-center py-50">
                        <i data-feather="shopping-cart" width="20" height="20" class="mr-10"></i>
                        <span class="">{{ trans('cart.your_cart_empty') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

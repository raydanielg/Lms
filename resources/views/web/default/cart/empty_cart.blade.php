@extends('web.default.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="d-flex-center flex-column empty-cart-container">
                    <div class="empty-cart-icon">
                        <img src="/assets/default/img/cart/empty.svg" alt="{{ trans('update.cart_is_empty') }}" class="img-fluid">
                    </div>

                    <h1 class="mt-30 font-20 font-weight-bold text-secondary">{{ trans('update.cart_is_empty') }}</h1>
                    <p class="mt-5 font-14 text-gray">{{ trans('update.explore_the_platform_and_add_some_items_to_the_cart') }}</p>


                    @if(!empty($cartDiscount))
                        @include('web.default.cart.includes.cart_discount')
                    @endif

                </div>

            </div>
        </div>
    </div>
@endsection

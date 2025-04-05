<div class="product-show-reviews-tab mt-20">
    <div class="course-reviews-box row align-items-center">
        <div class="col-3 col-lg-3 text-center">
            <div class="reviews-rate font-36 font-weight-bold text-primary">{{ $product->getRate() }}</div>

            <div class="text-center">
                @include(getTemplate() . '.includes.webinar.rate',[
                    'rate' => round($product->getRate(),1),
                    'dontShowRate' => true,
                    'className' => 'justify-content-center mt-0'
                ])
                <div class="mt-15">{{ $product->reviews->pluck('creator_id')->count() }}  {{ trans('product.reviews') }}</div>
            </div>
        </div>

        <div class="col-9 col-lg-6">
            <div class="d-flex align-items-center">
                <div class="progress course-progress rounded-sm">
                    <span class="progress-bar rounded-sm" style="width: {{ $product->reviews->avg('product_quality') / 5 * 100 }}%"></span>
                </div>
                <span class="ml-15 font-14 text-gray text-left">{{ trans('update.products') }} ({{ $product->reviews->count() > 0 ? round($product->reviews->avg('product_quality'), 1) : 0 }})</span>
            </div>

            <div class="mt-25 d-flex align-items-center">
                <div class="progress course-progress rounded-sm">
                    <span class="progress-bar rounded-sm" style="width: {{ $product->reviews->avg('purchase_worth') / 5 * 100 }}%"></span>
                </div>
                <span class="ml-15 font-14 text-gray text-left">{{ trans('product.purchase_worth') }} ({{ $product->reviews->count() > 0 ? round($product->reviews->avg('purchase_worth'), 1) : 0 }})</span>
            </div>

            <div class="mt-25 d-flex align-items-center">
                <div class="progress course-progress rounded-sm">
                    <span class="progress-bar rounded-sm" style="width: {{ $product->reviews->avg('delivery_quality') / 5 * 100 }}%"></span>
                </div>
                <span class="ml-15 font-14 text-gray text-left">{{ trans('update.delivery') }} ({{ $product->reviews->count() > 0 ? round($product->reviews->avg('delivery_quality'), 1) : 0 }})</span>
            </div>

            <div class="mt-25 d-flex align-items-center">
                <div class="progress course-progress rounded-sm">
                    <span class="progress-bar rounded-sm" style="width: {{ $product->reviews->avg('seller_quality') / 5 * 100 }}%"></span>
                </div>
                <span class="ml-15 font-14 text-gray text-left">{{ trans('update.seller') }} ({{ $product->reviews->count() > 0 ? round($product->reviews->avg('seller_quality'), 1) : 0 }})</span>
            </div>

        </div>
    </div>

    <section class="mt-40">
        <h2 class="section-title after-line">{{ trans('product.reviews') }} ({{ $product->reviews->pluck('creator_id')->count() }})</h2>

        <form action="/products/reviews/store" class="mt-20" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="product_id" value="{{ $product->id }}"/>

            <div class="form-group">
                <textarea name="description" class="form-control" rows="10"></textarea>
            </div>

            <div class="reviews-stars row align-items-center">

                <div class="col-6 col-md-3 d-flex flex-column align-items-center justify-content-center barrating-stars">
                    <span class="font-14 text-gray">{{ trans('update.products') }}</span>
                    <select name="product_quality" data-rate="1">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex flex-column align-items-center justify-content-center barrating-stars">
                    <span class="font-14 text-gray">{{ trans('product.purchase_worth') }}</span>
                    <select name="purchase_worth" data-rate="1">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex flex-column align-items-center justify-content-center barrating-stars">
                    <span class="font-14 text-gray">{{ trans('update.delivery') }}</span>
                    <select name="delivery_quality" data-rate="1">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex flex-column align-items-center justify-content-center barrating-stars">
                    <span class="font-14 text-gray">{{ trans('update.seller') }}</span>
                    <select name="seller_quality" data-rate="1">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-sm btn-primary mt-20">{{ trans('product.post_review') }}</button>
        </form>

        <div class="mt-45">
            @if($product->reviews->count() > 0)
                @foreach($product->reviews as $review)

                    <div class="comments-card shadow-lg rounded-sm border px-20 py-15 mt-30" data-address="/products/reviews/store-reply-comment" data-csrf="{{ csrf_token() }}" data-id="{{ $review->id }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="user-inline-avatar d-flex align-items-center mt-10">
                                <div class="avatar bg-gray200">
                                    <img src="{{ $review->creator->getAvatar() }}" class="img-cover" alt="">
                                </div>
                                <div class="d-flex flex-column ml-5">
                                    <span class="font-weight-500 text-secondary">{{ $review->creator->full_name }}</span>

                                    @include(getTemplate() . '.includes.webinar.rate',[
                                            'rate' => $review->rates,
                                            'dontShowRate' => true,
                                            'className' => 'justify-content-start mt-0'
                                        ])
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <span class="font-12 text-gray mr-10">{{ dateTimeFormat($review->created_at, 'j M Y | H:i') }}</span>

                                <div class="btn-group dropdown table-actions">
                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical" height="20"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="/products/reviews/store-reply-comment" class="webinar-actions d-block text-hover-primary reply-comment">{{ trans('panel.reply') }}</a>

                                        @if(!empty($authUser) and $authUser->id == $review->creator_id)
                                            <a href="/products/reviews/{{ $review->id }}/delete" class="delete-action d-block mt-10 text-hover-primary">{{ trans('public.delete') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-20 text-gray font-14">
                            {!! clean($review->description,'description') !!}
                        </div>

                        @if($review->comments->count() > 0)
                            @foreach($review->comments as $comment)
                                <div class="shadow-lg rounded-sm border px-20 py-15 mt-30">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="user-inline-avatar d-flex align-items-center mt-10">
                                            <div class="avatar bg-gray200">
                                                <img src="{{ $comment->user->getAvatar() }}" class="img-cover" alt="{{ $comment->user->full_name }}">
                                            </div>
                                            <div class="d-flex flex-column ml-5">
                                                <span class="font-weight-500 text-secondary">{{ $comment->user->full_name }}</span>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <span class="font-12 text-gray mr-10">{{ dateTimeFormat($comment->created_at, 'j M Y | H:i') }}</span>

                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="" class="webinar-actions d-block text-hover-primary reply-comment">{{ trans('panel.reply') }}</a>

                                                    @if(!empty($authUser) and $authUser->id == $comment->user_id)
                                                        <a href="/comments/{{ $comment->id }}/delete" class="webinar-actions d-block mt-10 text-hover-primary">{{ trans('public.delete') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-20 text-gray">
                                        {!! clean($comment->comment,'comment') !!}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </section>
</div>

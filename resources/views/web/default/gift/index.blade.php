@extends('web.default.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <div class="container mt-50">
        <div class="text-center">
            <h1 class="font-36 font-weight-bold text-dark">{{ $pageTitle }}</h1>
            <p class="font-16 text-gray mt-10">{{ $titleHint }}</p>
        </div>

        <div class="mt-50 rounded-lg border border-gray300">
            <div class="row">
                <div class="col-12 col-md-6 px-30 px-lg-80 py-30 py-lg-50 border-right">
                    <h3 class="font-24 font-weight-bold mb-25">{{ trans('update.recipient_information') }}</h3>

                    <form action="/gift/{{ $itemType }}/{{ $item->slug }}" method="post">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="input-label">{{ trans('auth.name') }}:</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('auth.email') }}:</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.gift_date') }}:</label>
                            <input name="date" type="text" class="form-control datetimepicker @error('date') is-invalid @enderror" autocomplete="off">
                            @error('date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.message_to_recipient_(optional)') }}:</label>
                            <textarea name="description" rows="5" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('update.proceed_to_checkout') }}</button>
                    </form>
                </div>

                <div class="col-12 col-md-6 d-flex-center px-30 px-lg-80 py-30 py-lg-50">
                    <div class="gift-item-card d-flex">

                        @if($itemType == 'course')
                            @include('web.default.gift.course_card',['webinar' => $item])
                        @elseif($itemType == 'bundle')
                            @include('web.default.gift.bundle_card',['bundle' => $item])
                        @elseif($itemType == 'product')
                            @include('web.default.gift.product_card',['product' => $item])
                        @endif

                        <div class="gift-item-card-icon">
                            <img src="/assets/default/img/gift/gift.svg" alt="gift">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/js/parts/gifts.min.js"></script>

@endpush

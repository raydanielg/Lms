@php
    $advertisingModalSettings = getAdvertisingModalSettings();
@endphp

@if(!empty($advertisingModalSettings))
    <div class="d-none" id="advertisingModalSettings">
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="section-title font-16 text-dark-blue mb-10">{{ $advertisingModalSettings['title'] ?? '' }}</h3>

            <button type="button" class="btn-close-advertising-modal close-swl btn-transparent d-flex">
                <i data-feather="x" width="25" height="25" class=""></i>
            </button>
        </div>

        <div class="d-flex align-items-center justify-content-center">
            <img src="{{ $advertisingModalSettings['image'] ?? '' }}" class="img-fluid rounded-lg" alt="{{ $advertisingModalSettings['title'] ?? 'ads' }}">
        </div>

        <p class="font-14 text-gray mt-20">{!! $advertisingModalSettings['description'] ?? '' !!}</p>

        <div class="row align-items-center mt-20">
            @if(!empty($advertisingModalSettings['button1']) and !empty($advertisingModalSettings['button1']['link']) and !empty($advertisingModalSettings['button1']['title']))
                <div class="col-6">
                    <a href="{{ $advertisingModalSettings['button1']['link'] }}" class="btn btn-primary btn-sm btn-block">{{ $advertisingModalSettings['button1']['title'] }}</a>
                </div>
            @endif

            @if(!empty($advertisingModalSettings['button2']) and !empty($advertisingModalSettings['button2']['link']) and !empty($advertisingModalSettings['button2']['title']))
                <div class="col-6">
                    <a href="{{ $advertisingModalSettings['button2']['link'] }}" class="btn btn-outline-primary btn-sm btn-block">{{ $advertisingModalSettings['button2']['title'] }}</a>
                </div>
            @endif
        </div>
    </div>
@endif

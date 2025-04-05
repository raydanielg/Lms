{{-- course FAQ --}}
@if(!empty($bundle->bundleWebinars) and $bundle->bundleWebinars->count() > 0)
    <div class="mt-20">
        <h2 class="section-title after-line">{{ trans('product.courses') }}</h2>

        @foreach($bundle->bundleWebinars as $bundleWebinar)
            @if(!empty($bundleWebinar->webinar))
                @include('web.default.includes.webinar.list-card',['webinar' => $bundleWebinar->webinar])
            @endif
        @endforeach
    </div>
@endif

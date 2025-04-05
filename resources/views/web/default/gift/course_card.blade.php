<div class="gift-webinar-card bg-white">
    <figure>
        <div class="image-box">
            <a href="{{ $webinar->getUrl() }}">
                <img src="{{ $webinar->getImage() }}" class="img-cover" alt="{{ $webinar->title }}">
            </a>
        </div>

        <figcaption class="mt-10">
            <div class="user-inline-avatar d-flex align-items-center">
                <div class="avatar bg-gray200">
                    <img src="{{ $webinar->teacher->getAvatar() }}" class="img-cover" alt="{{ $webinar->teacher->full_name }}">
                </div>
                <a href="{{ $webinar->teacher->getProfileUrl() }}" target="_blank" class="user-name ml-5 font-14">{{ $webinar->teacher->full_name }}</a>
            </div>

            <a href="{{ $webinar->getUrl() }}">
                <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">{{ clean($webinar->title,'title') }}</h3>
            </a>

            @if($webinar->getRate())
                @include('web.default.includes.webinar.rate',['rate' => $webinar->getRate()])
            @endif

            <div class="webinar-price-box mt-15">
                @if(!empty($webinar->price) and $webinar->price > 0)
                    @if($webinar->bestTicket() < $webinar->price)
                        <span class="real">{{ handlePrice($webinar->bestTicket()) }}</span>
                        <span class="off ml-10">{{ handlePrice($webinar->price) }}</span>
                    @else
                        <span class="real">{{ handlePrice($webinar->price) }}</span>
                    @endif
                @else
                    <span class="real font-14">{{ trans('public.free') }}</span>
                @endif
            </div>
        </figcaption>
    </figure>
</div>

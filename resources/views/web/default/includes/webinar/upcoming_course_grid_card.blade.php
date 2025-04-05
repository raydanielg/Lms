<div class="webinar-card">
    <figure>
        <div class="image-box">
            <div class="badges-lists">
                @if(!empty($upcomingCourse->webinar_id))
                    <span class="badge badge-secondary">{{ trans('update.released') }}</span>
                @endif

                @include('web.default.includes.product_custom_badge', ['itemTarget' => $upcomingCourse])
            </div>

            <a href="{{ $upcomingCourse->getUrl() }}">
                <img src="{{ $upcomingCourse->getImage() }}" class="img-cover" alt="{{ $upcomingCourse->title }}">
            </a>

            @if(empty($upcomingCourse->webinar_id))
                <a href="{{ $upcomingCourse->addToCalendarLink() }}" target="_blank" class="upcoming-bell d-flex align-items-center justify-content-center">
                    <i data-feather="bell" width="20" height="20"></i>
                </a>
            @endif
        </div>

        <figcaption class="webinar-card-body">
            <div class="user-inline-avatar d-flex align-items-center">
                <div class="avatar bg-gray200">
                    <img src="{{ $upcomingCourse->teacher->getAvatar() }}" class="img-cover" alt="{{ $upcomingCourse->teacher->full_name }}">
                </div>
                <a href="{{ $upcomingCourse->teacher->getProfileUrl() }}" target="_blank" class="user-name ml-5 font-14">{{ $upcomingCourse->teacher->full_name }}</a>
            </div>

            <a href="{{ $upcomingCourse->getUrl() }}">
                <h3 class="mt-15 webinar-title font-weight-bold font-16 text-dark-blue">{{ clean($upcomingCourse->title,'title') }}</h3>
            </a>

            @if(!empty($upcomingCourse->category))
                <span class="d-block font-14 mt-10">{{ trans('public.in') }} <a href="{{ $upcomingCourse->category->getUrl() }}" target="_blank" class="text-decoration-underline">{{ $upcomingCourse->category->title }}</a></span>
            @endif

            <div class="d-flex justify-content-between mt-20">
                @if(!empty($upcomingCourse->duration))
                    <div class="d-flex align-items-center">
                        <i data-feather="clock" width="20" height="20" class="webinar-icon"></i>
                        <span class="duration font-14 ml-5">{{ convertMinutesToHourAndMinute($upcomingCourse->duration) }} {{ trans('home.hours') }}</span>
                    </div>
                @endif

                @if(!empty($upcomingCourse->published_date))

                    @if(!empty($upcomingCourse->duration))
                        <div class="vertical-line mx-15"></div>
                    @endif

                    <div class="d-flex align-items-center">
                        <i data-feather="calendar" width="20" height="20" class="webinar-icon"></i>
                        <span class="date-published font-14 ml-5">{{ dateTimeFormat($upcomingCourse->published_date, 'j M Y') }}</span>
                    </div>
                @endif
            </div>

            <div class="webinar-price-box mt-25">
                @if(!empty($upcomingCourse->price) and $upcomingCourse->price > 0)
                    <span class="real">{{ handlePrice($upcomingCourse->price) }}</span>
                @else
                    <span class="real font-14">{{ trans('public.free') }}</span>
                @endif
            </div>
        </figcaption>
    </figure>
</div>

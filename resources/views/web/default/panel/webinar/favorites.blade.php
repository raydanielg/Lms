@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')

    <section>
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="section-title">{{ trans('panel.favorite_live_classes') }}</h2>
        </div>

        @if(!empty($favorites) and !$favorites->isEmpty())

            @foreach($favorites as $favorite)
                @php
                    $favItem = !empty($favorite->upcoming_course_id) ? $favorite->upcomingCourse : ((!empty($favorite->webinar_id)) ? $favorite->webinar : $favorite->bundle);
                @endphp

                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $favItem->getImage() }}" class="img-cover" alt="">

                                @if(!empty($favorite->webinar_id) and $favItem->type == 'webinar')
                                    <div class="progress">
                                        <span class="progress-bar" style="width: {{ $favItem->getProgress() }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $favItem->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $favItem->title }}</h3>
                                    </a>

                                    <div class="btn-group dropdown table-actions">
                                        <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i data-feather="more-vertical" height="20"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="/panel/webinars/favorites/{{ $favorite->id }}/delete" class="webinar-actions d-block delete-action">{{ trans('public.remove') }}</a>
                                        </div>
                                    </div>
                                </div>

                                @if(empty($favorite->upcoming_course_id))
                                    @include(getTemplate() . '.includes.webinar.rate',['rate' => $favItem->getRate()])
                                @endif

                                <div class="webinar-price-box mt-15">
                                    @if(empty($favorite->upcoming_course_id) and $favItem->bestTicket() < $favItem->price)
                                        <span class="real">{{ handlePrice($favItem->bestTicket(), true, true, false, null, true) }}</span>
                                        <span class="off ml-10">{{ handlePrice($favItem->price, true, true, false, null, true) }}</span>
                                    @else
                                        <span class="real">{{ handlePrice($favItem->price, true, true, false, null, true) }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $favItem->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($favItem->category_id) ? $favItem->category->title : '' }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.duration') }}:</span>
                                        <span class="stat-value">{{ convertMinutesToHourAndMinute($favItem->duration) }} {{ trans('home.hours') }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        @if(!empty($favorite->webinar_id) and $favItem->isWebinar())
                                            <span class="stat-title">{{ trans('public.start_date') }}:</span>
                                        @else
                                            <span class="stat-title">{{ trans('public.created_at') }}:</span>
                                        @endif

                                        <span class="stat-value">{{ dateTimeFormat(!empty($favItem->start_date) ? $favItem->start_date : $favItem->created_at,'j M Y') }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.instructor') }}:</span>
                                        <span class="stat-value">{{ $favItem->teacher->full_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'student.png',
                'title' => trans('panel.no_result_favorites'),
                'hint' =>  trans('panel.no_result_favorites_hint') ,
            ])
        @endif

    </section>

    <div class="my-30">
        {{ $favorites->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>
@endsection

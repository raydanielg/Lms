@extends(getTemplate().'.layouts.app')

@push('styles_top')

@endpush


@section('content')

    @php
        $resultCount = 0;

        if (!empty($webinars)) {
            $resultCount += count($webinars);
        }

        if (!empty($bundles)) {
            $resultCount += count($bundles);
        }

        if (!empty($upcomingCourses)) {
            $resultCount += count($upcomingCourses);
        }
    @endphp

    @if((!empty($webinars) and count($webinars)) or (!empty($bundles) and count($bundles)) or (!empty($upcomingCourses) and count($upcomingCourses)))
        <section class="site-top-banner search-top-banner opacity-04 position-relative">
            <img src="{{ getPageBackgroundSettings('tags') }}" class="img-cover" alt=""/>

            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-12 col-md-9 col-lg-7">
                        <div class="top-search-form">
                            <h1 class="text-white font-30 white-space-pre-wrap">{{ trans('site.result_find',['count' => $resultCount , 'search' => $tag]) }}</h1>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container">
            @if(!empty($webinars) and count($webinars))
                <section class="mt-50">
                    <h2 class="font-24 font-weight-bold text-secondary">{{ trans('product.courses') }}</h2>

                    <div class="row">
                        @foreach($webinars as $webinar)
                            <div class="col-md-6 col-lg-4 mt-30">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $webinar])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(!empty($bundles) and count($bundles))
                <section class="mt-50">
                    <h2 class="font-24 font-weight-bold text-secondary">{{ trans('update.bundles') }}</h2>

                    <div class="row">
                        @foreach($bundles as $bundle)
                            <div class="col-md-6 col-lg-4 mt-30">
                                @include('web.default.includes.webinar.grid-card',['webinar' => $bundle])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if(!empty($upcomingCourses) and count($upcomingCourses))
                <section class="mt-50">
                    <h2 class="font-24 font-weight-bold text-secondary">{{ trans('update.upcoming_courses') }}</h2>

                    <div class="row">
                        @foreach($upcomingCourses as $upcomingCourse)
                            <div class="col-md-6 col-lg-4 mt-30">
                                @include('web.default.includes.webinar.upcoming_course_grid_card',['upcomingCourse' => $upcomingCourse])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    @else

        <div class="no-result status-failed my-50 d-flex align-items-center justify-content-center flex-column">
            <div class="no-result-logo">
                <img src="/assets/default/img/no-results/search.png" alt="">
            </div>
            <div class="container">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-12 col-md-9 col-lg-7">
                        <div class="d-flex align-items-center flex-column mt-30 text-center w-100">
                            <h2>{{ trans('site.no_result_search') }}</h2>
                            <p class="mt-5 text-center white-space-pre-wrap">{{ trans('site.no_result_search_hint',['search' => $tag]) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

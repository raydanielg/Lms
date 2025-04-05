@php
    $statisticsSettings = getStatisticsSettings();
@endphp

@if(!empty($statisticsSettings['enable_statistics']))
    @if(!empty($statisticsSettings['display_default_statistics']) and !empty($homeDefaultStatistics))
        <div class="stats-container {{ ($heroSection == "2") ? 'page-has-hero-section-2' : '' }}">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                        <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                            <div class="stat-icon-box teacher">
                                <img src="/assets/default/img/stats/teacher.svg" alt="" class="img-fluid"/>
                            </div>
                            <strong class="stat-number mt-10">{{ $homeDefaultStatistics['skillfulTeachersCount'] }}</strong>
                            <h4 class="stat-title">{{ trans('home.skillful_teachers') }}</h4>
                            <p class="stat-desc mt-10">{{ trans('home.skillful_teachers_hint') }}</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                        <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                            <div class="stat-icon-box student">
                                <img src="/assets/default/img/stats/student.svg" alt="" class="img-fluid"/>
                            </div>
                            <strong class="stat-number mt-10">{{ $homeDefaultStatistics['studentsCount'] }}</strong>
                            <h4 class="stat-title">{{ trans('home.happy_students') }}</h4>
                            <p class="stat-desc mt-10">{{ trans('home.happy_students_hint') }}</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                        <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                            <div class="stat-icon-box video">
                                <img src="/assets/default/img/stats/video.svg" alt="" class="img-fluid"/>
                            </div>
                            <strong class="stat-number mt-10">{{ $homeDefaultStatistics['liveClassCount'] }}</strong>
                            <h4 class="stat-title">{{ trans('home.live_classes') }}</h4>
                            <p class="stat-desc mt-10">{{ trans('home.live_classes_hint') }}</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                        <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                            <div class="stat-icon-box course">
                                <img src="/assets/default/img/stats/course.svg" alt="" class="img-fluid"/>
                            </div>
                            <strong class="stat-number mt-10">{{ $homeDefaultStatistics['offlineCourseCount'] }}</strong>
                            <h4 class="stat-title">{{ trans('home.offline_courses') }}</h4>
                            <p class="stat-desc mt-10">{{ trans('home.offline_courses_hint') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(!empty($homeCustomStatistics))
        <div class="stats-container">
            <div class="container">
                <div class="row">
                    @foreach($homeCustomStatistics as $homeCustomStatistic)
                        <div class="col-sm-6 col-lg-3 mt-25 mt-lg-0">
                            <div class="stats-item d-flex flex-column align-items-center text-center py-30 px-5 w-100">
                                <div class="stat-icon-box " style="background-color: {{ $homeCustomStatistic->color }}">
                                    <img src="{{ $homeCustomStatistic->icon }}" alt="{{ $homeCustomStatistic->title }}" class="img-fluid"/>
                                </div>
                                <strong class="stat-number mt-10">{{ $homeCustomStatistic->count }}</strong>
                                <h4 class="stat-title">{{ $homeCustomStatistic->title }}</h4>
                                <p class="stat-desc mt-10">{{ $homeCustomStatistic->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="my-40"></div>
    @endif
@else
    <div class="my-40"></div>
@endif

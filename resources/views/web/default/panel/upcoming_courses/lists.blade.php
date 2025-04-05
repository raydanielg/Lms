@extends('web.default.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('update.overview') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/upcoming.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalCourses }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.total_courses') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $releasedCourses }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.released_courses') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $notReleased }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.not_released') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/49.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $followers }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.followers') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-25">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('update.my_upcoming_courses') }}</h2>

            <form action="" method="get">
                <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="cursor-pointer mb-0 mr-10 font-weight-500 font-14 text-gray" for="onlyReleasedSwitch">{{ trans('update.only_not_released_courses') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="only_not_released_courses" @if(request()->get('only_not_released_courses','') == 'on') checked @endif class="custom-control-input" id="onlyReleasedSwitch">
                        <label class="custom-control-label" for="onlyReleasedSwitch"></label>
                    </div>
                </div>
            </form>
        </div>

        @if(!empty($upcomingCourses) and !$upcomingCourses->isEmpty())
            @foreach($upcomingCourses as $upcomingCourse)
                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $upcomingCourse->getImage() }}" class="img-cover" alt="">

                                <div class="badges-lists">
                                    @if(!empty($upcomingCourse->webinar_id))
                                        <span class="badge badge-secondary">{{  trans('update.released') }}</span>
                                    @else
                                        @switch($upcomingCourse->status)
                                            @case(\App\Models\UpcomingCourse::$active)
                                                <span class="badge badge-primary">{{  trans('public.published') }}</span>
                                                @break
                                            @case(\App\Models\UpcomingCourse::$isDraft)
                                                <span class="badge badge-danger">{{ trans('public.draft') }}</span>
                                                @break
                                            @case(\App\Models\UpcomingCourse::$pending)
                                                <span class="badge badge-warning">{{ trans('public.waiting') }}</span>
                                                @break
                                            @case(\App\Models\UpcomingCourse::$inactive)
                                                <span class="badge badge-danger">{{ trans('public.rejected') }}</span>
                                                @break
                                        @endswitch
                                    @endif
                                </div>

                                @if(!empty($upcomingCourse->course_progress))
                                    <div class="progress">
                                        <span class="progress-bar {{ ($upcomingCourse->course_progress < 50) ? 'bg-warning' : '' }}" style="width: {{ $upcomingCourse->course_progress }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $upcomingCourse->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $upcomingCourse->title }}
                                            <span class="badge badge-dark ml-10 status-badge-dark">{{ trans('webinars.'.$upcomingCourse->type) }}</span>
                                        </h3>
                                    </a>

                                    @if($upcomingCourse->canAccess($authUser))
                                        <div class="btn-group dropdown table-actions">
                                            <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i data-feather="more-vertical" height="20"></i>
                                            </button>
                                            <div class="dropdown-menu ">
                                                @if(!empty($upcomingCourse->webinar_id))
                                                    <a href="{{ $upcomingCourse->webinar->getUrl() }}" class="webinar-actions d-block text-primary">{{ trans('update.view_course') }}</a>
                                                @else
                                                    @if($upcomingCourse->status == \App\Models\UpcomingCourse::$isDraft)
                                                        @can('panel_upcoming_courses_create')
                                                            <a href="/panel/upcoming_courses/{{ $upcomingCourse->id }}/step/4" class="js-send-for-reviewer webinar-actions btn-transparent d-block text-primary">{{ trans('update.send_for_reviewer') }}</a>
                                                        @endcan
                                                    @elseif($upcomingCourse->status == \App\Models\UpcomingCourse::$active)
                                                        <button type="button" data-id="{{ $upcomingCourse->id }}" class="js-mark-as-released webinar-actions btn-transparent d-block text-primary">{{ trans('update.mark_as_released') }}</button>
                                                    @endif

                                                    @can('panel_upcoming_courses_create')
                                                        <a href="/panel/upcoming_courses/{{ $upcomingCourse->id }}/edit" class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>
                                                    @endcan
                                                @endif

                                                @if($upcomingCourse->status == \App\Models\UpcomingCourse::$active)
                                                    @can('panel_upcoming_courses_followers')
                                                        <a href="/panel/upcoming_courses/{{ $upcomingCourse->id }}/followers" class="webinar-actions d-block mt-10">{{ trans('update.view_followers') }}</a>
                                                    @endcan
                                                @endif

                                                @if($upcomingCourse->creator_id == $authUser->id)
                                                    @can('panel_upcoming_courses_delete')
                                                        <a href="/panel/upcoming_courses/{{ $upcomingCourse->id }}/delete" class="webinar-actions d-block mt-10 text-danger delete-action">{{ trans('public.delete') }}</a>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $upcomingCourse->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($upcomingCourse->category_id) ? $upcomingCourse->category->title : '' }}</span>
                                    </div>

                                    @if(!empty($upcomingCourse->duration))
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('public.duration') }}:</span>
                                            <span class="stat-value">{{ convertMinutesToHourAndMinute($upcomingCourse->duration) }} Hrs</span>
                                        </div>
                                    @endif

                                    @if(!empty($upcomingCourse->publish_date))
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('update.estimated_publish_date') }}:</span>
                                            <span class="stat-value">{{ dateTimeFormat($upcomingCourse->publish_date, 'j M Y H:i') }}</span>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.price') }}:</span>
                                        <span class="stat-value">{{ (!empty($upcomingCourse->price)) ? handlePrice($upcomingCourse->price) : trans('public.free') }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('update.followers') }}:</span>
                                        <span class="stat-value">{{ $upcomingCourse->followers_count ?? 0 }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="my-30">
                {{ $upcomingCourses->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>

        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'webinar.png',
                'title' => trans('update.you_not_have_any_upcoming_courses'),
                'hint' =>  trans('update.you_not_have_any_upcoming_courses_hint') ,
                'btn' => ['url' => '/panel/upcoming_courses/new','text' => trans('update.create_a_upcoming_course') ]
            ])
        @endif
    </section>
@endsection

@push('scripts_bottom')

    <script src="/assets/default/js/panel/upcoming_course.min.js"></script>
@endpush

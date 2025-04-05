@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')

    <section class="mt-25">
        <h2 class="section-title">{{ trans('panel.filter_classes') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="/panel/webinars/organization_classes" method="get" class="row">
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.from') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="from" autocomplete="off" value="{{ request()->get('from') }}" class="form-control {{ !empty(request()->get('from')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.to') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="to" autocomplete="off" value="{{ request()->get('to') }}" class="form-control {{ !empty(request()->get('to')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-5">
                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('panel.course_type') }}</label>

                                <select name="type" class="custom-select">
                                    <option value="">{{ trans('public.all') }}</option>
                                    <option value="webinar" @if(request()->get('type') == 'webinar') selected @endif>{{ trans('webinars.webinar') }}</option>
                                    <option value="course" @if(request()->get('type') == 'course') selected @endif>{{ trans('product.course') }}</option>
                                    <option value="text_lesson" @if(request()->get('type') == 'text_lesson') selected @endif>{{ trans('webinars.text_lesson') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.sort_by') }}</label>
                                <select name="sort" class="form-control">
                                    <option value="">{{ trans('public.all') }}</option>
                                    <option value="newest" @if(request()->get('sort', null) == 'newest') selected="selected" @endif>{{ trans('public.newest') }}</option>
                                    <option value="expensive" @if(request()->get('sort', null) == 'expensive') selected="selected" @endif>{{ trans('public.expensive') }}</option>
                                    <option value="inexpensive" @if(request()->get('sort', null) == 'inexpensive') selected="selected" @endif>{{ trans('public.inexpensive') }}</option>
                                    <option value="bestsellers" @if(request()->get('sort', null) == 'bestsellers') selected="selected" @endif>{{ trans('public.bestsellers') }}</option>
                                    <option value="best_rates" @if(request()->get('sort', null) == 'best_rates') selected="selected" @endif>{{ trans('public.best_rates') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
                </div>
            </form>
        </div>
    </section>


    <section class="mt-25">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('panel.organization_classes') }}</h2>

            <form action="" method="get">
                <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="cursor-pointer mb-0 mr-10 text-gray font-14 font-weight-500" for="freeClassesSwitch">{{ trans('panel.only_free_classes') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="free" @if(request()->get('free','') == 'on') checked @endif class="custom-control-input" id="freeClassesSwitch">
                        <label class="custom-control-label" for="freeClassesSwitch"></label>
                    </div>
                </div>
            </form>
        </div>

        @if(!empty($webinars) and !$webinars->isEmpty())

            @foreach($webinars as $webinar)
                @php
                    $lastSession = $webinar->lastSession();
                    $nextSession = $webinar->nextSession();
                    $isProgressing = false;

                    if($webinar->start_date <= time() and !empty($lastSession) and $lastSession->date > time()) {
                        $isProgressing=true;
                    }
                @endphp

                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $webinar->getImage() }}" class="img-cover" alt="">

                                <div class="badges-lists">
                                    @switch($webinar->status)
                                        @case(\App\Models\Webinar::$active)
                                            @if($webinar->type == 'webinar')
                                                @if($webinar->start_date > time())
                                                    <span class="badge badge-primary">{{  trans('panel.not_conducted') }}</span>
                                                @elseif($webinar->isProgressing())
                                                    <span class="badge badge-secondary">{{ trans('webinars.in_progress') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ trans('public.finished') }}</span>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">{{ trans('webinars.'.$webinar->type) }}</span>
                                            @endif
                                            @break
                                        @case(\App\Models\Webinar::$isDraft)
                                            <span class="badge badge-danger">{{ trans('public.draft') }}</span>
                                            @break
                                        @case(\App\Models\Webinar::$pending)
                                            <span class="badge badge-warning">{{ trans('public.waiting') }}</span>
                                            @break
                                        @case(\App\Models\Webinar::$inactive)
                                            <span class="badge badge-danger">{{ trans('public.rejected') }}</span>
                                            @break
                                    @endswitch
                                </div>

                                @if($webinar->type == 'webinar')
                                    <div class="progress">
                                        <span class="progress-bar" style="width: {{ $webinar->getProgress() }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $webinar->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $webinar->title }}
                                            <span class="badge badge-dark status-badge-dark ml-10">{{ trans('webinars.'.$webinar->type) }}</span>

                                            @if($webinar->private)
                                                <span class="badge badge-danger status-badge-danger ml-10">{{ trans('webinars.private') }}</span>
                                            @endif
                                        </h3>
                                    </a>
                                </div>

                                @include(getTemplate() . '.includes.webinar.rate',['rate' => $webinar->getRate()])

                                <div class="webinar-price-box mt-15">
                                    @if($webinar->price > 0)
                                        @if($webinar->bestTicket() < $webinar->price)
                                            <span class="real">{{ handlePrice($webinar->bestTicket(), true, true, false, null, true) }}</span>
                                            <span class="off ml-10">{{ handlePrice($webinar->price, true, true, false, null, true) }}</span>
                                        @else
                                            <span class="real">{{ handlePrice($webinar->price, true, true, false, null, true) }}</span>
                                        @endif
                                    @else
                                        <span class="real">{{ trans('public.free') }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $webinar->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($webinar->category_id) ? $webinar->category->title : '' }}</span>
                                    </div>

                                    @if($webinar->isProgressing() and !empty($nextSession))
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('webinars.next_session_duration') }}:</span>
                                            <span class="stat-value">{{ convertMinutesToHourAndMinute($nextSession->duration) }} Hrs</span>
                                        </div>

                                        @if($webinar->isWebinar())
                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('webinars.next_session_start_date') }}:</span>
                                                <span class="stat-value">{{ dateTimeFormat($nextSession->date,'j M Y') }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('public.duration') }}:</span>
                                            <span class="stat-value">{{ convertMinutesToHourAndMinute($webinar->duration) }} Hrs</span>
                                        </div>

                                        @if($webinar->isWebinar())
                                            <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                                <span class="stat-title">{{ trans('public.start_date') }}:</span>
                                                <span class="stat-value">{{ dateTimeFormat($webinar->start_date,'j M Y') }}</span>
                                            </div>
                                        @endif
                                    @endif

                                    @if($webinar->isTextCourse() or $webinar->isCourse())
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('public.files') }}:</span>
                                            <span class="stat-value">{{ $webinar->files->count() }}</span>
                                        </div>
                                    @endif

                                    @if($webinar->isTextCourse())
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('webinars.text_lessons') }}:</span>
                                            <span class="stat-value">{{ $webinar->textLessons->count() }}</span>
                                        </div>
                                    @endif

                                    @if($webinar->isCourse())
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('home.downloadable') }}:</span>
                                            <span class="stat-value">{{ ($webinar->downloadable) ? trans('public.yes') : trans('public.no') }}</span>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('panel.sales') }}:</span>
                                        <span class="stat-value">{{ count($webinar->sales) }} ({{ (!empty($webinar->sales) and count($webinar->sales)) ? handlePrice($webinar->sales->sum('amount')) : 0 }})</span>
                                    </div>

                                    @if($authUser->id != $webinar->teacher_id and $authUser->id != $webinar->creator_id)
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('webinars.teacher_name') }}:</span>
                                            <span class="stat-value">{{ $webinar->teacher->full_name }}</span>
                                        </div>
                                    @elseif($authUser->id == $webinar->teacher_id and $authUser->id != $webinar->creator_id and $webinar->creator->isOrganization())
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('webinars.organization_name') }}:</span>
                                            <span class="stat-value">{{ $webinar->creator->full_name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="my-30">
                {{ $webinars->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>
        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'webinar.png',
                'title' => trans('panel.you_not_have_any_webinar'),
                'hint' =>  trans('panel.no_result_hint') ,
                'btn' => ['url' => '/panel/webinar/new','text' => trans('panel.create_a_webinar') ]
            ])
        @endif

    </section>

    @include('web.default.panel.webinar.make_next_session_modal')
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script>
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var selectChapterLang = '{{ trans('update.select_chapter') }}';
    </script>

    <script src="/assets/default/js/panel/make_next_session.min.js"></script>
@endpush

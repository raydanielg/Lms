@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')

    <section class="">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('product.courses') }}</h2>
        </div>

        @if(!empty($bundle->bundleWebinars) and !$bundle->bundleWebinars->isEmpty())
            @foreach($bundle->bundleWebinars as $bundleWebinar)
                @php
                    $webinar = $bundleWebinar->webinar;
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
                                            @if($webinar->isWebinar())
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

                                @if($webinar->isWebinar())
                                    <div class="progress">
                                        <span class="progress-bar" style="width: {{ $webinar->getProgress() }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $webinar->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $webinar->title }}
                                            <span class="badge badge-dark ml-10 status-badge-dark">{{ trans('webinars.'.$webinar->type) }}</span>
                                        </h3>
                                    </a>

                                    @if($authUser->id == $webinar->creator_id or $authUser->id == $webinar->teacher_id)
                                        <div class="btn-group dropdown table-actions">
                                            <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i data-feather="more-vertical" height="20"></i>
                                            </button>
                                            <div class="dropdown-menu ">
                                                @if(!empty($webinar->start_date) and ($authUser->id == $webinar->creator_id or $authUser->id == $webinar->teacher_id))
                                                    <button type="button" data-webinar-id="{{ $webinar->id }}" class="js-webinar-next-session webinar-actions btn-transparent d-block">{{ trans('public.create_join_link') }}</button>
                                                @endif

                                                <a href="{{ $webinar->getLearningPageUrl() }}" target="_blank" class="webinar-actions d-block mt-10">{{ trans('update.learning_page') }}</a>

                                                <a href="/panel/webinars/{{ $webinar->id }}/edit" class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>

                                                @if($webinar->isWebinar())
                                                    <a href="/panel/webinars/{{ $webinar->id }}/step/4" class="webinar-actions d-block mt-10">{{ trans('public.sessions') }}</a>
                                                @endif

                                                <a href="/panel/webinars/{{ $webinar->id }}/step/4" class="webinar-actions d-block mt-10">{{ trans('public.files') }}</a>


                                                @if($webinar->isOwner($authUser->id))
                                                    <a href="/panel/webinars/{{ $webinar->id }}/export-students-list" class="webinar-actions d-block mt-10">{{ trans('public.export_list') }}</a>
                                                @endif

                                                @if($authUser->id == $webinar->creator_id)
                                                    <a href="/panel/webinars/{{ $webinar->id }}/duplicate" class="webinar-actions d-block mt-10">{{ trans('public.duplicate') }}</a>
                                                @endif

                                                @if($webinar->isOwner($authUser->id))
                                                    <a href="/panel/webinars/{{ $webinar->id }}/statistics" class="webinar-actions d-block mt-10">{{ trans('update.statistics') }}</a>
                                                @endif

                                                @if($webinar->creator_id == $authUser->id)
                                                    <a href="/panel/webinars/{{ $webinar->id }}/delete" class="webinar-actions d-block mt-10 text-danger delete-action">{{ trans('public.delete') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @include(getTemplate() . '.includes.webinar.rate',['rate' => $webinar->getRate()])

                                <div class="webinar-price-box mt-15">
                                    @if($webinar->price > 0)
                                        @if($webinar->bestTicket() < $webinar->price)
                                            <span class="real">{{ handlePrice($webinar->bestTicket()) }}</span>
                                            <span class="off ml-10">{{ handlePrice($webinar->price) }}</span>
                                        @else
                                            <span class="real">{{ handlePrice($webinar->price) }}</span>
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

                                    @if(!empty($webinar->partner_instructor) and $webinar->partner_instructor and $authUser->id != $webinar->teacher_id and $authUser->id != $webinar->creator_id)
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('panel.invited_by') }}:</span>
                                            <span class="stat-value">{{ $webinar->teacher->full_name }}</span>
                                        </div>
                                    @elseif($authUser->id != $webinar->teacher_id and $authUser->id != $webinar->creator_id)
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

        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'webinar.png',
                'title' => trans('panel.you_not_have_any_webinar'),
                'hint' =>  trans('panel.no_result_hint') ,
                'btn' => ['url' => '/panel/webinars/new','text' => trans('panel.create_a_webinar') ]
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

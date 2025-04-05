@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    @if(empty($isCourseNotice))
        <section>
            <h2 class="section-title">{{ trans('update.noticeboard_statistics') }}</h2>

            <div class="activities-container mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/homework.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold mt-5">{{ $totalNoticeboards }}</strong>
                            <span class="font-16 text-dark-blue text-gray font-weight-500">{{ trans('update.total_noticeboards') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/58.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold mt-5">{{ $totalCourseNotices }}</strong>
                            <span class="font-16 text-dark-blue text-gray font-weight-500">{{ trans('update.course_notices') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/45.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalGeneralNotices }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('update.general_notices') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    <section class="mt-25">
        <h2 class="section-title">{{ trans('update.filter_noticeboards') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="{{ request()->url() }}" method="get" class="row">
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
                                    <input type="text" name="from" autocomplete="off" class="form-control @if(!empty(request()->get('from'))) datepicker @else datefilter @endif"
                                           aria-describedby="dateInputGroupPrepend" value="{{ request()->get('from','') }}"/>
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
                                    <input type="text" name="to" autocomplete="off" class="form-control @if(!empty(request()->get('to'))) datepicker @else datefilter @endif"
                                           aria-describedby="dateInputGroupPrepend" value="{{ request()->get('to','') }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label class="input-label">{{ trans('product.course') }}</label>
                                <select name="webinar_id" class="form-control select2">
                                    <option value="">{{ trans('webinars.all_courses') }}</option>

                                    @foreach($webinars as $webinar)
                                        <option value="{{ $webinar->id }}" @if(request()->get('webinar_id') == $webinar->id) selected @endif>{{ $webinar->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 {{ !empty($isCourseNotice) ? 'col-lg-5' : 'col-lg-8' }}">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.title') }}</label>
                                <input type="text" name="title" class="form-control" value="{{ request()->get('title') }}" placeholder="{{ trans('public.search') }}">
                            </div>
                        </div>

                        @if(!empty($isCourseNotice))
                            <div class="col-12 col-lg-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('update.color') }}</label>
                                    <select name="color" class="form-control select2">
                                        <option value="">{{ trans('update.all_colors') }}</option>

                                        @foreach(\App\Models\CourseNoticeboard::$colors as $noticeColor)
                                            <option value="{{ $noticeColor }}" @if(request()->get('color') == $noticeColor) selected @endif>{{ trans('update.course_noticeboard_color_'.$noticeColor) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
                </div>
            </form>
        </div>
    </section>

    <section class="mt-20">
        <h2 class="section-title">{{ trans('panel.noticeboards') }}</h2>

        @if(!empty($noticeboards) and !$noticeboards->isEmpty())

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center ">
                                <thead>
                                <tr>
                                    <th class="text-left text-gray">{{ trans('webinars.title') }}</th>
                                    <th class="text-center text-gray">{{ trans('site.message') }}</th>

                                    @if(!empty($isCourseNotice) and $isCourseNotice)
                                        <th class="text-center text-gray">{{ trans('update.color') }}</th>
                                    @else
                                        <th class="text-center text-gray">{{ trans('public.type') }}</th>
                                    @endif

                                    <th class="text-center text-gray">{{ trans('public.date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($noticeboards as $noticeboard)
                                    <tr class="noticeboard-item">
                                        <td class="text-left align-middle" width="25%">
                                            <span class="js-noticeboard-title d-block text-dark-blue font-weight-500">{{ $noticeboard->title }}</span>
                                            @if(!empty($noticeboard->webinar))
                                                <span class="d-block text-gray font-12">{{ $noticeboard->webinar->title }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <button type="button" class="js-view-message btn btn-sm btn-gray200">{{ trans('public.view') }}</button>
                                            <input type="hidden" class="js-noticeboard-message" value="{{ nl2br($noticeboard->message) }}">
                                        </td>
                                        <td class="text-dark-blue font-weight-500 align-middle">
                                            @if(!empty($isCourseNotice) and $isCourseNotice)
                                                {{ trans('update.course_noticeboard_color_'.$noticeboard->color) }}
                                            @else
                                                @if(!empty($noticeboard->instructor_id) and !empty($noticeboard->webinar_id))
                                                    {{ trans('product.course') }}
                                                @else
                                                    {{ trans('public.'.$noticeboard->type) }}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="js-noticeboard-time text-dark-blue font-weight-500 align-middle">{{ dateTimeFormat($noticeboard->created_at,'j M Y | H:i') }}</td>
                                        <td class="text-right align-middle">
                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('panel_noticeboard_create')
                                                        <a href="/panel/{{ (!empty($isCourseNotice) and $isCourseNotice) ? 'course-noticeboard' : 'noticeboard' }}/{{ $noticeboard->id }}/edit" class="webinar-actions d-block mt-10 text-hover-primary">{{ trans('public.edit') }}</a>
                                                    @endcan

                                                    @can('panel_noticeboard_delete')
                                                        <a href="/panel/{{ (!empty($isCourseNotice) and $isCourseNotice) ? 'course-noticeboard' : 'noticeboard' }}/{{ $noticeboard->id }}/delete" class="delete-action webinar-actions d-block mt-10 text-hover-primary">{{ trans('public.delete') }}</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'comment.png',
                'title' => trans('panel.comments_no_result'),
                'hint' =>  nl2br(trans('panel.comments_no_result_hint')) ,
            ])
        @endif
    </section>

    <div class="my-30">
        {{ $noticeboards->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>

    <div class="d-none" id="noticeboardMessageModal">
        <div class="text-center">
            <h3 class="modal-title font-16 font-weight-bold text-dark-blue"></h3>
            <span class="modal-time d-block font-12 text-gray mt-15"></span>
            <p class="modal-message font-weight-500 text-gray mt-2"></p>
        </div>
    </div>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/js/panel/noticeboard.min.js"></script>
@endpush

@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('quiz.my_certificates_statistics') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/56.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-500 mt-5">{{ $certificatesCount }}</strong>
                        <span class="font-16 font-weight-bold text-gray">{{ trans('panel.certificates') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-500 mt-5">{{ $avgGrades }}</strong>
                        <span class="font-16 font-weight-bold text-gray">{{ trans('quiz.average_grade') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/60.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-500 mt-5">{{ $failedQuizzes }}</strong>
                        <span class="font-16 font-weight-bold text-gray">{{ trans('quiz.failed_quizzes') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('quiz.filter_certificates') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="" method="get" class="row">
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
                                    <input type="text" name="from" autocomplete="off" class="form-control @if(!empty(request()->get('from'))) datepicker @else datefilter @endif" value="{{ request()->get('from','') }}" aria-describedby="dateInputGroupPrepend"/>
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
                                    <input type="text" name="to" autocomplete="off" class="form-control @if(!empty(request()->get('to'))) datepicker @else datefilter @endif" value="{{ request()->get('to','') }}" aria-describedby="dateInputGroupPrepend"/>
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
                                <select name="webinar_id" class="form-control">
                                    <option value="all">{{ trans('webinars.all_courses') }}</option>

                                    @foreach($userWebinars as $userWebinar)
                                        <option value="{{ $userWebinar->id }}" @if(request()->get('webinar_id','') == $userWebinar->id) selected @endif>{{ $userWebinar->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('quiz.quiz') }}</label>
                                        <select id="quizFilter" name="quiz_id" class="form-control" @if(empty(request()->get('quiz_id'))) disabled @endif>
                                            <option value="all">{{ trans('quiz.all_quizzes') }}</option>

                                            @foreach($userAllQuizzes as $userQuiz)
                                                <option value="{{ $userQuiz->id }}" data-webinar-id="{{ $userQuiz->webinar_id }}" @if(request()->get('quiz_id','') == $userQuiz->id) selected @else class="d-none" @endif>{{ $userQuiz->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('quiz.grade') }}</label>
                                        <input type="text" name="grade" value="{{ request()->get('grade','') }}" class="form-control"/>
                                    </div>
                                </div>
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

    <section class="mt-35">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('quiz.my_certificates') }}</h2>
        </div>

        @if(!empty($quizzes) and count($quizzes))
            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('public.certificate') }}</th>
                                    <th class="text-center">{{ trans('public.certificate_id') }}</th>
                                    <th class="text-center">{{ trans('quiz.minimum_grade') }}</th>
                                    <th class="text-center">{{ trans('quiz.average_grade') }}</th>
                                    <th class="text-center">{{ trans('quiz.my_grade') }}</th>
                                    <th class="text-center">{{ trans('public.date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($quizzes as $quiz)
                                    <tr>
                                        <td class="text-left">
                                            <span class="d-block text-dark-blue font-weight-500">{{ $quiz->title }}</span>
                                            <span class="d-block font-12 text-gray mt-5">{{ $quiz->webinar->title }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if($quiz->can_download_certificate)
                                                @php
                                                    $getUserCertificate = $quiz->getUserCertificate($authUser,$quiz->result);
                                                @endphp

                                                @if(!empty($getUserCertificate))
                                                    {{ $getUserCertificate->id }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-dark-blue font-weight-500">{{ $quiz->pass_mark }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-dark-blue font-weight-500">{{ $quiz->total_mark }}</span>
                                        </td>
                                        <td class="align-middle">{{ $quiz->result->user_grade }}</td>
                                        <td class="align-middle">
                                            <span class="text-dark-blue font-weight-500">{{ dateTimeFormat($quiz->result->created_at, 'j M Y') }}</span>
                                        </td>
                                        <td class="align-middle font-weight-normal">
                                            @if($quiz->can_download_certificate)
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i data-feather="more-vertical" height="20"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="/panel/quizzes/results/{{ $quiz->result->id }}/showCertificate" target="_blank" class="webinar-actions d-block">{{ trans('public.open') }}</a>
                                                    </div>
                                                </div>
                                            @endif
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
                'file_name' => 'cert.png',
                'title' => trans('quiz.my_certificates_no_result'),
                'hint' => nl2br(trans('quiz.my_certificates_no_result_hint')),
            ])
        @endif
    </section>

    <div class="my-30">
        {{ $quizzes->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script src="/assets/default/js/panel/certificates.min.js"></script>
@endpush

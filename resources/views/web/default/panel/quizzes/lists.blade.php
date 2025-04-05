@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('panel.comments_statistics') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/46.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $quizzesCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('quiz.quizzes') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/47.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $questionsCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('public.questions') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/48.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $userCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('quiz.students') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('quiz.filter_quizzes') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="/panel/quizzes" method="get" class="row">
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
                                    <input type="text" name="from" autocomplete="off" class="form-control @if(!empty(request()->get('from'))) datepicker @else datefilter @endif" aria-describedby="dateInputGroupPrepend" value="{{ request()->get('from','') }}"/>
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
                                    <input type="text" name="to" autocomplete="off" class="form-control @if(!empty(request()->get('to'))) datepicker @else datefilter @endif" aria-describedby="dateInputGroupPrepend" value="{{ request()->get('to','') }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('quiz.quiz_or_webinar') }}</label>
                                <select name="quiz_id" class="form-control select2" data-placeholder="{{ trans('public.all') }}">
                                    <option value="all">{{ trans('public.all') }}</option>

                                    @foreach($allQuizzesLists as $allQuiz)
                                        <option value="{{ $allQuiz->id }}" @if(request()->get('quiz_id') == $allQuiz->id) selected @endif>{{ $allQuiz->title .' - '. ($allQuiz->webinar ? $allQuiz->webinar->title : '-') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('public.total_mark') }}</label>
                                        <input type="text" name="total_mark" class="form-control" value="{{ request()->get('total_mark','') }}"/>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('public.status') }}</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="all">{{ trans('public.all') }}</option>
                                            <option value="active" @if(request()->get('status') == 'active') selected @endif >{{ trans('public.active') }}</option>
                                            <option value="inactive" @if(request()->get('status') == 'inactive') selected @endif >{{ trans('public.inactive') }}</option>
                                        </select>
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
            <h2 class="section-title">{{ trans('quiz.quizzes') }}</h2>

            <form action="/panel/quizzes" method="get" class="">
                <div class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="mb-0 mr-10 cursor-pointer text-gray font-14 font-weight-500" for="activeQuizzesSwitch">{{ trans('quiz.show_only_active_quizzes') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="active_quizzes" class="custom-control-input" id="activeQuizzesSwitch" @if(request()->get('active_quizzes',null) == 'on') checked @endif>
                        <label class="custom-control-label" for="activeQuizzesSwitch"></label>
                    </div>
                </div>
            </form>
        </div>

        @if($quizzes->count() > 0)

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th class="text-left">{{ trans('public.title') }}</th>
                                    <th class="text-center">{{ trans('public.questions') }}</th>
                                    <th class="text-center">{{ trans('public.time') }} <span class="braces">({{ trans('public.min') }})</span></th>
                                    <th class="text-center">{{ trans('public.total_mark') }}</th>
                                    <th class="text-center">{{ trans('public.pass_mark') }}</th>
                                    <th class="text-center">{{ trans('quiz.students') }}</th>
                                    {{--<th>{{ trans('quiz.average') }}</th>--}}
                                    <th class="text-center">{{ trans('public.status') }}</th>
                                    <th class="text-center">{{ trans('public.date_created') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($quizzes as $quiz)
                                    <tr>
                                        <td class="text-left">
                                            <span class="d-block">{{ $quiz->title }}</span>
                                            <span class="font-12 text-gray d-block">
                                                @if(!empty($quiz->webinar))
                                                    {{ $quiz->webinar->title }}
                                                @else
                                                    {{ trans('panel.not_assign_any_webinar') }}
                                                @endif
                                        </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            {{ $quiz->quizQuestions->count() }}
                                            @if(($quiz->display_limited_questions and !empty($quiz->display_number_of_questions)))
                                                <span class="font-12 text-gray">({{ trans('public.active') }}: {{ $quiz->display_number_of_questions }})</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">{{ $quiz->time }}</td>
                                        <td class="text-center align-middle">{{ $quiz->quizQuestions->sum('grade') }}</td>
                                        <td class="text-center align-middle">{{ $quiz->pass_mark }}</td>
                                        <td class="text-center align-middle">
                                            <span class="d-block">{{ $quiz->quizResults->pluck('user_id')->count() }}</span>

                                            @if(!empty($quiz->userSuccessRate) and $quiz->userSuccessRate > 0)
                                                <span class="font-12 text-primary d-block">{{ $quiz->userSuccessRate }}% {{ trans('quiz.passed')  }}</span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">{{ trans('public.'.$quiz->status) }}</td>

                                        <td class="text-center align-middle">{{ dateTimeFormat($quiz->created_at, 'j M Y H:i') }}</td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu font-weight-normal">
                                                    @can('panel_quizzes_create')
                                                        <a href="/panel/quizzes/{{ $quiz->id }}/edit" class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>
                                                    @endcan

                                                    @can('panel_quizzes_delete')
                                                        <a href="/panel/quizzes/{{ $quiz->id }}/delete" data-item-id="1" class="webinar-actions d-block mt-10 delete-action">{{ trans('public.delete') }}</a>
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
                'file_name' => 'quiz.png',
                'title' => trans('quiz.quiz_no_result'),
                'hint' => nl2br(trans('quiz.quiz_no_result_hint')),
                'btn' => ['url' => '/panel/quizzes/new','text' => trans('quiz.create_a_quiz')]
            ])

        @endif

    </section>

    <div class="my-30">
        {{ $quizzes->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>

    <script src="/assets/default/js/panel/quiz_list.min.js"></script>
@endpush

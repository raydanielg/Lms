@extends(getTemplate().'.layouts.app')

@section('content')
    <div class="container">
        <section class="mt-40">
            <h2 class="font-weight-bold font-16 text-dark-blue">{{ trans('quiz.level_identification_quiz') }}</h2>
            <p class="text-gray font-14 mt-5">{{ $quiz->title }} | {{ trans('public.by') }} <span class="font-weight-bold">{{ $quiz->creator->full_name }}</span></p>

            <div class="activities-container shadow-sm rounded-lg mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/58.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{  $quiz->pass_mark }}/{{  $quizQuestions->sum('grade') }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('public.min') }} {{ trans('quiz.grade') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/88.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $attempt_count }}/{{ $quiz->attempt }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('quiz.attempts') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/45.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{  $quizResult->user_grade }}/{{  $quizQuestions->sum('grade') }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('quiz.your_grade') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/44.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-{{ ($quizResult->status == 'passed') ? 'primary' : ($quizResult->status == 'waiting' ? 'warning' : 'danger') }} mt-5">
                                {{ trans('quiz.'.$quizResult->status) }}
                            </strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('public.status') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="mt-30 rounded-lg shadow-sm py-25 px-20">

                @switch($quizResult->status)

                @case(\App\Models\QuizzesResult::$passed)
                    <div class="no-result default-no-result mt-50 d-flex align-items-center justify-content-center flex-column">
                        <div class="no-result-logo">
                            <img src="/assets/default/img/no-results/497.png" alt="">
                        </div>
                        <div class="d-flex align-items-center flex-column mt-30 text-center">
                            <h2 class="section-title">{{ trans('quiz.status_passed_title') }}</h2>
                            <p class="mt-5 text-center">{!! trans('quiz.status_passed_hint',['grade' => $quizResult->user_grade.'/'.$quizQuestions->sum('grade')]) !!}</p>

                            @if($quiz->certificate)
                                <p>{{ trans('quiz.you_can_download_certificate') }}</p>
                            @endif

                            <div class=" mt-25">
                                <a href="/panel/quizzes/my-results" class="btn btn-sm btn-primary">{{ trans('public.show_results') }}</a>

                                @if($quiz->certificate)
                                    <a href="/panel/quizzes/results/{{ $quizResult->id }}/showCertificate" class="btn btn-sm btn-primary">{{ trans('quiz.download_certificate') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @break

                @case(\App\Models\QuizzesResult::$failed)
                    <div class="no-result status-failed mt-50 d-flex align-items-center justify-content-center flex-column">
                        <div class="no-result-logo">
                            <img src="/assets/default/img/no-results/339.png" alt="">
                        </div>
                        <div class="d-flex align-items-center flex-column mt-30 text-center">
                            <h2 class="section-title">{{ trans('quiz.status_failed_title') }}</h2>
                            <p class="mt-5 text-center">{!! trans('quiz.status_failed_hint',['min_grade' =>  $quiz->pass_mark .'/'. $quizQuestions->sum('grade'),'user_grade' => $quizResult->user_grade]) !!}</p>
                            @if($canTryAgain)
                                <p>{{ trans('public.you_can_try_again') }}</p>
                            @endif
                            <div class=" mt-25">
                                @if($canTryAgain)
                                    <a href="/panel/quizzes/{{ $quiz->id }}/start" class="btn btn-sm btn-primary">{{ trans('public.try_again') }}</a>
                                @endif
                                <a href="/panel/quizzes/my-results" class="btn btn-sm btn-primary">{{ trans('public.show_results') }}</a>
                            </div>
                        </div>
                    </div>
                @break

                @case(\App\Models\QuizzesResult::$waiting)
                    <div class="no-result status-waiting mt-50 d-flex align-items-center justify-content-center flex-column">
                        <div class="no-result-logo">
                            <img src="/assets/default/img/no-results/242.png" alt="">
                        </div>
                        <div class="d-flex align-items-center flex-column mt-30 text-center">
                            <h2 class="section-title">{{ trans('quiz.status_waiting_title') }}</h2>
                            <p class="mt-5 text-center">{!! nl2br(trans('quiz.status_waiting_hint')) !!}</p>
                            <div class=" mt-25">
                                <a href="/panel/quizzes/my-results" class="btn btn-sm btn-primary">{{ trans('public.show_results') }}</a>
                            </div>
                        </div>
                    </div>
                @break
            @endswitch

        </section>

    </div>
@endsection

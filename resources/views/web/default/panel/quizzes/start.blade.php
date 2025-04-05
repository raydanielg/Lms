@extends(getTemplate().'.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/video/video-js.min.css">
@endpush

@section('content')
    <div class="container">
        <section class="mt-40">
            <h2 class="font-weight-bold font-16 text-dark-blue">{{ $quiz->title }}</h2>
            <p class="text-gray font-14 mt-5">
                <a href="{{ $quiz->webinar->getUrl() }}" target="_blank" class="text-gray">{{ $quiz->webinar->title }}</a>
                | {{ trans('public.by') }}
                <span class="font-weight-bold">
                    <a href="{{ $quiz->creator->getProfileUrl() }}" target="_blank" class="font-14"> {{ $quiz->creator->full_name }}</a>
                </span>
            </p>

            <div class="activities-container shadow-sm rounded-lg mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/58.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{  $quiz->pass_mark }}/{{  $quizQuestions->sum('grade') }}</strong>
                            <span class="font-16 text-gray">{{ trans('public.min') }} {{ trans('quiz.grade') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/88.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $attempt_count }}/{{ $quiz->attempt }}</strong>
                            <span class="font-16 text-gray">{{ trans('quiz.attempts') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/47.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $totalQuestionsCount }}</strong>
                            <span class="font-16 text-gray">{{ trans('public.questions') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/clock.svg" width="64" height="64" alt="">
                            @if(!empty($quiz->time))
                                <strong class="font-30 font-weight-bold text-secondary mt-5">
                                    <div class="d-flex align-items-center timer ltr" data-minutes-left="{{ $quiz->time }}"></div>
                                </strong>
                            @else
                                <strong class="font-30 font-weight-bold text-secondary mt-5">{{ trans('quiz.unlimited') }}</strong>
                            @endif
                            <span class="font-16 text-gray">{{ trans('quiz.remaining_time') }}</span>
                        </div>
                    </div>


                </div>
            </div>
        </section>

        <section class="mt-30 quiz-form">
            <form action="/panel/quizzes/{{ $quiz->id }}/store-result" method="post" class="">
                {{ csrf_field() }}
                <input type="hidden" name="quiz_result_id" value="{{ $newQuizStart->id }}" class="form-control" placeholder=""/>
                <input type="hidden" name="attempt_number" value="{{ $attempt_count }}" class="form-control" placeholder=""/>

                @foreach($quizQuestions as $key => $question)

                    <fieldset class="question-step question-step-{{ $key + 1 }}">
                        <div class="rounded-lg shadow-sm py-25 px-20">
                            <div class="quiz-card">

                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="text-gray font-14">
                                        <span>{{ trans('quiz.question_grade') }} : {{ $question->grade }} </span>
                                    </p>

                                    <div class="rounded-sm border border-gray200 p-15 text-gray">{{ $key + 1 }}/{{ $totalQuestionsCount }}</div>
                                </div>

                                @if(!empty($question->image) or !empty($question->video))
                                    <div class="quiz-question-media-card rounded-lg mt-10 mb-15">
                                        @if(!empty($question->image))
                                            <img src="{{ $question->image }}" class="img-cover rounded-lg" alt="">
                                        @else
                                            <video id="questionVideo{{ $question->id }}" oncontextmenu="return false;" controlsList="nodownload" class="video-js" controls preload="auto" width="100%" data-setup='{"fluid": true}'>
                                                <source src="{{ $question->video }}" type="video/mp4"/>
                                            </video>
                                        @endif
                                    </div>
                                @endif

                                <div class="">
                                    <h3 class="font-weight-bold font-16 text-secondary">{{ $question->title }}</h3>
                                </div>

                                @if($question->type === \App\Models\QuizzesQuestion::$descriptive)
                                    <div class="form-group mt-35">
                                        <textarea name="question[{{ $question->id }}][answer]" rows="15" class="form-control"></textarea>
                                    </div>
                                @else
                                    <div class="question-multi-answers mt-35">
                                        @foreach($question->quizzesQuestionsAnswers as $key => $answer)
                                            <div class="answer-item">
                                                <input id="asw-{{ $answer->id }}" type="radio" name="question[{{ $question->id }}][answer]" value="{{ $answer->id }}">
                                                @if(!$answer->image)
                                                    <label for="asw-{{ $answer->id }}" class="answer-label font-16 text-dark-blue d-flex align-items-center justify-content-center">
                                                            <span class="answer-title">
                                                                {{ $answer->title }}
                                                            </span>
                                                    </label>
                                                @else
                                                    <label for="asw-{{ $answer->id }}" class="answer-label font-16 text-dark-blue d-flex align-items-center justify-content-center">
                                                        <div class="image-container">
                                                            <img src="{{ config('app_url') . $answer->image }}" class="img-cover" alt="">
                                                        </div>
                                                    </label>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </fieldset>
                @endforeach

                <div class="d-flex align-items-center mt-30">
                    <button type="button" class="previous btn btn-sm btn-primary mr-20">{{ trans('quiz.previous_question') }}</button>
                    <button type="button" class="next btn btn-sm btn-primary mr-auto">{{ trans('quiz.next_question') }}</button>
                    <button type="submit" class="finish btn btn-sm btn-danger">{{ trans('public.finish') }}</button>
                </div>
            </form>
        </section>

    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/video/video.min.js"></script>
    <script src="/assets/default/vendors/jquery.simple.timer/jquery.simple.timer.js"></script>
    <script src="/assets/default/js/parts/quiz-start.min.js"></script>
@endpush

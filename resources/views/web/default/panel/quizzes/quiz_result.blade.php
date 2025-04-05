@extends(getTemplate().'.layouts.app')

@section('content')
    <div class="container">
        <section class="mt-40">
            <h2 class="font-weight-bold font-16 text-dark-blue">{{ $quiz->title }}</h2>
            <p class="text-gray font-14 mt-5">
                <a href="{{ $quiz->webinar->getUrl() }}" target="_blank" class="text-gray">{{ $quiz->webinar->title }}</a>
                | {{ trans('public.by') }}
                <span class="font-weight-bold">
                    <a href="{{ $quiz->creator->getProfileUrl() }}" target="_blank" class=""> {{ $quiz->creator->full_name }}</a>
                </span>
            </p>

            <div class="activities-container shadow-sm rounded-lg mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/58.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $quiz->pass_mark }}/{{ $questionsSumGrade }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('public.min') }} {{ trans('quiz.grade') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/88.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $numberOfAttempt }}/{{ $quiz->attempt }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('quiz.attempts') }}</span>
                        </div>
                    </div>

                    <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/45.svg" width="64" height="64" alt="">
                            <strong class="font-30 font-weight-bold text-secondary mt-5">{{ $quizResult->user_grade }}/{{  $questionsSumGrade }}</strong>
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

        <section class="mt-30 quiz-form">
            <form action="{{ !empty($newQuizStart) ? '/panel/quizzes/'. $newQuizStart->quiz->id .'/update-result' : '' }} " method="post">
                {{ csrf_field() }}
                <input type="hidden" name="quiz_result_id" value="{{ !empty($newQuizStart) ? $newQuizStart->id : ''}}" class="form-control" placeholder=""/>
                <input type="hidden" name="attempt_number" value="{{  $numberOfAttempt }}" class="form-control" placeholder=""/>
                <input type="hidden" class="js-quiz-question-count" value="{{ $quizQuestions->count() }}"/>

                @foreach($quizQuestions as $key => $question)

                    <fieldset class="question-step question-step-{{ $key + 1 }}">
                        <div class="rounded-lg shadow-sm py-25 px-20">
                            <div class="quiz-card">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="font-weight-bold font-16 text-secondary">{{ $question->title }}?</h3>
                                        <p class="text-gray font-14 mt-5">
                                            <span>{{ trans('quiz.question_grade') }} : {{ $question->grade }}</span> | <span>{{ trans('quiz.your_grade') }} : {{ (!empty($userAnswers[$question->id]) and !empty($userAnswers[$question->id]["grade"])) ? $userAnswers[$question->id]["grade"] : 0 }}</span>
                                        </p>
                                    </div>

                                    <div class="rounded-sm border border-gray200 p-15 text-gray">{{ $key + 1 }}/{{ $quizQuestions->count() }}</div>
                                </div>
                                @if($question->type === \App\Models\QuizzesQuestion::$descriptive)

                                    <div class="form-group mt-35">
                                        <label class="input-label text-secondary">{{ trans('quiz.student_answer') }}</label>
                                        <textarea name="question[{{ $question->id }}][answer]" rows="10" disabled class="form-control">{{ (!empty($userAnswers[$question->id]) and !empty($userAnswers[$question->id]["answer"])) ? $userAnswers[$question->id]["answer"] : '' }}</textarea>
                                    </div>

                                    <div class="form-group mt-35">
                                        <label class="input-label text-secondary">{{ trans('quiz.correct_answer') }}</label>
                                        <textarea rows="10" name="question[{{ $question->id }}][correct_answer]" @if(empty($newQuizStart) or $newQuizStart->quiz->creator_id != $authUser->id) disabled @endif class="form-control">{{ $question->correct }}</textarea>
                                    </div>

                                    @if(!empty($newQuizStart) and $newQuizStart->quiz->creator_id == $authUser->id)
                                        <div class="form-group mt-35">
                                            <label class="font-16 text-secondary">{{ trans('quiz.grade') }}</label>
                                            <input type="text" name="question[{{ $question->id }}][grade]" value="{{ (!empty($userAnswers[$question->id]) and !empty($userAnswers[$question->id]["grade"])) ? $userAnswers[$question->id]["grade"] : 0 }}" class="form-control">
                                        </div>
                                    @endif

                                @else
                                    <div class="question-multi-answers mt-35">
                                        @foreach($question->quizzesQuestionsAnswers as $key => $answer)
                                            <div class="answer-item">
                                                @if($answer->correct)
                                                    <span class="badge badge-primary correct">{{ trans('quiz.correct') }}</span>
                                                @endif

                                                <input id="asw-{{ $answer->id }}" type="radio" disabled name="question[{{ $question->id }}][answer]" value="{{ $answer->id }}" {{ (!empty($userAnswers[$question->id]) and (int)$userAnswers[$question->id]["answer"] === $answer->id) ? 'checked' : '' }}>

                                                @if(!$answer->image)
                                                    <label for="asw-{{ $answer->id }}" class="answer-label font-16 d-flex text-dark-blue align-items-center justify-content-center ">
                                                        <span class="answer-title">
                                                            {{ $answer->title }}
                                                            @if(!empty($userAnswers[$question->id]) and (int)$userAnswers[$question->id]["answer"] ===  $answer->id)
                                                                <span class="d-block">({{ trans('quiz.student_answer') }})</span>
                                                            @endif
                                                        </span>
                                                    </label>
                                                @else
                                                    <label for="asw-{{ $answer->id }}" class="answer-label font-16 d-flex align-items-center text-dark-blue justify-content-center ">
                                                        <div class="image-container">
                                                            @if(!empty($userAnswers[$question->id]) and (int)$userAnswers[$question->id]["answer"] ===  $answer->id)
                                                                <span class="selected font-14">{{ trans('quiz.student_answer') }}</span>
                                                            @endif
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
                    <button type="button" disabled class="previous btn btn-sm btn-primary mr-20">{{ trans('quiz.previous_question') }}</button>
                    <button type="button" class="next btn btn-primary btn-sm mr-auto">{{ trans('quiz.next_question') }}</button>

                    @if(!empty($newQuizStart))
                        <button type="submit" class="finish btn btn-sm btn-danger">{{ trans('public.finish') }}</button>
                    @endif
                </div>
            </form>
        </section>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/quiz-start.min.js"></script>
@endpush

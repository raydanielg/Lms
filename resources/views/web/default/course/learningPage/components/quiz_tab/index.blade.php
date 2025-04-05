<div class="content-tab p-15 pb-50">
    @if(!empty($course->quizzes) and $course->quizzes->count())
        @foreach($course->quizzes as $quiz)
            @include('web.default.course.learningPage.components.quiz_tab.quiz',['item' => $quiz, 'type' => 'quiz','class' => 'px-10 border border-gray200 rounded-sm mb-15'])
        @endforeach

    @else
        <div class="learning-page-forum-empty d-flex align-items-center justify-content-center flex-column">
            <div class="learning-page-forum-empty-icon d-flex align-items-center justify-content-center">
                <img src="/assets/default/img/learning/quiz-empty.svg" class="img-fluid" alt="">
            </div>

            <div class="d-flex align-items-center flex-column mt-10 text-center">
                <h3 class="font-20 font-weight-bold text-dark-blue text-center">{{ trans('update.learning_page_empty_quiz_title') }}</h3>
                <p class="font-14 font-weight-500 text-gray mt-5 text-center">{{ trans('update.learning_page_empty_quiz_hint') }}</p>
            </div>
        </div>
    @endif
</div>

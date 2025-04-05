{{-- Sessions --}}

@if(!empty($course->chapters) and count($course->chapters))
    <section class="">
        @include('web.default.course.tabs.contents.chapter')
    </section>
@endif

@if(!empty($sessionsWithoutChapter) and count($sessionsWithoutChapter))
    <section class="mt-20">
        <div class="row">
            <div class="col-12">
                <div class="accordion-content-wrapper" id="sessionsAccordion" role="tablist" aria-multiselectable="true">
                    @foreach($sessionsWithoutChapter as $session)
                        @include('web.default.course.tabs.contents.sessions' , ['session' => $session, 'accordionParent' => 'sessionsAccordion'])
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

{{-- Files --}}

@if(!empty($filesWithoutChapter) and count($filesWithoutChapter))
    <section class="mt-20">
        <div class="row">
            <div class="col-12">
                <div class="accordion-content-wrapper" id="filesAccordion" role="tablist" aria-multiselectable="true">
                    @foreach($filesWithoutChapter as $file)
                        @include('web.default.course.tabs.contents.files' , ['file' => $file, 'accordionParent' => 'filesAccordion'])
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif

{{-- TextLessons --}}

@if(!empty($textLessonsWithoutChapter) and count($textLessonsWithoutChapter))
    <section class="mt-20">
        <div class="row">
            <div class="col-12">
                <div class="accordion-content-wrapper" id="textLessonsAccordion" role="tablist" aria-multiselectable="true">
                    @foreach($textLessonsWithoutChapter as $textLesson)
                        @include('web.default.course.tabs.contents.text_lessons' , ['textLesson' => $textLesson, 'accordionParent' => 'textLessonsAccordion'])
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif


{{-- Quizzes --}}
@if(!empty($quizzes) and $quizzes->count() > 0)
    <section class="mt-20">
        <h2 class="section-title after-line">{{ trans('update.quiz_and_certificates') }}</h2>

        <div class="row">
            <div class="col-12">
                <div class="accordion-content-wrapper" id="quizAccordion" role="tablist" aria-multiselectable="true">
                    @foreach($quizzes as $quiz)
                        @include('web.default.course.tabs.contents.quiz' , ['quiz' => $quiz, 'accordionParent' => 'quizAccordion'])
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Certificates --}}

    <section class="">
        @include('web.default.course.tabs.contents.certificate' , ['quizzes' => $course->quizzes])
    </section>
@endif


@include('web.default.course.tabs.play_modal.play_modal')

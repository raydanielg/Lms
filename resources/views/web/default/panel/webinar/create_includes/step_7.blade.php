@push('styles_top')

@endpush


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.quiz_certificate') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddQuiz" data-webinar-id="{{ $webinar->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_quiz') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="quizzesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($webinar->quizzes) and count($webinar->quizzes))
                    @foreach($webinar->quizzes as $quizInfo)
                        @include('web.default.panel.webinar.create_includes.accordions.quiz',['webinar' => $webinar,'quizInfo' => $quizInfo])
                    @endforeach
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'cert.png',
                        'title' => trans('public.quizzes_no_result'),
                        'hint' => trans('public.quizzes_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<div id="newQuizForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.quiz',['webinar' => $webinar,'quizInfo' => null])
</div>


@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var quizzesSectionLang = '{{ trans('quiz.quizzes_section') }}';
    </script>

    <script src="/assets/default/js/panel/quiz.min.js"></script>
@endpush

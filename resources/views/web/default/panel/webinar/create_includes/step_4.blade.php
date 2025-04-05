@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.chapters') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button type="button" class="js-add-chapter btn btn-primary btn-sm mt-15" data-webinar-id="{{ $webinar->id }}">{{ trans('public.new_chapter') }}</button>

    @include('web.default.panel.webinar.create_includes.accordions.chapter')
</section>

@if($webinar->isWebinar())
    <div id="newSessionForm" class="d-none">
        @include('web.default.panel.webinar.create_includes.accordions.session',['webinar' => $webinar])
    </div>
@endif

<div id="newFileForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.file',['webinar' => $webinar])
</div>

@if(getFeaturesSettings('new_interactive_file'))
    <div id="newInteractiveFileForm" class="d-none">
        @include('web.default.panel.webinar.create_includes.accordions.new_interactive_file',['webinar' => $webinar])
    </div>
@endif


<div id="newTextLessonForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.text-lesson',['webinar' => $webinar])
</div>

<div id="newQuizForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.quiz',['webinar' => $webinar, 'quizInfo' => null, 'webinarChapterPages' => true])
</div>

@if(getFeaturesSettings('webinar_assignment_status'))
    <div id="newAssignmentForm" class="d-none">
        @include('web.default.panel.webinar.create_includes.accordions.assignment',['webinar' => $webinar])
    </div>
@endif

@include('web.default.panel.webinar.create_includes.chapter_modal')

@include('web.default.panel.webinar.create_includes.change_chapter_modal')

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>

    <script>
        var requestFailedLang = '{{ trans('public.request_failed') }}';
        var thisLiveHasEndedLang = '{{ trans('update.this_live_has_been_ended') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var quizzesSectionLang = '{{ trans('quiz.quizzes_section') }}';
    </script>

    <script src="/assets/default/js/panel/quiz.min.js"></script>
@endpush

<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.chapters') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button type="button" class="js-add-chapter btn btn-primary btn-sm mt-15" data-webinar-id="{{ $webinar->id }}">{{ trans('public.new_chapter') }}</button>

    @include('admin.webinars.create_includes.accordions.chapter')
</section>

@if($webinar->isWebinar())
    <div id="newSessionForm" class="d-none">
        @include('admin.webinars.create_includes.accordions.session',['webinar' => $webinar])
    </div>
@endif

<div id="newFileForm" class="d-none">
    @include('admin.webinars.create_includes.accordions.file',['webinar' => $webinar])
</div>

@if(getFeaturesSettings('new_interactive_file'))
    <div id="newInteractiveFileForm" class="d-none">
        @include('admin.webinars.create_includes.accordions.new_interactive_file',['webinar' => $webinar])
    </div>
@endif


<div id="newTextLessonForm" class="d-none">
    @include('admin.webinars.create_includes.accordions.text-lesson',['webinar' => $webinar])
</div>

<div id="newQuizForm" class="d-none">
    @include('admin.webinars.create_includes.accordions.quiz',[
             'webinar' => $webinar,
             'quizInfo' => null,
             'webinarChapterPages' => true,
             'creator' => $webinar->creator
        ])
</div>

@if(getFeaturesSettings('webinar_assignment_status'))
    <div id="newAssignmentForm" class="d-none">
        @include('admin.webinars.create_includes.accordions.assignment',['webinar' => $webinar])
    </div>
@endif

@include('admin.webinars.create_includes.chapter_modal')

@include('admin.webinars.create_includes.change_chapter_modal')

@extends('web.default.layouts.app',['appFooter' => false, 'appHeader' => false])

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/learning_page/styles.css"/>
    <link rel="stylesheet" href="/assets/default/vendors/video/video-js.min.css">
@endpush

@section('content')

    <div class="learning-page">

        @include('web.default.course.learningPage.components.navbar')

        <div class="d-flex position-relative">
            <div class="learning-page-content flex-grow-1 bg-info-light p-15">
                @include('web.default.course.learningPage.components.content')
            </div>

            <div class="learning-page-tabs show">
                <ul class="nav nav-tabs py-15 d-flex align-items-center justify-content-around" id="tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="position-relative font-14 d-flex align-items-center active" id="content-tab"
                           data-toggle="tab" href="#content" role="tab" aria-controls="content"
                           aria-selected="true">
                            <i class="learning-page-tabs-icons mr-5">
                                @include('web.default.panel.includes.sidebar_icons.webinars')
                            </i>
                            <span class="learning-page-tabs-link-text">{{ trans('product.content') }}</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="position-relative font-14 d-flex align-items-center" id="quizzes-tab" data-toggle="tab"
                           href="#quizzes" role="tab" aria-controls="quizzes"
                           aria-selected="false">
                            <i class="learning-page-tabs-icons mr-5">
                                @include('web.default.panel.includes.sidebar_icons.quizzes')
                            </i>
                            <span class="learning-page-tabs-link-text">{{ trans('quiz.quizzes') }}</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="position-relative font-14 d-flex align-items-center" id="certificates-tab" data-toggle="tab"
                           href="#certificates" role="tab" aria-controls="certificates"
                           aria-selected="false">
                            <i class="learning-page-tabs-icons mr-5">
                                @include('web.default.panel.includes.sidebar_icons.certificate')
                            </i>
                            <span class="learning-page-tabs-link-text">{{ trans('panel.certificates') }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content h-100" id="nav-tabContent">
                    <div class="pb-20 tab-pane fade show active h-100" id="content" role="tabpanel"
                         aria-labelledby="content-tab">
                        @include('web.default.course.learningPage.components.content_tab.index')
                    </div>

                    <div class="pb-20 tab-pane fade  h-100" id="quizzes" role="tabpanel"
                         aria-labelledby="quizzes-tab">
                        @include('web.default.course.learningPage.components.quiz_tab.index')
                    </div>

                    <div class="pb-20 tab-pane fade  h-100" id="certificates" role="tabpanel"
                         aria-labelledby="certificates-tab">
                        @include('web.default.course.learningPage.components.certificate_tab.index')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/video/video.min.js"></script>
    <script src="/assets/default/vendors/video/youtube.min.js"></script>
    <script src="/assets/default/vendors/video/vimeo.js"></script>

    <script>
        var defaultItemType = '{{ !empty(request()->get('type')) ? request()->get('type') : (!empty($userLearningLastView) ? $userLearningLastView->item_type : '') }}'
        var defaultItemId = '{{ !empty(request()->get('item')) ? request()->get('item') : (!empty($userLearningLastView) ? $userLearningLastView->item_id : '') }}'
        var loadFirstContent = {{ (!empty($dontAllowLoadFirstContent) and $dontAllowLoadFirstContent) ? 'false' : 'true' }}; // allow to load first content when request item is empty

        var appUrl = '{{ url('') }}';
        var courseUrl = '{{ $course->getUrl() }}';
        var courseNotesStatus = '{{ !empty(getFeaturesSettings('course_notes_status')) }}';
        var courseNotesShowAttachment = '{{ !empty(getFeaturesSettings('course_notes_attachment')) }}';

        // lang
        var pleaseWaitForTheContentLang = '{{ trans('update.please_wait_for_the_content_to_load') }}';
        var downloadTheFileLang = '{{ trans('update.download_the_file') }}';
        var downloadLang = '{{ trans('home.download') }}';
        var showHtmlFileLang = '{{ trans('update.show_html_file') }}';
        var showLang = '{{ trans('update.show') }}';
        var sessionIsLiveLang = '{{ trans('update.session_is_live') }}';
        var youCanJoinTheLiveNowLang = '{{ trans('update.you_can_join_the_live_now') }}';
        var passwordLang = '{{ trans('auth.password') }}';
        var joinTheClassLang = '{{ trans('update.join_the_class') }}';
        var coursePageLang = '{{ trans('update.course_page') }}';
        var quizPageLang = '{{ trans('update.quiz_page') }}';
        var sessionIsNotStartedYetLang = '{{ trans('update.session_is_not_started_yet') }}';
        var thisSessionWillBeStartedOnLang = '{{ trans('update.this_session_will_be_started_on') }}';
        var sessionIsFinishedLang = '{{ trans('update.session_is_finished') }}';
        var sessionIsFinishedHintLang = '{{ trans('update.this_session_is_finished_You_cant_join_it') }}';
        var goToTheQuizPageForMoreInformationLang = '{{ trans('update.go_to_the_quiz_page_for_more_information') }}';
        var downloadCertificateLang = '{{ trans('update.download_certificate') }}';
        var enjoySharingYourCertificateWithOthersLang = '{{ trans('update.enjoy_sharing_your_certificate_with_others') }}';
        var attachmentsLang = '{{ trans('public.attachments') }}';
        var checkAgainLang = '{{ trans('update.check_again') }}';
        var learningToggleLangSuccess = '{{ trans('public.course_learning_change_status_success') }}';
        var learningToggleLangError = '{{ trans('public.course_learning_change_status_error') }}';
        var sequenceContentErrorModalTitle = '{{ trans('update.sequence_content_error_modal_title') }}';
        var sendAssignmentSuccessLang = '{{ trans('update.send_assignment_success') }}';
        var saveAssignmentRateSuccessLang = '{{ trans('update.save_assignment_grade_success') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var changesSavedSuccessfullyLang = '{{ trans('update.changes_saved_successfully') }}';
        var oopsLang = '{{ trans('update.oops') }}';
        var somethingWentWrongLang = '{{ trans('update.something_went_wrong') }}';
        var notAccessToastTitleLang = '{{ trans('public.not_access_toast_lang') }}';
        var notAccessToastMsgLang = '{{ trans('public.not_access_toast_msg_lang') }}';
        var cantStartQuizToastTitleLang = '{{ trans('public.request_failed') }}';
        var cantStartQuizToastMsgLang = '{{ trans('quiz.cant_start_quiz') }}';
        var learningPageEmptyContentTitleLang = '{{ trans('update.learning_page_empty_content_title') }}';
        var learningPageEmptyContentHintLang = '{{ trans('update.learning_page_empty_content_hint') }}';
        var expiredQuizLang = '{{ trans('update.expired_quiz') }}';
        var personalNoteLang = '{{ trans('update.personal_note') }}';
        var personalNoteHintLang = '{{ trans('update.this_note_will_be_displayed_for_you_privately') }}';
        var attachmentLang = '{{ trans('update.attachment') }}';
        var saveNoteLang = '{{ trans('update.save_note') }}';
        var clearNoteLang = '{{ trans('update.clear_note') }}';
        var personalNoteStoredSuccessfullyLang = '{{ trans('update.personal_note_stored_successfully') }}';
    </script>
    <script type="text/javascript" src="/assets/default/vendors/dropins/dropins.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

    <script src="/assets/default/js/parts/video_player_helpers.min.js"></script>
    <script src="/assets/learning_page/scripts.min.js"></script>

    @if((!empty($isForumPage) and $isForumPage) or (!empty($isForumAnswersPage) and $isForumAnswersPage))
        <script src="/assets/learning_page/forum.min.js"></script>
    @endif
@endpush

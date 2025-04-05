@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <div class="flex-grow-1">
                <h2 class="font-20 font-weight-bold">{{ $topic->title }}</h2>

                <span class="d-block font-14 font-weight-500 text-gray mt-1">{{ trans('public.by') }} <span class="font-weight-bold">{{ $topic->creator->full_name }}</span> {{ trans('public.in') }} {{ dateTimeFormat($topic->created_at, 'j M Y | H:i') }}</span>
            </div>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/forums">{{ trans('update.forums') }}</a></div>
                <div class="breadcrumb-item"><a href="{{ getAdminPanelUrl() }}/forums/{{ $topic->forum_id }}/topics">{{ trans('update.topics') }}</a></div>
                <div class="breadcrumb-item">{{ trans('site.posts') }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12 ">

                    <div class="card">
                        <div class="card-body">

                            @include('admin.forums.topics.post_card')

                            {{-- Topic Posts --}}
                            @if(!empty($topic->posts) and count($topic->posts))
                                @foreach($topic->posts as $postRow)
                                    @include('admin.forums.topics.post_card',['post' => $postRow])
                                @endforeach
                            @endif

                            <div class="mt-4">
                                <h3 class="font-16 font-weight-bold text-dark">{{ trans('update.reply_to_the_topic') }}</h3>

                                <div class="p-2 rounded-lg border bg-white mt-2">
                                    <form action="{{ getAdminPanelUrl() }}/forums/{{ $topic->forum_id }}/topics/{{ $topic->id }}/posts" method="post">
                                        {{ csrf_field() }}

                                        <div class="topic-posts-reply-card d-none position-relative px-2 py-2 rounded-sm bg-info-light mb-2">
                                            <input type="hidden" name="reply_post_id" class="js-reply-post-id">
                                            <div class="js-reply-post-title font-14 font-weight-500 text-gray">{!! trans('update.you_are_replying_to_the_message') !!}</div>
                                            <div class="js-reply-post-description mt-1 font-14 text-gray"></div>

                                            <button type="button" class="js-close-reply-post btn-transparent">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>


                                        <div class="form-group">
                                            <label class="input-label">{{ trans('public.description') }}</label>
                                            <textarea id="summernote" name="description" class="form-control"></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6">

                                                <div class="form-group">
                                                    <label class="input-label">{{ trans('update.attach_a_file') }} ({{ trans('public.optional') }})</label>

                                                    <div class="d-flex align-items-center">
                                                        <div class="input-group mr-2">
                                                            <div class="input-group-prepend">
                                                                <button type="button" class="input-group-text admin-file-manager" data-input="postAttachmentInput" data-preview="holder">
                                                                    <i class="fa fa-upload"></i>
                                                                </button>
                                                            </div>
                                                            <input type="text" name="attach" id="postAttachmentInput" value="" class="form-control" placeholder="{{ trans('update.assignment_attachments_placeholder') }}"/>
                                                        </div>

                                                        <button type="button" class="js-save-post btn btn-primary btn-sm">{{ trans('update.send') }}</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                            <div class="mt-4">
                                <h3 class="font-16 font-weight-bold">{{ trans('update.topic_actions') }}</h3>

                                <div class=" mt-2">
                                    <form action="{{ getAdminPanelUrl() }}/forums/{{ $topic->forum_id }}/topics/{{ $topic->id }}/closeToggle" method="post">
                                        {{ csrf_field() }}

                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group custom-switches-stacked">
                                                    <label class="custom-switch pl-0 d-flex align-items-center">
                                                        <input type="hidden" name="close" value="0">
                                                        <input type="checkbox" name="close" id="forumCloseSwitch" value="1" {{ (!empty($topic) and $topic->close) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                        <span class="custom-switch-indicator"></span>
                                                        <label class="custom-switch-description mb-0 cursor-pointer" for="forumCloseSwitch">{{ trans('admin/main.close') }}</label>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        var replyToTopicSuccessfullySubmittedLang = '{{ trans('update.reply_to_topic_successfully_submitted') }}'
        var reportSuccessfullySubmittedLang = '{{ trans('update.report_successfully_submitted') }}';
        var changesSavedSuccessfullyLang = '{{ trans('update.changes_saved_successfully') }}';
        var oopsLang = '{{ trans('update.oops') }}';
        var somethingWentWrongLang = '{{ trans('update.something_went_wrong') }}';
        var reportLang = '{{ trans('panel.report') }}';
        var descriptionLang = '{{ trans('public.description') }}';
        var editAttachmentLabelLang = '{{ trans('update.attach_a_file') }} ({{ trans('public.optional') }})';
        var sendLang = '{{ trans('update.send') }}';
        var notLoginToastTitleLang = '{{ trans('public.not_login_toast_lang') }}';
        var notLoginToastMsgLang = '{{ trans('public.not_login_toast_msg_lang') }}';
        var topicBookmarkedSuccessfullyLang = '{{ trans('update.topic_bookmarked_successfully') }}';
        var topicUnBookmarkedSuccessfullyLang = '{{ trans('update.topic_un_bookmarked_successfully') }}';
        var editPostLang = '{{ trans('update.edit_post') }}';
    </script>

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/js/parts/topic_posts.min.js"></script>

@endpush

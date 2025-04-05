@extends('web.default.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <div class="container">
        <section class="topics-title-section mt-30 mt-md-50 px-20 px-md-30 py-25 py-md-35 rounded-lg">
            <h1 class="font-30 font-weight-bold text-white">{{ !empty($topic) ? trans('update.edit_topic') : trans('update.new_topic') }}</h1>
            <p class="font-14 text-white">{{ trans('update.new_topic_hint') }}</p>

            <div class="mt-10">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb p-0 m-0">
                        <li class="breadcrumb-item font-12 text-white"><a href="/" class="text-white">{{ getGeneralSettings('site_name') }}</a></li>
                        <li class="breadcrumb-item font-12 text-white"><a href="/forums" class="text-white">{{ trans('update.forum') }}</a></li>
                        <li class="breadcrumb-item font-12 text-white font-weight-bold" aria-current="page">{{ !empty($topic) ? trans('update.edit_topic') : trans('update.new_topic') }}</li>
                    </ol>
                </nav>
            </div>
        </section>

        <form action="{{ !empty($topic) ? $topic->getEditUrl() : '/forums/create-topic' }}" method="post">
            {{ csrf_field() }}

            <div class="rounded-lg px-15 py-20 border bg-white mt-20">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ trans('update.topic_title') }}</label>
                            <input type="text" name="title" value="{{ !empty($topic) ? $topic->title : old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="{{ trans('update.topic_title_placeholder') }}">
                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.forums') }}</label>
                            <select name="forum_id" class="form-control @error('forum_id') is-invalid @enderror">
                                <option selected disabled>{{ trans('admin/main.choose_category') }}</option>

                                @foreach($forums as $forum)
                                    @if(!empty($forum->subForums) and count($forum->subForums))
                                        @php
                                            $showOptgroup = false;

                                            foreach($forum->subForums as $subForum) {
                                                if($subForum->checkUserCanCreateTopic() and !$subForum->close) {
                                                    $showOptgroup = true;
                                                }
                                            }
                                        @endphp

                                        @if($showOptgroup)
                                            <optgroup label="{{ $forum->title }}">
                                                @foreach($forum->subForums as $subForum)
                                                    @if($subForum->checkUserCanCreateTopic() and !$subForum->close)
                                                        <option value="{{ $subForum->id }}" {{ ((!empty($topic) and $topic->forum_id == $subForum->id) or (request()->get('forum_id') == $subForum->id)) ? 'selected' : '' }}>{{ $subForum->title }}</option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @elseif($forum->checkUserCanCreateTopic() and !$forum->close)
                                        <option value="{{ $forum->id }}" {{ (request()->get('forum_id') == $forum->id) ? 'selected' : '' }}>{{ $forum->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('forum_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.description') }}</label>
                            <textarea id="summernote" name="description" class="form-control @error('description')  is-invalid @enderror">{!! !empty($topic) ? $topic->description : old('description') !!}</textarea>
                            @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div id="topicImagesInputs" class="create-topic-attachments form-group mt-15">
                            <label class="input-label mb-0">{{ trans('update.attachments') }}</label>

                            <div class="main-row input-group product-images-input-group mt-10">
                                <div class="input-group-prepend">
                                    <button type="button" class="input-group-text panel-file-manager" data-input="attachments_record" data-preview="holder">
                                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                    </button>
                                </div>
                                <input type="text" name="attachments[]" id="attachments_record" value="" class="form-control"/>

                                <button type="button" class="btn btn-primary btn-sm add-btn">
                                    <i data-feather="plus" width="18" height="18" class="text-white"></i>
                                </button>
                            </div>

                            @if(!empty($topic) and !empty($topic->attachments) and count($topic->attachments))
                                @foreach($topic->attachments as $topicAttachment)
                                    <div class="input-group product-images-input-group mt-10">
                                        <div class="input-group-prepend">
                                            <button type="button" class="input-group-text panel-file-manager" data-input="attachments_{{ $topicAttachment->id }}" data-preview="holder">
                                                <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="attachments[]" id="attachments_{{ $topicAttachment->id }}" value="{{ $topicAttachment->path }}" class="form-control" placeholder="{{ trans('update.attachments_size') }}"/>

                                        <button type="button" class="btn btn-sm btn-danger remove-btn">
                                            <i data-feather="x" width="18" height="18" class="text-white"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif

                            @error('images')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-15 p-10 bg-info-light rounded-lg d-flex align-items-center justify-content-between">
                <div class="py-5">
                    <div class="font-14 font-weight-bold text-gray">{{ trans('update.terms_and_rules_confirmation') }}</div>
                    <p class="d-block font-14 text-gray mt-5">{{ trans('update.terms_and_rules_confirmation_hint') }}</p>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i data-feather="file" class="text-white" width="16" height="16"></i>
                    <span class="ml-1">{{ trans('update.publish_topic') }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/js/parts/create_topics.min.js"></script>
@endpush

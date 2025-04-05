@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.'.(!empty($post) ? 'edit_blog' : 'create_blog')) }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.blog') }}</div>
            </div>
        </div>

        <div class="section-body ">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/blog/{{ (!empty($post) ? $post->id.'/update' : 'store') }}" method="post">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-12 col-md-6">

                                        @if(!empty(getGeneralSettings('content_translate')) and !empty($userLanguages))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                <select name="locale" class="form-control {{ !empty($post) ? 'js-edit-content-locale' : '' }}">
                                                    @foreach($userLanguages as $lang => $language)
                                                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                                                    @endforeach
                                                </select>
                                                @error('locale')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                                        @endif

                                        <div class="form-group mt-15 ">
                                            <label class="input-label d-block">{{ trans('update.author') }}</label>

                                            <select name="author_id" class="form-control search-user-select2"
                                                    data-placeholder="{{ trans('update.select_a_user') }}"
                                                    data-search-option="except_user"
                                            >
                                                @if(!empty($post))
                                                    <option value="{{ $post->author->id }}" selected>{{ $post->author->full_name }}</option>
                                                @else
                                                    <option selected disabled>{{ trans('update.select_a_user') }}</option>
                                                @endif
                                            </select>

                                            @error('teacher_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="title"
                                                   class="form-control  @error('title') is-invalid @enderror"
                                                   value="{{ !empty($post) ? $post->title : old('title') }}"
                                                   placeholder="{{ trans('admin/main.choose_title') }}"/>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('/admin/main.category') }}</label>
                                            <select class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                                <option {{ !empty($trend) ? '' : 'selected' }} disabled>{{ trans('admin/main.choose_category') }}</option>

                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ (((!empty($post) and $post->category_id == $category->id) or (old('category_id') == $category->id)) ? 'selected="selected"' : '') }}>{{ $category->title }}</option>
                                                @endforeach
                                            </select>

                                            @error('category_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        {{-- Product Badges --}}
                                        @if(!empty($post))
                                            @include('admin.product_badges.content_include', ['itemTarget' => $post])
                                        @endif

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('public.cover_image') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                                                        <i class="fa fa-chevron-up"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="image" id="image" value="{{ (!empty($post)) ? $post->image : old('image') }}" class="form-control @error('image') is-invalid @enderror" placeholder="{{ trans('update.blog_cover_image_placeholder') }}"/>
                                                @error('image')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('public.description') }}</label>
                                    <div class="text-muted text-small mb-3">{{ trans('admin/main.create_blog_description_hint') }}</div>
                                    <textarea id="summernote" name="description" class="summernote form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('admin/main.description_placeholder') }}">{!! !empty($post) ? $post->description : old('description')  !!}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.content') }}</label>
                                    <div class="text-muted text-small mb-3">{{ trans('admin/main.create_blog_content_hint') }}</div>
                                    <textarea id="contentSummernote" name="content" class="summernote form-control @error('content')  is-invalid @enderror" placeholder="{{ trans('admin/main.content_placeholder') }}">{!! !empty($post) ? $post->content : old('content')  !!}</textarea>
                                    @error('content')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/pages/blog.meta_description') }}</label>
                                    <textarea name="meta_description" rows="5" class="form-control @error('meta_description')  is-invalid @enderror" placeholder="{{ trans('admin/pages/blog.meta_description_placeholder') }}">{!! !empty($post) ? $post->meta_description : old('meta_description')  !!}</textarea>
                                    @error('meta_description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group mt-30  d-flex align-items-center cursor-pointer">
                                    <div class="custom-control custom-switch align-items-start">
                                        <input type="checkbox" name="enable_comment" class="custom-control-input" id="commentsSwitch" {{ (!empty($post) and !$post->enable_comment) ? '' : 'checked' }}>
                                        <label class="custom-control-label" for="commentsSwitch"></label>
                                    </div>
                                    <label for="commentsSwitch" class="mb-0">{{ trans('admin/main.comments_section') }}</label>
                                </div>

                                <div class="form-group mt-30 d-flex align-items-center cursor-pointer">
                                    <div class="custom-control custom-switch align-items-start">
                                        <input type="checkbox" name="status" class="custom-control-input" id="statusSwitch" {{ (!empty($post) and $post->status == 'pending') ? '' : 'checked' }}>
                                        <label class="custom-control-label" for="statusSwitch"></label>
                                    </div>
                                    <label for="statusSwitch" class="mb-0">{{ trans('admin/main.publish') }}</label>
                                </div>

                                <button type="submit" class="btn btn-primary mt-1">{{ trans('admin/main.save_change') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush

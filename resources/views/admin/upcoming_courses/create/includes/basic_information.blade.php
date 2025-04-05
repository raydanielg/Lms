<div class="row">
    <div class="col-12 col-md-5">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control {{ !empty($upcomingCourse) ? 'js-edit-content-locale' : '' }}">
                    @foreach($userLanguages as $lang => $language)
                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }} {{ (!empty($definedLanguage) and is_array($definedLanguage) and in_array(mb_strtolower($lang), $definedLanguage)) ? '('. trans('panel.content_defined') .')' : '' }}</option>
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
            <label class="input-label d-block">{{ trans('panel.course_type') }}</label>

            <select name="type" class="custom-select @error('type')  is-invalid @enderror">
                <option value="webinar" @if((!empty($upcomingCourse) and $upcomingCourse->type == 'webinar') or old('type') == \App\Models\Webinar::$webinar) selected @endif>{{ trans('webinars.webinar') }}</option>
                <option value="course" @if((!empty($upcomingCourse) and $upcomingCourse->type == 'course') or old('type') == \App\Models\Webinar::$course) selected @endif>{{ trans('product.video_course') }}</option>
                <option value="text_lesson" @if((!empty($upcomingCourse) and $upcomingCourse->type == 'text_lesson') or old('type') == \App\Models\Webinar::$textLesson) selected @endif>{{ trans('product.text_course') }}</option>
            </select>

            @error('type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" value="{{ !empty($upcomingCourse) ? $upcomingCourse->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
            @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('admin/main.class_url') }}</label>
            <input type="text" name="slug" value="{{ !empty($upcomingCourse) ? $upcomingCourse->slug : old('slug') }}" class="form-control @error('slug')  is-invalid @enderror" placeholder=""/>
            <div class="text-muted text-small mt-1">{{ trans('admin/main.class_url_hint') }}</div>
            @error('slug')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        @if(!empty($upcomingCourse) and $upcomingCourse->creator->isOrganization())
            <div class="form-group mt-15 ">
                <label class="input-label d-block">{{ trans('admin/main.organization') }}</label>

                <select name="organ_id" data-search-option="just_organization_role" class="form-control search-user-select2" data-placeholder="{{ trans('search_organization') }}">
                    <option value="{{ $upcomingCourse->creator->id }}" selected>{{ $upcomingCourse->creator->full_name }}</option>
                </select>
            </div>
        @endif


        <div class="form-group mt-15 ">
            <label class="input-label d-block">{{ trans('admin/main.select_a_instructor') }}</label>

            <select name="teacher_id" data-search-option="except_user" class="form-control search-user-select22"
                    data-placeholder="{{ trans('public.select_a_teacher') }}"
            >
                @if(!empty($upcomingCourse))
                    <option value="{{ $upcomingCourse->teacher->id }}" selected>{{ $upcomingCourse->teacher->full_name }}</option>
                @else
                    <option selected disabled>{{ trans('public.select_a_teacher') }}</option>
                @endif
            </select>

            @error('teacher_id')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.seo_description') }}</label>
            <input type="text" name="seo_description" value="{{ !empty($upcomingCourse) ? $upcomingCourse->seo_description : old('seo_description') }}" class="form-control @error('seo_description')  is-invalid @enderror"/>
            <div class="text-muted text-small mt-1">{{ trans('admin/main.seo_description_hint') }}</div>
            @error('seo_description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="thumbnail" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="thumbnail" id="thumbnail" value="{{ !empty($upcomingCourse) ? $upcomingCourse->thumbnail : old('thumbnail') }}" class="form-control @error('thumbnail')  is-invalid @enderror"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="thumbnail">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('thumbnail')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>


        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.cover_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="cover_image" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="image_cover" id="cover_image" value="{{ !empty($upcomingCourse) ? $upcomingCourse->image_cover : old('image_cover') }}" class="form-control @error('image_cover')  is-invalid @enderror"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="cover_image">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('image_cover')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group mt-25">
            <label class="input-label">{{ trans('public.demo_video') }} ({{ trans('public.optional') }})</label>


            <div class="">
                <label class="input-label font-12">{{ trans('public.source') }}</label>
                <select name="video_demo_source"
                        class="js-video-demo-source form-control"
                >
                    @foreach(\App\Models\Webinar::$videoDemoSource as $source)
                        <option value="{{ $source }}" @if(!empty($upcomingCourse) and $upcomingCourse->video_demo_source == $source) selected @endif>{{ trans('update.file_source_'.$source) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="js-video-demo-other-inputs form-group mt-0 {{ (empty($upcomingCourse) or $upcomingCourse->video_demo_source != 'secure_host') ? '' : 'd-none' }}">
            <label class="input-label font-12">{{ trans('update.path') }}</label>
            <div class="input-group js-video-demo-path-input">
                <div class="input-group-prepend">
                    <button type="button" class="js-video-demo-path-upload input-group-text admin-file-manager {{ (empty($upcomingCourse) or empty($upcomingCourse->video_demo_source) or $upcomingCourse->video_demo_source == 'upload') ? '' : 'd-none' }}" data-input="demo_video" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>

                    <button type="button" class="js-video-demo-path-links rounded-left input-group-text input-group-text-rounded-left  {{ (empty($upcomingCourse) or empty($upcomingCourse->video_demo_source) or $upcomingCourse->video_demo_source == 'upload') ? 'd-none' : '' }}">
                        <i class="fa fa-link"></i>
                    </button>
                </div>
                <input type="text" name="video_demo" id="demo_video" value="{{ !empty($upcomingCourse) ? $upcomingCourse->video_demo : old('video_demo') }}" class="form-control @error('video_demo')  is-invalid @enderror"/>
                @error('video_demo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div class="form-group js-video-demo-secure-host-input {{ (!empty($upcomingCourse) and $upcomingCourse->video_demo_source == 'secure_host') ? '' : 'd-none' }}">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <div class="custom-file js-ajax-s3_file">
                    <input type="file" name="video_demo_secure_host_file" class="custom-file-input cursor-pointer" id="video_demo_secure_host_file" accept="video/*">
                    <label class="custom-file-label cursor-pointer" for="video_demo_secure_host_file">{{ trans('update.choose_file') }}</label>
                </div>

                <div class="invalid-feedback"></div>
            </div>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea id="summernote" name="description" class="form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('forms.webinar_description_placeholder') }}">{!! !empty($upcomingCourse) ? $upcomingCourse->description : old('description')  !!}</textarea>
            @error('description')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>

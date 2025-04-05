<!-- Modal -->
<div class="d-none" id="webinarTestLessonModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('public.add_text_lesson') }}</h3>
    <form action="{{ getAdminPanelUrl() }}/test-lesson/store" method="post">
        <input type="hidden" name="webinar_id" value="{{  !empty($webinar) ? $webinar->id :''  }}">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control ">
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


        <div class="form-group">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" class="form-control" placeholder=""/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.chapter') }}</label>
            <select class="custom-select" name="chapter_id">
                <option value="">{{ trans('admin/main.no_chapter') }}</option>

                @if(!empty($chapters))
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}">{{ $chapter->title }}</option>
                    @endforeach
                @endif
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.study_time') }}</label>
                    <input type="text" name="study_time" class="form-control"/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.accessibility') }}</label>
                    <select class="custom-select" name="accessibility">
                        <option selected disabled>{{ trans('public.choose_accessibility') }}</option>
                        <option value="free">{{ trans('public.free') }}</option>
                        <option value="paid">{{ trans('public.paid') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="image_record" data-preview="holder">
                        <i class="fa fa-arrow-up"></i>
                    </button>
                </div>
                <input type="text" name="image" id="image_record" class="form-control"/>
                <div class="input-group-append">
                    <button type="button" class="input-group-text admin-file-view" data-input="image_record">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label d-block">{{ trans('public.attachments') }}</label>

            <select class="js-ajax-attachments form-control attachments-select2" name="attachments" data-placeholder="{{ trans('public.choose_attachments') }}">
                <option></option>

                @if(!empty($webinar->files) and count($webinar->files))
                    @foreach($webinar->files as $filesInfo)
                        @if($filesInfo->downloadable)
                            <option value="{{ $filesInfo->id }}">{{ $filesInfo->title }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.summary') }}</label>
            <textarea name="summary" class="js-ajax-summary form-control" rows="6"></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.content') }}</label>
            <div class="content-summernote js-ajax-file_path">
                <textarea class="js-content-summernote form-control"></textarea>
                <textarea name="content" class="js-hidden-content-summernote d-none"></textarea>
            </div>
            <div class="invalid-feedback"></div>
        </div>

        <div class="js-textLesson-status form-group mt-3">
            <div class="d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label" for="textLessonStatusSwitch_record">{{ trans('admin/main.active') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="status" checked class="custom-control-input" id="textLessonStatusSwitch_record">
                    <label class="custom-control-label" for="textLessonStatusSwitch_record"></label>
                </div>
            </div>
        </div>

        @if(getFeaturesSettings('sequence_content_status'))
            <div class="form-group mb-1">
                <div class="d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="SequenceContentSwitch_record">{{ trans('update.sequence_content') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="sequence_content" class="js-sequence-content-switch custom-control-input" id="SequenceContentSwitch_record">
                        <label class="custom-control-label" for="SequenceContentSwitch_record"></label>
                    </div>
                </div>
            </div>

            <div class="js-sequence-content-inputs pl-2 d-none">
                <div class="form-group mb-1">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="checkPreviousPartsSwitch_record">{{ trans('update.check_previous_parts') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" checked name="check_previous_parts" class="custom-control-input" id="checkPreviousPartsSwitch_record">
                            <label class="custom-control-label" for="checkPreviousPartsSwitch_record"></label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('update.access_after_day') }}</label>
                    <input type="number" name="access_after_day" value="" class="js-ajax-access_after_day form-control" placeholder="{{ trans('update.access_after_day_placeholder') }}"/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        @endif

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" id="saveTestLesson" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>

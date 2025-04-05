<!-- Modal -->
<div class="d-none" id="interactiveFileModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('update.new_interactive_file') }}</h3>
    <form action="{{ getAdminPanelUrl() }}/files/store" method="post" enctype="multipart/form-data">
        <input type="hidden" name="webinar_id" value="{{  !empty($webinar) ? $webinar->id :''  }}">
        <input type="hidden" name="storage" value="upload_archive" class="">
        <input type="hidden" name="file_type" value="archive" class="">

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
            <input type="text" name="title" class="form-control" placeholder="{{ trans('forms.maximum_255_characters') }}"/>
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
                    <label class="input-label">{{ trans('update.interactive_type') }}</label>
                    <select name="interactive_type" class="js-interactive-type form-control">
                        <option value="adobe_captivate">{{ trans('update.adobe_captivate') }}</option>
                        <option value="i_spring">{{ trans('update.i_spring') }}</option>
                        <option value="custom">{{ trans('update.custom') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group">
                    <label class="input-label">{{ trans('public.accessibility') }}</label>
                    <select class="custom-select" name="accessibility" required>
                        <option selected disabled>{{ trans('public.choose_accessibility') }}</option>
                        <option value="free">{{ trans('public.free') }}</option>
                        <option value="paid">{{ trans('public.paid') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>

        <div class="js-interactive-file-name-input form-group d-none">
            <label class="input-label">{{ trans('update.interactive_file_name') }}</label>
            <input type="text" name="interactive_file_name" class="js-ajax-interactive_file_name form-control" value="" placeholder="{{ trans('update.interactive_file_name_placeholder') }}"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group js-file-path-input">
            <div class="local-input input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="file_path_record" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="file_path" id="file_path_record" value="" class="js-ajax-file_path form-control" placeholder="{{ trans('update.choose_zip_file') }}"/>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea name="description" class="js-ajax-description form-control" rows="6"></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group mt-20">
            <div class="d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label" for="fileStatusSwitch_record">{{ trans('public.active') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="status" class="custom-control-input" id="fileStatusSwitch_record">
                    <label class="custom-control-label" for="fileStatusSwitch_record"></label>
                </div>
            </div>
        </div>

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

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" id="saveInteractiveFile" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>

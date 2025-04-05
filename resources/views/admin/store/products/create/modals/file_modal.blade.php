<div class="d-none" id="productFileModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('public.add_file') }}</h3>
    <form action="{{ getAdminPanelUrl() }}/store/products/files/store" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="{{  !empty($product) ? $product->id :''  }}">

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

        <div class="form-group js-file-path-input">
            <div class="local-input input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager" data-input="file_path_record" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="path" id="file_path_record" value="" class="js-file_path form-control" placeholder="{{ trans('webinars.file_upload_placeholder') }}"/>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="row form-group js-file-type-volume">
            <div class="col-6">
                <label class="input-label">{{ trans('webinars.file_type') }}</label>
                <select name="file_type" class="js-ajax-file_type form-control">
                    <option value="">{{ trans('webinars.select_file_type') }}</option>

                    @foreach(\App\Models\File::$fileTypes as $fileType)
                        <option value="{{ $fileType }}" >{{ trans('update.file_type_'.$fileType) }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-6">
                <label class="input-label">{{ trans('webinars.file_volume') }}</label>
                <input type="text" name="volume" value="" class="js-ajax-volume form-control" placeholder="{{ trans('webinars.online_file_volume') }}"/>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea name="description" rows="4" class="js-description form-control" placeholder="{{ trans('public.description') }}"></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <div class="js-online_viewer-input form-group mt-20 d-none">
            <div class="d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label" for="online_viewerSwitch_record">{{ trans('update.online_viewer') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="online_viewer" class="custom-control-input" id="online_viewerSwitch_record" {{ (!empty($file) and $file->online_viewer) ? 'checked' : ''  }}>
                    <label class="custom-control-label" for="online_viewerSwitch_record"></label>
                </div>
            </div>
        </div>

        <div class="form-group mt-20">
            <div class="d-flex align-items-center justify-content-between">
                <label class="cursor-pointer input-label mr-2" for="fileStatusSwitch_record">{{ trans('public.active') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="status" class="custom-control-input" id="fileStatusSwitch_record">
                    <label class="custom-control-label" for="fileStatusSwitch_record"></label>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex align-items-center justify-content-end">
            <button type="button" id="saveFile" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-12 col-md-8">

        <div class="mb-20">
            <div class="form-group mt-30 mb-0 d-flex align-items-center">
                <label class="" for="verificationSwitch">{{ trans('update.verification') }}</label>
                <div class="custom-control custom-switch ml-3">
                    <input type="checkbox" name="verification" class="custom-control-input" id="verificationSwitch" {{ (!empty($installment) && $installment->verification) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="verificationSwitch"></label>
                </div>
            </div>
            <p class="text-muted font-12">{{ trans('update.installment_verification_hint') }}</p>
        </div>

        <div class="js-verification-fields {{ (!empty($installment) && $installment->verification) ? '' : 'd-none' }}">

            <div class="form-group ">
                <label class="control-label">{{ trans('update.verification_description') }}</label>
                <textarea name="verification_description" class="summernote form-control text-left  @error('verification_description') is-invalid @enderror" data-height="180">{{ (!empty($installment)) ? $installment->verification_description :'' }}</textarea>
                <div class="text-muted text-small mt-1">{{ trans('update.installment_verification_description_hint') }}</div>
                <div class="invalid-feedback">@error('verification_description') {{ $message }} @enderror</div>
            </div>
            
            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.verification_banner') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="verification_banner" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="verification_banner" id="verification_banner" value="{{ !empty($installment) ? $installment->verification_banner : old('verification_banner') }}" class="form-control @error('verification_banner')  is-invalid @enderror"/>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text admin-file-view" data-input="verification_banner">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    
                    @error('verification_banner')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <p class="text-muted font-12">{{ trans('update.installment_verification_banner_hint') }}</p>
                </div>
            </div>

            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.verification_video') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="verification_video" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="verification_video" id="verification_video" value="{{ !empty($installment) ? $installment->verification_video : old('verification_video') }}" class="form-control @error('verification_video')  is-invalid @enderror"/>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text admin-file-view" data-input="verification_video">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    
                    @error('verification_video')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <p class="text-muted font-12">{{ trans('update.installment_verification_video_hint') }}</p>
                </div>
            </div>

        </div>

        <div class="mb-20">
            <div class="form-group mt-30 mb-0 d-flex align-items-center">
                <label class="" for="request_uploadsSwitch">{{ trans('update.request_uploads') }}</label>
                <div class="custom-control custom-switch ml-3">
                    <input type="checkbox" name="request_uploads" class="custom-control-input" id="request_uploadsSwitch" {{ (!empty($installment) && $installment->request_uploads) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="request_uploadsSwitch"></label>
                </div>
            </div>
            <p class="text-muted font-12">{{ trans('update.installment_request_uploads_hint') }}</p>
        </div>

        <div class="mb-20">
            <div class="form-group mt-30 mb-0 d-flex align-items-center">
                <label class="" for="bypassSwitch">{{ trans('update.bypass_verification_for_verified_users') }}</label>
                <div class="custom-control custom-switch ml-3">
                    <input type="checkbox" name="bypass_verification_for_verified_users" class="custom-control-input" id="bypassSwitch" {{ (!empty($installment) && $installment->bypass_verification_for_verified_users) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="bypassSwitch"></label>
                </div>
            </div>
            <p class="text-muted font-12">{{ trans('update.installment_bypass_verification_for_verified_users_hint') }}</p>
        </div>

    </div>
</div>

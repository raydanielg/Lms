<div class="form-group">
    <label class="input-label font-weight-500">{{ trans('site.captcha') }}</label>
    <div class="row align-items-center">
        <div class="col">
            <input type="text" name="captcha" class="form-control @error('captcha')  is-invalid @enderror">
            @error('captcha')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="col d-flex align-items-center">
            <img id="captchaImageComment" class="captcha-image" src="">

            <button type="button" id="refreshCaptcha" class="btn-transparent ml-2">
                <i class="fas fa-sync"></i>
            </button>
        </div>
    </div>
</div>

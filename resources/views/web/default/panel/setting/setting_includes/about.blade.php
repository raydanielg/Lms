<section>
    <h3 class="section-title after-line mt-35">{{ trans('site.about') }}</h3>

    <div class="row mt-20">
        <div class="col-12 col-lg-6">
            <div class="form-group">
                <label class="input-label">{{ trans('panel.bio') }}</label>
                <textarea name="about" rows="9" class="form-control @error('about')  is-invalid @enderror">{!! (!empty($user) and empty($new_user)) ? $user->about : old('about')  !!}</textarea>
                @error('about')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('panel.job_title') }}</label>
                <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror">{{ $user->bio }}</textarea>
                @error('bio')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror

                <div class="mt-15">
                     <p class="font-12 text-gray">- {{ trans('panel.bio_hint_1') }}</p>
                     <p class="font-12 text-gray">- {{ trans('panel.bio_hint_2') }}</p>
                </div>

            </div>
        </div>
    </div>
</section>

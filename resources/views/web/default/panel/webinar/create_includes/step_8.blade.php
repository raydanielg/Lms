@push('styles_top')

@endpush


<section class="mt-20">
    <h2 class="section-title after-line">{{ trans('public.message_to_reviewer') }}</h2>
    <div class="row">
        <div class="col-12">
            <div class="form-group mt-15">
                <textarea name="message_for_reviewer" rows="10" class="form-control">{{ (!empty($webinar) and $webinar->message_for_reviewer) ? $webinar->message_for_reviewer : old('message_for_reviewer') }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="form-group mt-10">
                <div class="d-flex align-items-center justify-content-between">
                    <label class="cursor-pointer input-label" for="rulesSwitch">{{ trans('public.agree_rules') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="rules" class="custom-control-input " id="rulesSwitch">
                        <label class="custom-control-label" for="rulesSwitch"></label>
                    </div>
                </div>

                @error('rules')
                <div class="text-danger mt-10">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>
</section>

@push('scripts_bottom')

@endpush

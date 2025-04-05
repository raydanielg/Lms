@php
    $termsSetting = $settings->where('name', \App\Models\Setting::$installmentsTermsSettingsName)->first();

    $termsValue = (!empty($termsSetting) and !empty($termsSetting->translate($selectedLocale))) ? $termsSetting->translate($selectedLocale)->value : null;

    if (!empty($termsValue)) {
        $termsValue = json_decode($termsValue, true);
    }
@endphp


<form action="{{ getAdminPanelUrl('/financial/installments/settings') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="page" value="general">
    <input type="hidden" name="name" value="{{ \App\Models\Setting::$installmentsTermsSettingsName }}">

    <div class="row">
        <div class="col-12 col-md-6">
            @if(!empty(getGeneralSettings('content_translate')))
                <div class="form-group">
                    <label class="input-label">{{ trans('auth.language') }}</label>
                    <select name="locale" class="form-control js-edit-content-locale">
                        @foreach($userLanguages as $lang => $language)
                            <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', (!empty($termsValue) and !empty($termsValue['locale'])) ? $termsValue['locale'] : app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group ">
                <label class="control-label">{{ trans('admin/main.description') }}</label>
                <textarea name="value[terms_description]" required class="summernote form-control text-left">{{ (!empty($termsValue) and !empty($termsValue['terms_description'])) ? $termsValue['terms_description'] : '' }}</textarea>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-1">{{ trans('admin/main.submit') }}</button>
</form>

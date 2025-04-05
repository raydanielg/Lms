@php
    $termsSetting = $settings->where('name', \App\Models\Setting::$registrationBonusTermsSettingsName)->first();

    $termsValue = (!empty($termsSetting) and !empty($termsSetting->translate($selectedLocale))) ? $termsSetting->translate($selectedLocale)->value : null;

    if (!empty($termsValue)) {
        $termsValue = json_decode($termsValue, true);
    }
@endphp


<form action="{{ getAdminPanelUrl('/registration_bonus/settings') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="page" value="general">
    <input type="hidden" name="name" value="{{ \App\Models\Setting::$registrationBonusTermsSettingsName }}">

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


            <div class="form-group mt-15">
                <label class="input-label">{{ trans('public.image') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="term_image" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="value[term_image]" id="term_image" value="{{ (!empty($termsValue) and !empty($termsValue['term_image'])) ? $termsValue['term_image'] : old('term_image') }}" class="form-control @error('image')  is-invalid @enderror"/>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text admin-file-view" data-input="term_image">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="addAccountTypes">

                <button type="button" class="btn btn-success add-btn mb-4 fa fa-plus"></button>

                @if(!empty($termsValue) and !empty($termsValue['items']))

                    @if(!empty($termsValue) and is_array($termsValue['items']))
                        @foreach($termsValue['items'] as $key => $item)
                            <div class="form-group d-flex align-items-start">
                                <div class="px-2 py-1 border flex-grow-1">

                                    <div class="form-group mb-1">
                                        <label class="mb-1">{{ trans('admin/main.icon') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button type="button" class="input-group-text admin-file-manager" data-input="icon_record" data-preview="holder">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                            </div>
                                            <input type="text" name="value[items][{{ $key }}][icon]" id="icon_{{ $key }}" value="{{ $item['icon'] ?? '' }}" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="form-group mb-1">
                                        <label class="mb-1">{{ trans('admin/main.title') }}</label>
                                        <input type="text" name="value[items][{{ $key }}][title]"
                                               class="form-control"
                                               value="{{ $item['title'] ?? '' }}"
                                        />
                                    </div>

                                    <div class="form-group mb-1">
                                        <label class="mb-1">{{ trans('public.description') }}</label>
                                        <input type="text" name="value[items][{{ $key }}][description]"
                                               class="form-control"
                                               value="{{ $item['description'] ?? '' }}"
                                        />
                                    </div>
                                </div>
                                <button type="button" class="fas fa-times btn ml-2 remove-btn btn-danger"></button>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>

        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-1">{{ trans('admin/main.submit') }}</button>
</form>

<div class="main-row d-none">
    <div class="form-group d-flex align-items-start">
        <div class="px-2 py-1 border flex-grow-1">

            <div class="form-group mb-1">
                <label class="mb-1">{{ trans('admin/main.icon') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="icon_record" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="value[items][record][icon]" id="icon_record" value="" class="form-control"/>
                </div>
            </div>

            <div class="form-group mb-1">
                <label class="mb-1">{{ trans('admin/main.title') }}</label>
                <input type="text" name="value[items][record][title]"
                       class="form-control"
                />
            </div>

            <div class="form-group mb-1">
                <label class="mb-1">{{ trans('public.description') }}</label>
                <input type="text" name="value[items][record][description]"
                       class="form-control"
                />
            </div>
        </div>
        <button type="button" class="fas fa-times btn ml-2 remove-btn btn-danger d-none"></button>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/default/js/admin/settings/site_bank_accounts.min.js"></script>
@endpush

@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }

@endphp

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

<div class="mt-3" id="cookie_settings">

    <form action="{{ getAdminPanelUrl() }}/settings/cookie_settings" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="personalization">
        <input type="hidden" name="cookie_settings" value="cookie_settings">


        <h5 class="mb-3">{{ trans('update.cookie_settings_modal') }}</h5>
        <div class="row">
            <div class="col-12 col-md-6">
                @if(!empty(getGeneralSettings('content_translate')))
                    <div class="form-group">
                        <label class="input-label">{{ trans('auth.language') }}</label>
                        <select name="locale" class="form-control js-edit-content-locale">
                            @foreach($userLanguages as $lang => $language)
                                <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', (!empty($itemValue) and !empty($itemValue['locale'])) ? $itemValue['locale'] : app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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

            <div class="col-12">
                <div class="form-group ">
                    <label class="control-label">{{ trans('admin/main.message') }}</label>
                    <textarea name="value[cookie_settings_modal_message]" class="summernote form-control text-left">{{ (!empty($itemValue) and !empty($itemValue['cookie_settings_modal_message'])) ? $itemValue['cookie_settings_modal_message'] : '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div id="cookie_settings_modal_items" class="ml-0">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <strong class="d-block">{{ trans('admin/main.items') }}</strong>

                        <button type="button" data-parent="cookie_settings_modal_items" data-main-row="cookieSettingsItemsMainRow" class="btn btn-success add-btn"><i class="fa fa-plus"></i> {{ trans('admin/main.add') }}</button>
                    </div>


                    @if(!empty($itemValue) and !empty($itemValue['cookie_settings_modal_items']))
                        @foreach($itemValue['cookie_settings_modal_items'] as $modalItemKey => $modalItemValue)
                            <div class="form-group list-group p-2 border rounded-lg">
                                <div class="input-group">

                                    <input type="text" name="value[cookie_settings_modal_items][{{ $modalItemKey }}][title]"
                                           class="form-control w-auto flex-grow-1"
                                           placeholder="{{ trans('admin/main.choose_title') }}"
                                           value="{{ $modalItemValue['title'] ?? '' }}"
                                    />

                                    <div class="input-group-append">
                                        <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>

                                <textarea name="value[cookie_settings_modal_items][{{ $modalItemKey }}][description]"
                                          class="form-control w-100 flex-grow-1 mt-1" rows="4"
                                          placeholder="{{ trans('admin/main.description') }}"
                                >{{ $modalItemValue['description'] ?? '' }}</textarea>

                                <div class="form-group mb-0 mt-1 custom-switches-stacked">
                                    <label class="custom-switch pl-0">
                                        <input type="hidden" name="value[cookie_settings_modal_items][{{ $modalItemKey }}][required]" value="0">
                                        <input type="checkbox" name="value[cookie_settings_modal_items][{{ $modalItemKey }}][required]" id="requiredSwitch_{{ $modalItemKey }}" value="1" {{ (!empty($modalItemValue['required']) and $modalItemValue['required']) ? 'checked' : '' }} class="custom-switch-input"/>
                                        <span class="custom-switch-indicator"></span>
                                        <label class="custom-switch-description mb-0 cursor-pointer" for="requiredSwitch_{{ $modalItemKey }}">{{ trans('public.required') }}</label>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>

<div id="cookieSettingsItemsMainRow" class="form-group p-2 border rounded-lg d-none">
    <div class="input-group">

        <input type="text" name="value[cookie_settings_modal_items][record][title]"
               class="form-control w-auto flex-grow-1" required
               placeholder="{{ trans('admin/main.choose_title') }}"/>

        <div class="input-group-append">
            <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <textarea name="value[cookie_settings_modal_items][record][description]" required
              class="form-control w-100 flex-grow-1 mt-1" rows="4" placeholder="{{ trans('admin/main.description') }}"></textarea>

    <div class="form-group mb-0 mt-1 custom-switches-stacked">
        <label class="custom-switch pl-0">
            <input type="hidden" name="value[cookie_settings_modal_items][record][required]" value="0">
            <input type="checkbox" name="value[cookie_settings_modal_items][record][required]" id="requiredSwitch_record" value="1" class="custom-switch-input"/>
            <span class="custom-switch-indicator"></span>
            <label class="custom-switch-description mb-0 cursor-pointer" for="requiredSwitch_record">{{ trans('public.required') }}</label>
        </label>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/js/admin/settings/cookie_settings.min.js"></script>
@endpush

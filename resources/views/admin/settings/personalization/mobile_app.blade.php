@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
    $buttonColors = ['primary','secondary','warning','danger'];
@endphp

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

<div class="mt-3" id="mobile_app">

    <form action="{{ getAdminPanelUrl() }}/settings/mobile_app" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="personalization">
        <input type="hidden" name="mobile_app" value="mobile_app">

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

                <div class="form-group">
                    <label class="input-label mb-0">{{ trans('admin/main.image') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="mobile_app_hero_image" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="value[mobile_app_hero_image]" required id="mobile_app_hero_image" value="{{ (!empty($itemValue) and !empty($itemValue['mobile_app_hero_image'])) ? $itemValue['mobile_app_hero_image'] : '' }}" class="form-control" placeholder="{{ trans('update.mobile_app_hero_image_placeholder') }}"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group ">
                    <label class="control-label">{{ trans('admin/main.description') }}</label>
                    <textarea name="value[mobile_app_description]" required class="summernote form-control text-left">{{ (!empty($itemValue) and !empty($itemValue['mobile_app_description'])) ? $itemValue['mobile_app_description'] : '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">

                <div id="mobile_app_buttons" class="ml-0">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <strong class="d-block">{{ trans('update.buttons') }}</strong>

                        <button type="button" data-parent="mobile_app_buttons" data-main-row="mobileAppMainRow" class="btn btn-success add-btn"><i class="fa fa-plus"></i> {{ trans('admin/main.add') }}</button>
                    </div>


                    @if(!empty($itemValue) and !empty($itemValue['mobile_app_buttons']))
                        @foreach($itemValue['mobile_app_buttons'] as $mobileAppButtonsKey => $mobileAppButtonsValue)
                            <div class="form-group p-2 border rounded-lg">
                           <label class="input-label mb-0">{{ trans('update.button_label') }}</label>
                                <div class="input-group">
                                    <input type="text" required name="value[mobile_app_buttons][{{ $mobileAppButtonsKey }}][title]"
                                           class="form-control w-auto flex-grow-1"
                                           placeholder="{{ trans('admin/main.choose_title') }}"
                                           value="{{ $mobileAppButtonsValue['title'] ?? '' }}"
                                    />

                                    <div class="input-group-append">
                                        <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>

                                <div class="form-group mb-0 mt-1">
                                    <label class="input-label mb-0">{{ trans('admin/main.link') }}</label>
                                    <input type="text" required name="value[mobile_app_buttons][{{ $mobileAppButtonsKey }}][link]"
                                           class="form-control w-100 flex-grow-1"
                                           placeholder="{{ trans('admin/main.link') }}"
                                           value="{{ $mobileAppButtonsValue['link'] ?? '' }}"
                                    />
                                </div>

                                <div class="form-group mb-0 mt-1">
                                    <label class="input-label mb-0">{{ trans('admin/main.icon') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button type="button" class="input-group-text admin-file-manager" data-input="mobile_app_button_icon_{{ $mobileAppButtonsKey }}" data-preview="holder">
                                                <i class="fa fa-upload"></i>
                                            </button>
                                        </div>
                                        <input type="text" required
                                               name="value[mobile_app_buttons][{{ $mobileAppButtonsKey }}][icon]"
                                               id="mobile_app_button_icon_{{ $mobileAppButtonsKey }}"
                                               class="form-control" placeholder="{{ trans('update.mobile_app_button_icon_placeholder') }}"
                                               value="{{ $mobileAppButtonsValue['icon'] ?? '' }}"
                                        />
                                    </div>
                                </div>

                                <div class="form-group mb-0 mt-1">
                                    <label class="input-label mb-0">{{ trans('update.color') }}</label>
                                    <select class="form-control" required name="value[mobile_app_buttons][{{ $mobileAppButtonsKey }}][color]">
                                        @foreach($buttonColors as $buttonColor)
                                            <option value="{{ $buttonColor }}" {{ (!empty($mobileAppButtonsValue['color']) and $mobileAppButtonsValue['color'] == $buttonColor) ? 'selected' : '' }}>{{ $buttonColor }}</option>
                                            <option value="outline-{{ $buttonColor }}" {{ (!empty($mobileAppButtonsValue['color']) and $mobileAppButtonsValue['color'] == "outline-$buttonColor") ? 'selected' : '' }}>outline-{{ $buttonColor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>

        <div class="form-group custom-switches-stacked mb-3 mt-3">
            <label class="custom-switch pl-0">
                <input type="hidden" name="value[show_app_footer]" value="0">
                <input type="checkbox" name="value[show_app_footer]" id="showAppFooterSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['show_app_footer']) and $itemValue['show_app_footer']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                <span class="custom-switch-indicator"></span>
                <label class="custom-switch-description mb-0 cursor-pointer" for="showAppFooterSwitch">{{ trans('update.show_footer') }}</label>
            </label>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>

<div id="mobileAppMainRow" class="form-group p-2 border rounded-lg d-none">
                 <label class="input-label mb-0">{{ trans('update.button_label') }}</label>
  <div class="input-group">
        <input type="text" name="value[mobile_app_buttons][record][title]" required
               class="form-control w-auto flex-grow-1"
               placeholder="{{ trans('admin/main.choose_title') }}"/>

        <div class="input-group-append">
            <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <div class="form-group mb-0 mt-1">
        <label class="input-label mb-0">{{ trans('admin/main.link') }}</label>
        <input type="text" name="value[mobile_app_buttons][record][link]" required
               class="form-control w-100 flex-grow-1"
               placeholder="{{ trans('admin/main.link') }}"/>
    </div>

    <div class="form-group mb-0 mt-1">
        <label class="input-label mb-0">{{ trans('admin/main.icon') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="input-group-text admin-file-manager" data-input="mobile_app_button_icon_record" data-preview="holder">
                    <i class="fa fa-upload"></i>
                </button>
            </div>
            <input type="text" name="value[mobile_app_buttons][record][icon]" required id="mobile_app_button_icon_record" class="form-control" placeholder="{{ trans('update.mobile_app_button_icon_placeholder') }}"/>
        </div>
    </div>

    <div class="form-group mb-0 mt-1">
        <label class="input-label mb-0">{{ trans('update.color') }}</label>
        <select class="form-control" name="value[mobile_app_buttons][record][color]" required>
            @foreach($buttonColors as $buttonColor)
                <option value="{{ $buttonColor }}">{{ $buttonColor }}</option>
                <option value="outline-{{ $buttonColor }}">outline-{{ $buttonColor }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/js/admin/settings/cookie_settings.min.js"></script>
@endpush

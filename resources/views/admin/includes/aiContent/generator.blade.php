@php
    $aiSettings = getAiContentsSettingsName();
@endphp

<div class="ai-content-generator-drawer bg-white">
    <div class="ai-content-generator-drawer-header d-flex align-items-center justify-content-between p-15">
        <div class="">
            <h5 class="font-16 font-weight-bold mb-0">{{ trans('update.ai_content_generator') }}</h5>
            <p class="mt-1 font-12 text-gray mb-0">{{ trans('update.generate_content_using_AI_fast_and_premium') }}</p>
        </div>

        <button type="button" class="js-right-drawer-close d-flex btn-transparent">
            <i data-feather="x" width="33" height="33" class=""></i>
        </button>
    </div>

    <div class="drawer-content p-15" data-simplebar @if((!empty($isRtl) and $isRtl)) data-simplebar-direction="rtl" @endif>

        <form action="{{ getAdminPanelUrl("/ai-contents/generate") }}" method="post">
            {{ csrf_field() }}

            <div class="d-flex align-items-center p-15 rounded-sm  bg-info-light border-gray300 mb-15">
                <div class="d-flex-center size-40 rounded-circle bg-white">
                    <img src="/assets/default/img/ai/ai-chip.svg" alt="ai" class="" width="20px" height="20px">
                </div>
                <div class="ml-2">
                    <h5 class="d-block font-weight-bold text-gray font-14 mb-0">{{ trans('update.generate_content_easily') }}</h5>
                    <p class="font-12 text-gray mb-0">{{ trans('update.select_the_content_type_you_want_and_describe_your_requirements_and_get_the_content') }}</p>
                </div>
            </div>

            @if(!empty($aiSettings['activate_text_service_type']) and !empty($aiSettings['activate_image_service_type']))
                <div class="form-group mb-1">
                    <label class="input-label">{{ trans('update.service_type') }}</label>
                    <select name="service_type" class="form-control">
                        <option value="">{{ trans('update.select_service_type') }}</option>
                        <option value="text">{{ trans('update.text') }}</option>
                        <option value="image">{{ trans('update.image') }}</option>
                    </select>
                </div>
            @elseif(!empty($aiSettings['activate_text_service_type']))
                <input type="hidden" name="service_type" value="text">
            @elseif(!empty($aiSettings['activate_image_service_type']))
                <input type="hidden" name="service_type" value="image">
            @endif
            <div class="">
                <span class="js-ajax-service_type"></span>
                <div class="invalid-feedback d-block"></div>
            </div>

            {{-- Text Fields --}}
            <div class="js-text-templates-field mt-20 {{ (!empty($aiSettings['activate_text_service_type']) and empty($aiSettings['activate_image_service_type'])) ? '' : 'd-none' }}">
                <div class="form-group">
                    <label class="input-label">{{ trans('update.service') }}</label>
                    <select name="text_service_id" class="js-ajax-text_service_id js-text-service-templates form-control">
                        <option value="">{{ trans('update.select_service') }}</option>

                        @if(!empty($aiContentTemplates))
                            @foreach($aiContentTemplates->where('type', 'text') as $aiContentTemplate)
                                <option value="{{ $aiContentTemplate->id }}" data-enable-length="{{ $aiContentTemplate->enable_length ? 'yes' : 'no' }}" data-length="{{ $aiContentTemplate->length }}">{{ $aiContentTemplate->title }}</option>
                            @endforeach
                        @endif

                        <option value="custom_text">{{ trans('update.custom_text') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                @if(!empty(getUserLanguagesLists()))
                    <div class="js-for-service-fields form-group d-none">
                        <label class="input-label">{{ trans('auth.language') }}</label>
                        <select name="language" class="js-ajax-language form-control">
                            @foreach(getUserLanguagesLists() as $lang => $language)
                                <option value="{{ $lang }}">{{ $language }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                @endif

                <div class="js-for-service-fields form-group d-none">
                    <label class="input-label">{{ trans('update.keyword') }}</label>
                    <input type="text" name="keyword" class="js-ajax-keyword form-control"/>
                    <div class="invalid-feedback"></div>
                    <p class="mt-1 font-12 text-gray">{{ trans('update.describe_in_some_words_about_what_you_want') }}</p>
                </div>

                <div class="form-group js-question-field d-none">
                    <label class="input-label">{{ trans('update.question') }}</label>
                    <input type="text" name="question" class="js-ajax-question form-control"/>
                    <div class="invalid-feedback"></div>
                    <p class="mt-1 font-12 text-gray">{{ trans('update.ask_ai_what_you_want') }}</p>
                </div>


                <div class="js-service-length-field form-group d-none">
                    <label class="input-label">{{ trans('update.length') }}</label>
                    <input type="number" name="length" class="js-ajax-length form-control" min="1" max="" data-max-error="{{ trans('update.the_maximum_allowed_is') }}"/>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            {{-- Image Fields --}}
            <div class="js-image-templates-field {{ (!empty($aiSettings['activate_image_service_type']) and empty($aiSettings['activate_text_service_type'])) ? '' : 'd-none' }}">

                <div class="form-group mt-20">
                    <label class="input-label">{{ trans('update.service') }}</label>
                    <select name="image_service_id" class="js-ajax-image_service_id js-image-service-templates form-control">
                        <option value="">{{ trans('update.select_service') }}</option>

                        @if(!empty($aiContentTemplates))
                            @foreach($aiContentTemplates->where('type', 'image') as $aiContentTemplate)
                                <option value="{{ $aiContentTemplate->id }}">{{ $aiContentTemplate->title }}</option>
                            @endforeach
                        @endif

                        <option value="custom_image">{{ trans('update.custom_image') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="form-group js-image-question-field d-none">
                    <label class="input-label">{{ trans('update.question') }}</label>
                    <input type="text" name="image_question" class="js-ajax-image_question form-control"/>
                    <div class="invalid-feedback"></div>
                    <p class="mt-1 font-12 text-gray">{{ trans('update.ask_ai_what_you_want') }}</p>
                </div>

                <div class="js-image-keyword-field form-group d-none">
                    <label class="input-label">{{ trans('update.keyword') }}</label>
                    <input type="text" name="image_keyword" class="js-ajax-image_keyword form-control"/>
                    <div class="invalid-feedback"></div>
                    <p class="mt-1 font-12 text-gray">{{ trans('update.describe_in_some_words_about_what_you_want') }}</p>
                </div>

                <div class="js-image-size-field form-group d-none">
                    <label class="input-label">{{ trans('update.image_size') }}</label>
                    <select name="image_size" class="js-ajax-image_size form-control">
                        <option value="">{{ trans('update.select_image_size') }}</option>
                        <option value="256">{{ trans('update.256x256') }}</option>
                        <option value="512">{{ trans('update.512x512') }}</option>
                        <option value="1024">{{ trans('update.1024x1024') }}</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

            </div>

            <button type="button" class="js-submit-ai-content-form btn btn-primary btn-block mt-20">{{ trans('update.generate') }}</button>
        </form>

        {{-- Text Generated --}}
        <div id="generatedTextContents" class="d-none"></div>


        {{-- Text Generated --}}
        <div class="js-image-generated mt-20 p-15 bg-info-light border-gray300 rounded-sm d-none">
            <div class="">
                <h4 class="font-14 text-gray">{{ trans('update.generated_content') }}</h4>
                <p class="mt-1 text-gray font-12">{{ trans('update.click_on_images_to_download_them') }}</p>
            </div>

            <div class="js-content mt-15 d-flex-center flex-wrap">

            </div>
        </div>

    </div>
</div>
<div class="ai-content-generator-drawer-mask"></div>

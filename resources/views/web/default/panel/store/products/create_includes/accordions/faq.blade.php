<li data-id="{{ !empty($faq) ? $faq->id :'' }}" class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="faq_{{ !empty($faq) ? $faq->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-controls="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" data-parent="#faqsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="help-circle" class=""></i>
            </span>

            <div class="font-weight-bold text-dark-blue d-block">{{ !empty($faq) ? $faq->title : trans('webinars.add_new_faqs') }}</div>
        </div>

        <div class="d-flex align-items-center">
            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($faq))
                <a href="/panel/store/products/faqs/{{ $faq->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-controls="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" data-parent="#faqsAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-labelledby="faq_{{ !empty($faq) ? $faq->id :'record' }}" class=" collapse @if(empty($faq)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-content-form faq-form" data-action="/panel/store/products/faqs/{{ !empty($faq) ? $faq->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($faq) ? $faq->id : 'new' }}][product_id]" value="{{ !empty($product) ? $product->id :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        @if(!empty(getGeneralSettings('content_translate')))
                            <div class="form-group">
                                <label class="input-label">{{ trans('auth.language') }}</label>
                                <select name="ajax[{{ !empty($faq) ? $faq->id : 'new' }}][locale]"
                                        class="form-control {{ !empty($faq) ? 'js-product-content-locale' : '' }}"
                                        data-product-id="{{ !empty($product) ? $product->id : '' }}"
                                        data-id="{{ !empty($faq) ? $faq->id : '' }}"
                                        data-relation="faqs"
                                        data-fields="title,answer"
                                >
                                    @foreach(getUserLanguagesLists() as $lang => $language)
                                        <option value="{{ $lang }}" {{ (!empty($faq) and !empty($faq->locale)) ? (mb_strtolower($faq->locale) == mb_strtolower($lang) ? 'selected' : '') : ($locale == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="ajax[{{ !empty($faq) ? $faq->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                        @endif


                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($faq) ? $faq->id : 'new' }}][title]" class="js-ajax-title form-control" value="{{ !empty($faq) ? $faq->title : '' }}" placeholder="{{ trans('forms.maximum_255_characters') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.answer') }}</label>
                            <textarea name="ajax[{{ !empty($faq) ? $faq->id : 'new' }}][answer]" class="js-ajax-answer form-control" rows="6">{{ !empty($faq) ? $faq->answer : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-faq btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($faq))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>

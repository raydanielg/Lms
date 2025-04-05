<div class="accordion-row bg-white rounded-lg border border-gray300 mt-3 py-3 py-lg-4 px-2 px-lg-3">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="faq_{{ !empty($faq) ? $faq->id :'record' }}">
        <div class="d-flex align-items-center cursor-pointer" href="#collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-controls="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" data-parent="#faqsAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <div class="font-weight-bold text-dark-blue d-block">{{ !empty($faq) ? $faq->title : trans('webinars.add_new_faqs') }}</div>
        </div>

        <div class="d-flex align-items-center">

            @if(!empty($faq))
                @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/store/products/faqs/'. $faq->id .'/delete','btnClass' => 'mr-3', 'btnText' => '<i class="fa fa-trash"></i>'])
            @endif

            <i class="collapse-chevron-icon fa fa-chevron-down cursor-pointer" href="#collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-controls="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" data-parent="#faqsAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseFaq{{ !empty($faq) ? $faq->id :'record' }}" aria-labelledby="faq_{{ !empty($faq) ? $faq->id :'record' }}" class=" collapse @if(empty($faq)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-content-form faq-form" data-action="{{ getAdminPanelUrl() }}/store/products/faqs/{{ !empty($faq) ? $faq->id . '/update' : 'store' }}">
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
                        <button type="button" class="btn btn-sm btn-danger ml-2 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

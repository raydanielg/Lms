<li data-id="{{ !empty($formField) ? $formField->id :'' }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="form_{{ !empty($formField) ? $formField->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseForm{{ !empty($formField) ? $formField->id :'record' }}" aria-controls="collapseForm{{ !empty($formField) ? $formField->id :'record' }}" data-parent="#formFieldsCard" role="button"
             data-toggle="collapse"
             aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="file-text" class=""></i>
            </span>

            <div class="font-weight-bold text-dark-blue d-block cursor-pointer">{{ !empty($formField) ? $formField->title : trans('update.add_new_field') }}</div>
        </div>

        <div class="d-flex align-items-center">

            @if(!empty($formField))
                <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

                <a href="{{ getAdminPanelUrl() }}/forms/{{ $form->id }}/fields/{{ $formField->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray" data-confirm="{{ trans('update.delete_form_field_confirm_btn_text') }}" data-title="{{ trans('update.delete_form_field_hint') }}">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseForm{{ !empty($formField) ? $formField->id :'record' }}" aria-controls="collapseForm{{ !empty($formField) ? $formField->id :'record' }}"
               data-parent="#formFieldsCard"
               role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseForm{{ !empty($formField) ? $formField->id :'record' }}" aria-labelledby="form_{{ !empty($formField) ? $formField->id :'record' }}" class=" collapse @if(empty($formField)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-field-form" data-action="{{ getAdminPanelUrl() }}/forms/{{ $form->id }}/fields/{{ !empty($formField) ? $formField->id . '/update' : 'store' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">

                        @if(!empty(getGeneralSettings('content_translate')))
                            <div class="form-group">
                                <label class="input-label">{{ trans('auth.language') }}</label>
                                <select name="ajax[{{ !empty($formField) ? $formField->id : 'new' }}][locale]"
                                        class="form-control {{ !empty($formField) ? 'js-form-field-locale' : '' }}"
                                        data-path="{{ !empty($formField) ? getAdminPanelUrl("/forms/{$form->id}/fields/{$formField->id}/edit") : '' }}"
                                >
                                    @foreach($userLanguages as $lang => $language)
                                        <option value="{{ $lang }}" {{ (!empty($formField) and !empty($formField->locale)) ? (mb_strtolower($formField->locale) == mb_strtolower($lang) ? 'selected' : '') : (app()->getLocale() == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="ajax[{{ !empty($formField) ? $formField->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                        @endif

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.type') }}</label>
                            <select name="ajax[{{ !empty($formField) ? $formField->id : 'new' }}][type]" class="js-ajax-type js-form-field-type form-control">
                                <option value="">{{ trans('update.choose_a_field_type') }}</option>
                                @foreach(\App\Models\FormField::$fieldTypes as $type)
                                    <option value="{{ $type }}" @if(!empty($formField) and $formField->type == $type) selected @endif>{{ trans('update.'.$type) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="js-form-field-title-card form-group {{ (!empty($formField)) ? '' : 'd-none' }}">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($formField) ? $formField->id : 'new' }}][title]" class="js-ajax-title form-control {{ !empty($formField) ? "js-title-field-{$formField->id}" : '' }}" value="{{ !empty($formField) ? $formField->title : '' }}"
                                   placeholder="{{ trans('forms.maximum_255_characters') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="js-field-options ml-1 {{ (!empty($formField) and in_array($formField->type, ['dropdown', 'checkbox', 'radio'])) ? '' : 'd-none' }}">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <strong class="d-block">{{ trans('admin/main.add_options') }}</strong>
                                <button type="button" class="btn btn-success add-field-option-btn "><i class="fa fa-plus"></i> {{ trans('admin/main.add') }}</button>
                            </div>

                            <ul class="js-field-options-lists draggable-content-lists draggable-form-field-options-lists-{{ !empty($formField) ? $formField->id : '' }}"
                                data-drag-class="draggable-form-field-options-lists-{{ !empty($formField) ? $formField->id : '' }}"
                                data-path="{{ !empty($formField) ? getAdminPanelUrl("/forms/{$form->id}/fields/{$formField->id}/options/orders") : '' }}"
                                data-move-class="move-icon2"
                            >
                                @if(!empty($formField) and !empty($formField->options))
                                    @foreach($formField->options as $option)
                                        <li data-id="{{ $option->id }}" class="form-group list-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text cursor-pointer move-icon2">
                                                        <i class="fa fa-arrows-alt"></i>
                                                    </div>
                                                </div>

                                                <input type="text" name="ajax[options][{{ $option->id }}][title]"
                                                       class="form-control w-auto flex-grow-1 js-title-option-{{ $option->id }}"
                                                       value="{{ $option->title }}"
                                                       placeholder="{{ trans('admin/main.choose_title') }}"/>

                                                <div class="input-group-append">
                                                    <a href="{{ getAdminPanelUrl() }}/forms/{{ $formField->id }}/fields/{{ $formField->id }}/options/{{ $option->id }}/delete" class="delete-action btn btn-danger d-inline-flex align-items-center">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>

                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <div class="form-group mt-30 mb-0 d-flex align-items-center">
                            <label class="" for="requiredSwitch{{ !empty($formField) ? $formField->id : 'new' }}">{{ trans('public.required') }}</label>
                            <div class="custom-control custom-switch ml-3">
                                <input type="checkbox" name="ajax[{{ !empty($formField) ? $formField->id : 'new' }}][required]" class="custom-control-input" id="requiredSwitch{{ !empty($formField) ? $formField->id : 'new' }}" {{ (!empty($formField) and $formField->required) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requiredSwitch{{ !empty($formField) ? $formField->id : 'new' }}"></label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-20 d-flex align-items-center">
                    <button type="button" class="js-save-form-field btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($formField))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>

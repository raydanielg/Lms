<div class="card">
    <div class="card-body">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control {{ !empty($template) ? 'js-edit-content-locale' : '' }}">
                    @foreach($userLanguages as $lang => $language)
                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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
            <label class="control-label">{!! trans('public.title') !!}</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ !empty($template) ? $template->title : old('title') }}">
            <div class="invalid-feedback">@error('title') {{ $message }} @enderror</div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('admin/main.template_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text admin-file-manager " data-input="image" data-preview="holder">
                        <i class="fa fa-upload"></i>
                    </button>
                </div>
                <input type="text" name="image" id="image" value="{{ !empty($template) ? $template->image : old('image') }}" class="js-certificate-image form-control @error('image') is-invalid @enderror" data-prefix="{{ url('') }}"/>
                <div class="invalid-feedback">@error('image') {{ $message }} @enderror</div>
            </div>
            <div class="invalid-feedback">@error('image') {{ $message }} @enderror</div>
            <div class="text-muted text-small mt-1">{{ trans('update.certificate_template_image_hint') }}</div>
        </div>

        <div class="form-group">
            <label class="control-label">{!! trans('public.type') !!}</label>
            <select name="type" class="form-control @error('type') is-invalid @enderror">
                <option value="">{{ trans('admin/main.select_type') }}</option>
                <option value="quiz" {{ (!empty($template) and $template->type == 'quiz') ? 'selected' : '' }}>{{ trans('update.quiz_related') }}</option>
                <option value="course" {{ (!empty($template) and $template->type == 'course') ? 'selected' : '' }}>{{ trans('update.course_completion') }}</option>
                <option value="bundle" {{ (!empty($template) and $template->type == 'bundle') ? 'selected' : '' }}>{{ trans('update.bundle_completion') }}</option>
            </select>
            <div class="invalid-feedback">@error('type') {{ $message }} @enderror</div>
        </div>


        @foreach($elements as $element => $elementValues)
            @if(!empty($elementValues))
                <div data-element="{{ $element }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
                    <div class="d-flex align-items-center justify-content-between " role="tab" id="{{ $element }}_accordion">
                        <div class="d-flex align-items-center" href="#{{ $element }}_accordion_collapse" aria-controls="{{ $element }}_accordion_collapse" role="button"
                             data-toggle="collapse"
                             aria-expanded="true">

                            <div class="font-weight-bold text-dark-blue d-block cursor-pointer">{{ trans("update.certificate_{$element}") }}</div>
                        </div>

                        <div class="d-flex align-items-center">
                            @foreach($elementValues as $elementValue)
                                @php
                                    $isActiveElement = (!empty($template) and !empty($template->elements) and !empty($template->elements[$element]) and !empty($template->elements[$element][$elementValue['name']])) ? $template->elements[$element][$elementValue['name']] : null;
                                @endphp
                            @endforeach

                            <span class="js-status-element text-success mr-2 {{ !empty($isActiveElement) ? '' : 'd-none' }}">{{ trans('admin/main.active') }}</span>

                            <div class="d-flex align-items-center">
                                <i class="fa fa-chevron-down" href="#{{ $element }}_accordion_collapse" aria-controls="{{ $element }}_accordion_collapse" role="button" data-toggle="collapse" aria-expanded="true"></i>
                            </div>
                        </div>
                    </div>

                    <div id="{{ $element }}_accordion_collapse" aria-labelledby="{{ $element }}_accordion" class="collapse" role="tabpanel">
                        <div class="panel-collapse text-gray">

                            @foreach($elementValues as $elementValue)
                                @php
                                    $storedData = (!empty($template) and !empty($template->elements) and !empty($template->elements[$element]) and !empty($template->elements[$element][$elementValue['name']])) ? $template->elements[$element][$elementValue['name']] : null;
                                @endphp

                                @if($elementValue['type'] == 'text_input')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <input type="text" name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control" value="{{ !empty($storedData) ? $storedData : '' }}">
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'number_input')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <input type="number" name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control" value="{{ !empty($storedData) ? $storedData : '' }}">
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'file_input_manager')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button type="button" class="input-group-text admin-file-manager " data-input="elements_{{ $element }}_{{ $elementValue['name'] }}" data-preview="holder">
                                                    <i class="fa fa-upload"></i>
                                                </button>
                                            </div>
                                            <input type="text" name="elements[{{ $element }}][{{ $elementValue['name'] }}]" id="elements_{{ $element }}_{{ $elementValue['name'] }}" value="{{ !empty($storedData) ? $storedData : '' }}" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control" data-prefix="{{ url('') }}"/>
                                        </div>
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'color_input')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <div class="input-group colorpickerinput">
                                            <input type="text" name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control" value="{{ !empty($storedData) ? $storedData : '#000' }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <i class="fas fa-fill-drip"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'switch')
                                    <div class="form-group d-flex align-items-center cursor-pointer">
                                        <div class="custom-control custom-switch align-items-start">
                                            <input type="checkbox" name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-element-{{ $elementValue['name'] }} custom-control-input {{ ($elementValue['name'] != 'enable') ? 'js-changeable-element-input' : '' }}" id="{{ $element }}_{{ $elementValue['name'] }}Switch" {{ !empty($storedData) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="{{ $element }}_{{ $elementValue['name'] }}Switch"></label>
                                        </div>
                                        <label for="{{ $element }}_{{ $elementValue['name'] }}Switch" class="mb-0 cursor-pointer">{{ $elementValue['label'] }}</label>
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'textarea')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <textarea name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control" rows="4">{{ !empty($storedData) ? $storedData : '' }}</textarea>
                                    </div>
                                @endif

                                @if($elementValue['type'] == 'select')
                                    <div class="form-group">
                                        <label class="input-label">{{ $elementValue['label'] }}:</label>
                                        <select name="elements[{{ $element }}][{{ $elementValue['name'] }}]" class="js-changeable-element-input js-element-{{ $elementValue['name'] }} form-control">
                                            @foreach($elementValue['options'] as $optionName => $optionLabel)
                                                <option value="{{ $optionName }}" {{ (!empty($storedData) and $storedData == $optionName) ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                            @endforeach


                            @if($element == 'date')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[date]">
                            @elseif($element == 'qr_code')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[qr_code]">
                            @elseif($element == 'student_name')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[student_name]">
                            @elseif($element == 'instructor_name')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[instructor_name]">
                            @elseif($element == 'platform_name')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[platform_name]">
                            @elseif($element == 'course_name')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[course_name]">
                            @elseif($element == 'instructor_signature')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[instructor_signature]">
                            @elseif($element == 'user_certificate_additional')
                                <input type="hidden" name="elements[{{ $element }}][content]" class="js-element-content" value="[user_certificate_additional]">
                            @endif

                        </div>
                    </div>
                </div>

            @endif

        @endforeach


        <input type="hidden" name="template_contents" class="js-template-contents" value="{{ (!empty($template) and !empty($template->body)) ? $template->body : '' }}">

        <div class="form-group custom-switches-stacked mt-3">
            <label class="custom-switch pl-0">
                <input type="hidden" name="status" value="draft">
                <input type="checkbox" id="status" name="status" value="publish" {{ (!empty($template) and $template->status == 'publish') ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                <span class="custom-switch-indicator"></span>
                <label class="custom-switch-description mb-0 cursor-pointer" for="status">{{ trans('admin/main.active') }}</label>
            </label>
        </div>


        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary">{{ trans('save') }}</button>
        </div>
    </div>
</div>

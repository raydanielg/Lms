<li data-id="{{ !empty($chapterItem) ? $chapterItem->id :'' }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="file_{{ !empty($assignment) ? $assignment->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseFile{{ !empty($assignment) ? $assignment->id :'record' }}" aria-controls="collapseFile{{ !empty($assignment) ? $assignment->id :'record' }}" data-parent="#chapterContentAccordion{{ !empty($chapter) ? $chapter->id :'' }}" role="button" data-toggle="collapse" aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="feather" class=""></i>
            </span>

            <div class="font-weight-bold text-dark-blue d-block">{{ !empty($assignment) ? $assignment->title . ($assignment->accessibility == 'free' ? " (". trans('public.free') .")" : '') : trans('update.add_new_assignments') }}</div>
        </div>

        <div class="d-flex align-items-center">

            @if(!empty($assignment) and $assignment->status != \App\Models\WebinarChapter::$chapterActive)
                <span class="disabled-content-badge mr-10">{{ trans('public.disabled') }}</span>
            @endif

            @if(!empty($assignment))
                <button type="button" data-item-id="{{ $assignment->id }}" data-item-type="{{ \App\Models\WebinarChapterItem::$chapterAssignment }}" data-chapter-id="{{ !empty($chapter) ? $chapter->id : '' }}" class="js-change-content-chapter btn btn-sm btn-transparent text-gray mr-10">
                    <i data-feather="grid" class="" height="20"></i>
                </button>
            @endif

            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($assignment))
                <a href="/panel/assignments/{{ $assignment->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseFile{{ !empty($assignment) ? $assignment->id :'record' }}" aria-controls="collapseFile{{ !empty($assignment) ? $assignment->id :'record' }}" data-parent="#chapterContentAccordion{{ !empty($chapter) ? $chapter->id :'' }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseFile{{ !empty($assignment) ? $assignment->id :'record' }}" aria-labelledby="file_{{ !empty($assignment) ? $assignment->id :'record' }}" class=" collapse @if(empty($assignment)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-content-form assignment-form" data-action="/panel/assignments/{{ !empty($assignment) ? $assignment->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][webinar_id]" value="{{ !empty($webinar) ? $webinar->id :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">

                        @if(!empty(getGeneralSettings('content_translate')))
                            <div class="form-group">
                                <label class="input-label">{{ trans('auth.language') }}</label>
                                <select name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][locale]"
                                        class="form-control {{ !empty($assignment) ? 'js-webinar-content-locale' : '' }}"
                                        data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}"
                                        data-id="{{ !empty($assignment) ? $assignment->id : '' }}"
                                        data-relation="assignments"
                                        data-fields="title,description"
                                >
                                    @foreach($userLanguages as $lang => $language)
                                        <option value="{{ $lang }}" {{ (!empty($assignment) and !empty($assignment->locale)) ? (mb_strtolower($assignment->locale) == mb_strtolower($lang) ? 'selected' : '') : ($locale == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                        @endif

                        @if(!empty($assignment))
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.chapter') }}</label>
                                <select name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][chapter_id]" class="js-ajax-chapter_id form-control">
                                    @foreach($webinar->chapters as $ch)
                                        <option value="{{ $ch->id }}" {{ ($assignment->chapter_id == $ch->id) ? 'selected' : '' }}>{{ $ch->title }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @else
                            <input type="hidden" name="ajax[new][chapter_id]" value="" class="chapter-input">
                        @endif

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][title]" class="js-ajax-title form-control" value="{{ !empty($assignment) ? $assignment->title : '' }}" placeholder=""/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.description') }}</label>
                            <textarea name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][description]" class="js-ajax-description form-control" rows="6">{{ !empty($assignment) ? $assignment->description : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('quiz.grade') }}</label>
                            <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][grade]" class="js-ajax-grade form-control" value="{{ !empty($assignment) ? $assignment->grade : '' }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.pass_grade') }}</label>
                            <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][pass_grade]" class="js-ajax-pass_grade form-control" value="{{ !empty($assignment) ? $assignment->pass_grade : '' }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.deadline') }}</label>
                            <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][deadline]" class="js-ajax-deadline form-control" value="{{ !empty($assignment) ? $assignment->deadline : '' }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.attempts') }}</label>
                            <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][attempts]" class="js-ajax-attempts form-control" value="{{ !empty($assignment) ? $assignment->attempts : '' }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="js-assignment-attachments-items form-group mt-15">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="input-label mb-0">{{ trans('public.attachments') }}</label>

                                <button type="button" class="btn btn-primary btn-sm assignment-attachments-add-btn">
                                    <i data-feather="plus" width="18" height="18" class="text-white"></i>
                                </button>
                            </div>

                            <div class="assignment-attachments-main-row js-ajax-attachments position-relative">
                                <div class="mt-10 p-10 border rounded">
                                    <div class="mb-10">
                                        <label class="input-label">{{ trans('public.title') }}</label>
                                        <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][attachments][assignmentTemp][title]" class="form-control" placeholder="{{ trans('forms.maximum_255_characters') }}"/>
                                    </div>

                                    <div class="input-group product-images-input-group">
                                        <div class="input-group-prepend">
                                            <button type="button" class="input-group-text panel-file-manager" data-input="attachments_assignmentTemp" data-preview="holder">
                                                <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][attachments][assignmentTemp][attach]" id="attachments_assignmentTemp" value="" class="form-control" placeholder="{{ trans('update.assignment_attachments_placeholder') }}"/>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-danger btn-sm assignment-attachments-remove-btn d-none">
                                    <i data-feather="x" width="18" height="18" class="text-white"></i>
                                </button>
                            </div>

                            <div class="invalid-feedback"></div>

                            @if(!empty($assignment) and !empty($assignment->attachments) and count($assignment->attachments))
                                @foreach($assignment->attachments as $attachment)
                                    <div class="js-ajax-attachments position-relative">
                                        <div class="mt-10 p-10 border rounded">
                                            <div class="mb-10">
                                                <label class="input-label">{{ trans('public.title') }}</label>
                                                <input type="text" name="ajax[{{ $assignment->id }}][attachments][{{ $attachment->id }}][title]" value="{{ $attachment->title }}" class="form-control" placeholder="{{ trans('forms.maximum_255_characters') }}"/>
                                            </div>

                                            <div class="input-group product-images-input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text panel-file-manager" data-input="attachments_{{ $attachment->id }}" data-preview="holder">
                                                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="ajax[{{ $assignment->id }}][attachments][{{ $attachment->id }}][attach]" id="attachments_{{ $attachment->id }}" value="{{ $attachment->attach }}" class="form-control" placeholder="{{ trans('update.assignment_attachments_placeholder') }}"/>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-danger btn-sm assignment-attachments-remove-btn">
                                            <i data-feather="x" width="18" height="18" class="text-white"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group mt-20">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer input-label" for="assignmentStatusSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}">{{ trans('public.active') }}</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][status]" class="custom-control-input" id="assignmentStatusSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}" {{ (empty($assignment) or $assignment->status == \App\Models\File::$Active) ? 'checked' : ''  }}>
                                    <label class="custom-control-label" for="assignmentStatusSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}"></label>
                                </div>
                            </div>
                        </div>

                        @if(getFeaturesSettings('sequence_content_status'))
                            <div class="form-group mt-20">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="cursor-pointer input-label" for="SequenceContentSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}">{{ trans('update.sequence_content') }}</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][sequence_content]" class="js-sequence-content-switch custom-control-input" id="SequenceContentSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}" {{ (!empty($assignment) and ($assignment->check_previous_parts or !empty($assignment->access_after_day))) ? 'checked' : ''  }}>
                                        <label class="custom-control-label" for="SequenceContentSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="js-sequence-content-inputs pl-5 {{ (!empty($assignment) and ($assignment->check_previous_parts or !empty($assignment->access_after_day))) ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="cursor-pointer input-label" for="checkPreviousPartsSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}">{{ trans('update.check_previous_parts') }}</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][check_previous_parts]" class="custom-control-input" id="checkPreviousPartsSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}" {{ (empty($assignment) or $assignment->check_previous_parts) ? 'checked' : ''  }}>
                                            <label class="custom-control-label" for="checkPreviousPartsSwitch{{ !empty($assignment) ? $assignment->id : '_record' }}"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('update.access_after_day') }}</label>
                                    <input type="number" name="ajax[{{ !empty($assignment) ? $assignment->id : 'new' }}][access_after_day]" value="{{ (!empty($assignment)) ? $assignment->access_after_day : '' }}" class="js-ajax-access_after_day form-control" placeholder="{{ trans('update.access_after_day_placeholder') }}"/>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-assignment btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($assignment))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>

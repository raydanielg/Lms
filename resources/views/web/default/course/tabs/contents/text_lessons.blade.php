@php
    $checkSequenceContent = $textLesson->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));
@endphp

<div class="accordion-row rounded-sm border mt-15 p-15">
    <div class="d-flex align-items-center justify-content-between" role="tab" id="textLessons_{{ $textLesson->id }}">
        <div class="d-flex align-items-center" href="#collapseTextLessons{{ $textLesson->id }}" aria-controls="collapseTextLessons{{ $textLesson->id }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true">

            @if($textLesson->accessibility == 'paid')
                @if(!empty($user) and $hasBought)
                    <a href="{{ $course->getLearningPageUrl() }}?type=text_lesson&item={{ $textLesson->id }}" target="_blank" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.read') }}">
                            <span class="chapter-icon chapter-content-icon">
                            <i data-feather="file-text" width="20" height="20" class="text-gray"></i>
                            </span>
                    </a>
                @else
                    <span class="mr-15 chapter-icon chapter-content-icon">
                        <i data-feather="lock" width="20" height="20" class="text-gray"></i>
                    </span>
                @endif
            @else
                @if(!empty($user) and $hasBought)
                    <a href="{{ $course->getLearningPageUrl() }}?type=text_lesson&item={{ $textLesson->id }}" target="_blank" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.read') }}">
                        <span class="chapter-icon chapter-content-icon">
                            <i data-feather="file-text" width="20" height="20" class="text-gray"></i>
                        </span>
                    </a>
                @else
                    <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="mr-15" data-toggle="tooltip" data-placement="top" title="{{ trans('public.read') }}">
                        <span class="chapter-icon chapter-content-icon">
                            <i data-feather="file-text" width="20" height="20" class="text-gray"></i>
                        </span>
                    </a>
                @endif
            @endif

            <span class="font-weight-bold text-secondary font-14 file-title">{{ $textLesson->title }}</span>
        </div>

        <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseTextLessons{{ !empty($textLesson) ? $textLesson->id :'record' }}" aria-controls="collapseTextLessons{{ !empty($textLesson) ? $textLesson->id :'record' }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
    </div>

    <div id="collapseTextLessons{{ $textLesson->id }}" aria-labelledby="textLessons_{{ $textLesson->id }}" class=" collapse" role="tabpanel">
        <div class="panel-collapse">
            <div class="text-gray">
                {!! nl2br(clean($textLesson->summary)) !!}
            </div>

            @if(!empty($user) and $hasBought)
                <div class="d-flex align-items-center mt-20">
                    <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="textLessonReadToggle{{ $textLesson->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" @if($sequenceContentHasError) disabled @endif id="textLessonReadToggle{{ $textLesson->id }}" data-lesson-id="{{ $textLesson->id }}" value="{{ $course->id }}" class="js-text-lesson-learning-toggle custom-control-input" @if(!empty($textLesson->checkPassedItem())) checked @endif>
                        <label class="custom-control-label" for="textLessonReadToggle{{ $textLesson->id }}"></label>
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mt-20">

                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="clock" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ $textLesson->study_time }} {{ trans('public.min') }}</span>
                    </div>

                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="paperclip" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ trans('public.attachments') }}: {{ $textLesson->attachments_count }}</span>
                    </div>
                </div>

                <div class="">
                    @if(!empty($checkSequenceContent) and $sequenceContentHasError)
                        <button
                            type="button"
                            class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled js-sequence-content-error-modal"
                            data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
                            data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
                        >{{ trans('public.read') }}</button>
                    @elseif($textLesson->accessibility == 'paid')
                        @if(!empty($user) and $hasBought)
                            <a href="{{ $course->getLearningPageUrl() }}?type=text_lesson&item={{ $textLesson->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                {{ trans('public.read') }}
                            </a>
                        @else
                            <button type="button" class="course-content-btns btn btn-sm btn-gray disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : '')) }}">
                                {{ trans('public.read') }}
                            </button>
                        @endif
                    @else
                        @if(!empty($user) and $hasBought)
                            <a href="{{ $course->getLearningPageUrl() }}?type=text_lesson&item={{ $textLesson->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                {{ trans('public.read') }}
                            </a>
                        @else
                            <a href="{{ $course->getUrl() }}/lessons/{{ $textLesson->id }}/read" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                {{ trans('public.read') }}
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

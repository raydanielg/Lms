@php
    $checkSequenceContent = $assignment->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));
@endphp

<div class="accordion-row rounded-sm border mt-15 p-15">
    <div class="d-flex align-items-center justify-content-between" role="tab" id="assignment_{{ $assignment->id }}">
        <div class="d-flex align-items-center" href="#collapseAssignment{{ $assignment->id }}" aria-controls="collapseAssignment{{ $assignment->id }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true">

            <span class="mr-15 chapter-icon chapter-content-icon">
                <i data-feather="feather" width="20" height="20" class="text-gray"></i>
            </span>

            <span class="font-weight-bold text-secondary font-14 file-title">{{ $assignment->title }}</span>
        </div>

        <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseAssignment{{ !empty($assignment) ? $assignment->id :'record' }}" aria-controls="collapseAssignment{{ !empty($assignment) ? $assignment->id :'record' }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
    </div>

    <div id="collapseAssignment{{ $assignment->id }}" aria-labelledby="assignment_{{ $assignment->id }}" class=" collapse" role="tabpanel">
        <div class="panel-collapse">
            <div class="text-gray">
                {!! nl2br(clean($assignment->description)) !!}
            </div>

            <div class="d-flex align-items-center justify-content-between mt-20">

                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="clock" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ trans('update.min_grade') }}: {{ $assignment->pass_grade }}</span>
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
                    @elseif(!empty($user) and $hasBought)
                        <a href="{{ $course->getLearningPageUrl() }}?type=assignment&item={{ $assignment->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                            {{ trans('public.read') }}
                        </a>
                    @else
                        <button type="button" class="course-content-btns btn btn-sm btn-gray disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : '')) }}">
                            {{ trans('public.read') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

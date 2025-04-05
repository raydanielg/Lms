@php
    $itemHistory = $item->getAssignmentHistoryByStudentId(request()->get('student', $user->id));

    $checkSequenceContent = $item->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));

    $assignmentUrl = "{$course->getLearningPageUrl()}?type=assignment&item={$item->id}";
    $assignmentUrlTarget = "_self";
    if ($course->isOwner($user->id)) {
        $assignmentUrl = "/panel/assignments/{$item->id}/students";
        $assignmentUrlTarget = "_blank";
    } elseif ($user->isAdmin() or $course->isPartnerTeacher($user->id)) {
        $assignmentUrl = "#!";
    }
@endphp

<a href="{{ (!empty($checkSequenceContent) and $sequenceContentHasError) ? '#!' : $assignmentUrl }}" target="{{ $assignmentUrlTarget }}" class=" d-flex align-items-start p-10 cursor-pointer {{ (!empty($checkSequenceContent) and $sequenceContentHasError) ? 'js-sequence-content-error-modal' : 'tab-item' }} {{ ($user->isAdmin() or $course->isPartnerTeacher($user->id)) ? 'js-not-access-toast' : '' }}"
   data-type="assignment"
   data-id="{{ $item->id }}"
   data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
   data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
>

        <span class="chapter-icon bg-gray300 mr-10">
            <i data-feather="feather" class="text-gray" width="16" height="16"></i>
        </span>

    <div>
        <div class="">
            <span class="font-weight-500 font-14 text-dark-blue d-block">{{ $item->title }}</span>
            @if(empty($itemHistory) or ($itemHistory->status == \App\Models\WebinarAssignmentHistory::$notSubmitted))
                <span class="text-danger font-12 d-block">{{ trans('update.assignment_history_status_not_submitted') }}</span>
            @else
                @switch($itemHistory->status)
                    @case(\App\Models\WebinarAssignmentHistory::$passed)
                        <span class="text-primary font-12 d-block">{{ trans('quiz.passed') }}</span>
                        @break
                    @case(\App\Models\WebinarAssignmentHistory::$pending)
                        <span class="text-warning font-12 d-block">{{ trans('public.pending') }}</span>
                        @break
                    @case(\App\Models\WebinarAssignmentHistory::$notPassed)
                        <span class="font-12 d-block text-danger">{{ trans('quiz.failed') }}</span>
                        @break
                @endswitch
            @endif
        </div>


        <div class="tab-item-info mt-15">
            <p class="font-12 text-gray d-block">
                {!! truncate($item->description, 150) !!}
            </p>

            @php
                $itemDeadline = $item->getDeadlineTimestamp();
            @endphp

            <div class="d-block mt-10 font-12 text-gray">
                <span class="">{{ trans('update.deadline') }}: </span>
                @if(is_bool($itemDeadline))
                    @if(!$itemDeadline)
                        <span class="text-danger">{{ trans('panel.expired') }}</span>
                    @else
                        <span>{{ trans('update.unlimited') }}</span>
                    @endif
                @elseif(!empty($itemDeadline))
                    {{ dateTimeFormat($itemDeadline, 'j M Y') }}
                @else
                    <span>{{ trans('update.unlimited') }}</span>
                @endif
            </div>
        </div>
    </div>
</a>

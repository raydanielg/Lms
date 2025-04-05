@php
    $checkSequenceContent = $session->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));
@endphp

<div class="accordion-row rounded-sm border mt-15 p-15">
    <div class="d-flex align-items-center justify-content-between" role="tab" id="session_{{ $session->id }}">
        <div class="d-flex align-items-center" href="#collapseSession{{ $session->id }}" aria-controls="collapseSession{{ $session->id }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true">
            @if($session->date > time())
                <a href="{{ $session->addToCalendarLink() }}" target="_blank" class="mr-15 d-flex" data-toggle="tooltip" data-placement="top" title="{{ trans('public.add_to_calendar') }}">
                    <span class="chapter-icon chapter-content-icon">
                    <i data-feather="bell" width="20" height="20" class="text-gray"></i>
                    </span>
                </a>
            @else
                <span class="mr-15 d-flex chapter-icon chapter-content-icon">
                    <i data-feather="bell" width="20" height="20" class="text-gray"></i>
                </span>
            @endif
            <span class="font-weight-bold text-secondary font-14">{{ $session->title }}</span>
        </div>

        <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-controls="collapseSession{{ !empty($session) ? $session->id :'record' }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
    </div>

    <div id="collapseSession{{ $session->id }}" aria-labelledby="session_{{ $session->id }}" class=" collapse" role="tabpanel">
        <div class="panel-collapse">
            <div class="text-gray">
                {!! nl2br(clean($session->description)) !!}
            </div>

            @if(!empty($user) and $hasBought)
                <div class="d-flex align-items-center mt-20">
                    <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="sessionReadToggle{{ $session->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" @if(($session->date < time()) or $sequenceContentHasError) disabled @endif id="sessionReadToggle{{ $session->id }}" data-session-id="{{ $session->id }}" value="{{ $course->id }}" class="js-text-session-toggle custom-control-input" @if(!empty($session->checkPassedItem())) checked @endif>
                        <label class="custom-control-label" for="sessionReadToggle{{ $session->id }}"></label>
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mt-20">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="clock" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ convertMinutesToHourAndMinute($session->duration) }} {{ trans('home.hours') }}</span>
                    </div>

                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="calendar" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ dateTimeFormat($session->date, 'j M Y | H:i') }}</span>
                    </div>
                </div>

                <div class="">
                    @if($session->isFinished())
                        <button type="button" class="course-content-btns btn btn-sm btn-gray disabled flex-grow-1 disabled session-finished-toast">{{ trans('public.finished') }}</button>
                    @elseif(empty($user))
                        <button type="button" class="course-content-btns btn btn-sm btn-gray disabled flex-grow-1 disabled not-login-toast">{{ trans('public.go_to_class') }}</button>
                    @elseif($hasBought)
                        @if(!empty($checkSequenceContent) and $sequenceContentHasError)
                            <button
                                type="button"
                                class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled js-sequence-content-error-modal"
                                data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
                                data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
                            >{{ trans('public.go_to_class') }}</button>
                        @else
                            <a href="{{ $course->getLearningPageUrl() }}?type=session&item={{ $session->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary flex-grow-1">{{ trans('public.go_to_class') }}</a>
                        @endif
                    @else
                        <button type="button" class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled not-access-toast">{{ trans('public.go_to_class') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

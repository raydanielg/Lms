@php
    $cardUser = !empty($answer) ? $answer->user : $courseForum->user;
@endphp

<div class="course-forum-answer-card py-15 m-15 rounded-lg {{ (!empty($answer) and $answer->resolved) ? 'resolved' : '' }}">
    <div class="d-flex flex-wrap">
        <div class="col-12 col-md-3">
            <div class="position-relative bg-info-light d-flex flex-column align-items-center justify-content-center rounded-lg w-100 h-100 p-20">
                <div class="user-avatar rounded-circle {{ (!empty($answer) and $cardUser->isTeacher()) ? 'is-instructor' : '' }}">
                    <img src="{{ $cardUser->getAvatar(72) }}" class="img-cover rounded-circle" alt="{{ $cardUser->full_name }}">
                </div>
                <h4 class="font-14 text-secondary mt-15 font-weight-bold">{{ $cardUser->full_name }}</h4>

                <span class="px-10 py-5 mt-5 rounded-lg border bg-info-light text-center font-12 text-gray">
                    @if($cardUser->isUser())
                        {{ trans('quiz.student') }}
                    @elseif($cardUser->isTeacher())
                        {{ trans('public.instructor') }}
                    @elseif($cardUser->isOrganization())
                        {{ trans('home.organization') }}
                    @elseif($cardUser->isAdmin())
                        {{ trans('panel.staff') }}
                    @endif
                </span>

                @if(!empty($answer) and $answer->pin)
                    <span class="pinned-icon d-flex align-items-center justify-content-center">
                        <img src="/assets/default/img/learning/un_pin.svg" alt="pin icon" class="">
                    </span>
                @endif
            </div>
        </div>

        <div class="col-12 col-md-9 mt-15 mt-md-0">
            <div class="d-flex flex-column justify-content-between h-100">
                <div class="">
                    <p class="font-14 text-gray d-block white-space-pre-wrap">{{ !empty($answer) ? $answer->description : $courseForum->description }}</p>

                    @if(empty($answer) and !empty($courseForum->attach))
                        <div class="mt-25 d-inline-block">
                            <a href="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/downloadAttach" target="_blank" class="d-flex align-items-center text-gray bg-info-light border px-10 py-5 rounded-pill">
                                <i data-feather="paperclip" class="text-gray" width="16" height="16"></i>
                                <span class="ml-5 font-12 text-gray">{{ trans('update.attachment') }}</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between mt-15 pt-15 border-top">
                    <span class="font-12 font-weight-500 text-gray">{{ dateTimeFormat(!empty($answer) ? $answer->created_at : $courseForum->created_at,'j M Y | H:i') }}</span>

                    <div class="d-flex align-items-center">
                        @if(empty($answer) and $user->id == $courseForum->user_id)
                            <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/edit" class="js-edit-forum btn-transparent font-12 font-weight-500 text-gray">{{ trans('public.edit') }}</button>
                        @elseif(!empty($answer))
                            @if($course->isOwner($user->id))
                                @if($answer->pin)
                                    <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/answers/{{ $answer->id }}/un_pin" class="js-btn-answer-un_pin btn-transparent font-12 font-weight-500 text-warning">{{ trans('update.un_pin') }}</button>
                                @else
                                    <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/answers/{{ $answer->id }}/pin" class="js-btn-answer-pin btn-transparent font-12 font-weight-500 text-gray">{{ trans('update.pin') }}</button>
                                @endif
                            @endif

                            @if($course->isOwner($user->id) or $user->id == $courseForum->user_id)
                                @if($answer->resolved)
                                    <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/answers/{{ $answer->id }}/mark_as_not_resolved" class="js-btn-answer-mark_as_not_resolved btn-transparent font-12 font-weight-500 text-gray ml-20">{{ trans('update.mark_as_not_resolved') }}</button>
                                @else
                                    <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/answers/{{ $answer->id }}/mark_as_resolved" class="js-btn-answer-mark_as_resolved btn-transparent font-12 font-weight-500 text-gray ml-20">{{ trans('update.mark_as_resolved') }}</button>
                                @endif
                            @endif

                            @if($user->id == $answer->user_id)
                                <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $courseForum->id }}/answers/{{ $answer->id }}/edit" class="js-edit-forum-answer btn-transparent font-12 font-weight-500 text-gray ml-20">{{ trans('public.edit') }}</button>
                            @endif

                            @if($answer->resolved)
                                <div class="resolved-answer-badge d-flex align-items-center ml-25 text-primary font-12">
                                    <span class="badge-icon d-flex align-items-center justify-content-center">
                                        <i data-feather="check" width="20" height="20"></i>
                                    </span>
                                    {{ trans('update.resolved') }}
                                </div>
                            @endif

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

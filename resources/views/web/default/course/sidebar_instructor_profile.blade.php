<div class="rounded-lg shadow-sm mt-35 p-20 course-teacher-card d-flex align-items-center flex-column">

    @if(!empty($webinarPartnerTeacher))
        <span class="user-select-none px-15 py-10 bg-gray200 off-label text-gray text-white font-12 rounded-sm ml-auto">{{ trans('public.invited') }}</span>
    @endif

    <div class="teacher-avatar mt-5">
        <img src="{{ $courseTeacher->getAvatar(100) }}" class="img-cover" alt="{{ $courseTeacher->full_name }}">

        @if($courseTeacher->offline)
            <span class="user-circle-badge unavailable d-flex align-items-center justify-content-center">
              <i data-feather="slash" width="20" height="20" class="text-white"></i>
           </span>
        @elseif($courseTeacher->verified)
            <span class="user-circle-badge has-verified d-flex align-items-center justify-content-center">
                <i data-feather="check" width="20" height="20" class="text-white"></i>
            </span>
        @endif
    </div>
    <h3 class="mt-10 font-16 font-weight-bold text-secondary">{{ $courseTeacher->full_name }}</h3>
    <span class="mt-5 font-14 font-weight-500 text-gray text-center">{{ $courseTeacher->bio }}</span>

    @include('web.default.includes.webinar.rate',['rate' => $courseTeacher->rates()])

    <div class="user-reward-badges d-flex flex-wrap align-items-center mt-20">
        @foreach($courseTeacher->getBadges() as $userBadge)
            <div class="mr-15 mt-10" data-toggle="tooltip" data-placement="bottom" data-html="true" title="{!! (!empty($userBadge->badge_id) ? nl2br($userBadge->badge->description) : nl2br($userBadge->description)) !!}">
                <img src="{{ !empty($userBadge->badge_id) ? $userBadge->badge->image : $userBadge->image }}" width="32" height="32" alt="{{ !empty($userBadge->badge_id) ? $userBadge->badge->title : $userBadge->title }}">
            </div>
        @endforeach
    </div>

    @php
        $hasMeeting = !empty($courseTeacher->hasMeeting());
    @endphp

    <div class="mt-25 d-flex flex-row align-items-center justify-content-center w-100">
        <a href="{{ $courseTeacher->getProfileUrl() }}" target="_blank" class="btn btn-sm btn-primary {{ $hasMeeting ? 'teacher-btn-action' : 'btn-block' }}">{{ trans('public.profile') }}</a>

        @if($hasMeeting)
            <a href="{{ $courseTeacher->getProfileUrl() }}" class="btn btn-sm btn-primary teacher-btn-action ml-15">{{ trans('public.book_a_meeting') }}</a>
        @endif
    </div>
</div>

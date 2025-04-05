@if(!empty($userBadges) and count($userBadges))

    <div class="user-reward-badges badges-lg row align-items-center mt-10 mt-lg-20">

        @foreach($userBadges as $userBadge)

            <div class="col-6 col-lg-3 mt-20 mt-lg-0">
                <div class="rounded-lg badges-item py-20 py-lg-40 shadow-sm px-10 px-lg-25 d-flex flex-column align-items-center">
                    <img src="{{ !empty($userBadge->badge_id) ? $userBadge->badge->image : $userBadge->image }}" class="rounded-circle" alt="{{ !empty($userBadge->badge_id) ? $userBadge->badge->title : $userBadge->title }}">

                    <span class="font-16 font-weight-bold text-dark-blue mt-15 mt-lg-25">{{ !empty($userBadge->badge_id) ? $userBadge->badge->title : $userBadge->title }}</span>
                    <span class="font-14 text-gray mt-5 mt-lg-10 text-center">{!! (!empty($userBadge->badge_id) ? nl2br($userBadge->badge->description) : nl2br($userBadge->description)) !!}</span>
                </div>
            </div>

        @endforeach

    </div>

@else
    @include(getTemplate() . '.includes.no-result',[
        'file_name' => 'badge.png',
        'title' => trans('site.instructor_not_have_badge'),
        'hint' => '',
    ])

@endif

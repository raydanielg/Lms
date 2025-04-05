@php
    $progressSteps = [
        1 => [
            'lang' => 'public.basic_information',
            'icon' => 'basic-info'
        ],

        2 => [
            'lang' => 'public.images',
            'icon' => 'images'
        ],

        3 => [
            'lang' => 'public.about',
            'icon' => 'about'
        ],

        4 => [
            'lang' => 'public.educations',
            'icon' => 'graduate'
        ],

        5 => [
            'lang' => 'public.experiences',
            'icon' => 'experiences'
        ],

        6 => [
            'lang' => 'public.identity_and_financial',
            'icon' => 'financial'
        ]
    ];

    if(!$user->isUser()) {
        $progressSteps[6] =[
            'lang' => 'public.occupations',
            'icon' => 'skills'
        ];

        $progressSteps[7] =[
            'lang' => 'public.identity_and_financial',
            'icon' => 'financial'
        ];

        $progressSteps[8] =[
            'lang' => 'public.extra_information',
            'icon' => 'extra_info'
        ];
    } else {
        $progressSteps[7] =[
            'lang' => 'public.extra_information',
            'icon' => 'extra_info'
        ];
    }

    $currentStep = empty($currentStep) ? 1 : $currentStep;
@endphp


<div class="webinar-progress d-block d-lg-flex align-items-center p-15 panel-shadow bg-white rounded-sm">

    @foreach($progressSteps as $key => $step)
        <div class="progress-item d-flex align-items-center">
            <a href="@if(!empty($organization_id)) /panel/manage/{{ $user_type ?? 'instructors' }}/{{ $user->id }}/edit/step/{{ $key }} @else /panel/setting/step/{{ $key }} @endif" class="progress-icon p-10 d-flex align-items-center justify-content-center rounded-circle {{ $key == $currentStep ? 'active' : '' }}" data-toggle="tooltip" data-placement="top" title="{{ trans($step['lang']) }}">
                <img src="/assets/default/img/icons/{{ $step['icon'] }}.svg" class="img-cover" alt="">
            </a>

            <div class="ml-10 {{ $key == $currentStep ? '' : 'd-lg-none' }}">
                <h4 class="font-16 text-secondary font-weight-bold">{{ trans($step['lang']) }}</h4>
            </div>
        </div>
    @endforeach
</div>

@if($user->offline)
    <div class="user-offline-alert d-flex mt-40">
        <div class="p-15">
            <h3 class="font-16 text-dark-blue">{{ trans('public.instructor_is_not_available') }}</h3>
            <p class="font-14 font-weight-500 text-gray mt-15">{{ $user->offline_message }}</p>
        </div>

        <div class="offline-icon offline-icon-right ml-auto d-flex align-items-stretch">
            <div class="d-flex align-items-center">
                <img src="/assets/default/img/profile/time-icon.png" alt="offline">
            </div>
        </div>
    </div>
@endif

@if((!empty($educations) and !$educations->isEmpty()) or (!empty($experiences) and !$experiences->isEmpty()) or (!empty($occupations) and !$occupations->isEmpty()) or !empty($user->about))
    @if(!empty($educations) and !$educations->isEmpty())
        <div class="mt-40">
            <h3 class="font-16 text-dark-blue font-weight-bold">{{ trans('site.education') }}</h3>

            <ul class="list-group-custom">
                @foreach($educations as $education)
                    <li class="mt-15 text-gray">{{ $education->value }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($experiences) and !$experiences->isEmpty())
        <div class="mt-40">
            <h3 class="font-16 text-dark-blue font-weight-bold">{{ trans('site.experiences') }}</h3>

            <ul class="list-group-custom">
                @foreach($experiences as $experience)
                    <li class="mt-15 text-gray">{{ $experience->value }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($user->about))
        <div class="mt-40">
            <h3 class="font-16 text-dark-blue font-weight-bold">{{ trans('site.about') }}</h3>

            <div class="mt-30">
                {!! nl2br($user->about) !!}
            </div>
        </div>
    @endif

    @if(!empty($occupations) and !$occupations->isEmpty())
        <div class="mt-40">
            <h3 class="font-16 text-dark-blue font-weight-bold">{{ trans('site.occupations') }}</h3>

            <div class="d-flex flex-wrap align-items-center pt-10">
                @foreach($occupations as $occupation)
                    <div class="bg-gray200 font-14 rounded mt-10 px-10 py-5 text-gray mr-15">{{ $occupation->category->title }}</div>
                @endforeach
            </div>
        </div>
    @endif

@else

    @include(getTemplate() . '.includes.no-result',[
        'file_name' => 'bio.png',
        'title' => trans('site.not_create_bio'),
        'hint' => '',
    ])

@endif


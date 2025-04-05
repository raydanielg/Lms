@if(!empty($webinars) and !$webinars->isEmpty())
    <div class="mt-20 row">

        @foreach($webinars as $webinar)
            <div class="col-lg-4 mt-20">
                @include('web.default.includes.webinar.grid-card',['webinar' => $webinar])
            </div>
        @endforeach
    </div>
@else
    @include(getTemplate() . '.includes.no-result',[
        'file_name' => 'webinar.png',
        'title' => trans('site.instructor_not_have_webinar'),
        'hint' => '',
    ])
@endif


@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.prerequisites') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddPrerequisites" data-webinar-id="{{ $webinar->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_prerequisites') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="prerequisitesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($webinar->prerequisites) and count($webinar->prerequisites))
                    <ul class="draggable-lists" data-order-table="prerequisites">
                        @foreach($webinar->prerequisites as $prerequisiteInfo)
                            @include('web.default.panel.webinar.create_includes.accordions.prerequisites',['webinar' => $webinar,'prerequisite' => $prerequisiteInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'comment.png',
                        'title' => trans('public.prerequisites_no_result'),
                        'hint' => trans('public.prerequisites_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('update.related_courses') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddRelatedCourses" data-webinar-id="{{ $webinar->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_related_courses') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="relatedCoursesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($webinar->relatedCourses) and count($webinar->relatedCourses))
                    <ul class="draggable-lists" data-order-table="relatedCourses">
                        @foreach($webinar->relatedCourses as $relatedCourseInfo)
                            @include('web.default.panel.webinar.create_includes.accordions.related_courses',['webinar' => $webinar,'relatedCourse' => $relatedCourseInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'comment.png',
                        'title' => trans('update.related_courses_no_result'),
                        'hint' => trans('update.related_courses_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>



<div id="newPrerequisiteForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.prerequisites',['webinar' => $webinar])
</div>

<div id="newRelatedCourseForm" class="d-none">
    @include('web.default.panel.webinar.create_includes.accordions.related_courses',['webinar' => $webinar])
</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush

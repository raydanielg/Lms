@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('public.faq') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="upcomingCourseAddFAQ" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_faq') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="faqsAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($upcomingCourse->faqs) and count($upcomingCourse->faqs))
                    <ul class="draggable-lists draggable-content-lists js-draggable-faq-lists" data-order-table="faqs" data-drag-class="js-draggable-faq-lists">
                        @foreach($upcomingCourse->faqs as $faqInfo)
                            @include('web.default.panel.upcoming_courses.create_includes.accordions.faq',['upcomingCourse' => $upcomingCourse, 'faq' => $faqInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'faq.png',
                        'title' => trans('public.faq_no_result'),
                        'hint' => trans('public.faq_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<div id="newFaqForm" class="d-none">
    @include('web.default.panel.upcoming_courses.create_includes.accordions.faq',['upcomingCourse' => $upcomingCourse])
</div>

@foreach(\App\Models\WebinarExtraDescription::$types as $upcomingCourseExtraDescriptionType)
    <section class="mt-50">
        <div class="">
            <h2 class="section-title after-line">{{ trans('update.'.$upcomingCourseExtraDescriptionType) }} ({{ trans('public.optional') }})</h2>
        </div>

        <button id="add_new_{{ $upcomingCourseExtraDescriptionType }}" data-webinar-id="{{ $upcomingCourse->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_'.$upcomingCourseExtraDescriptionType) }}</button>

        <div class="row mt-10">
            <div class="col-12">

                @php
                    $upcomingCourseExtraDescriptionValues = $upcomingCourse->extraDescriptions->where('type',$upcomingCourseExtraDescriptionType);
                @endphp

                <div class="accordion-content-wrapper mt-15" id="{{ $upcomingCourseExtraDescriptionType }}_accordion" role="tablist" aria-multiselectable="true">
                    @if(!empty($upcomingCourseExtraDescriptionValues) and count($upcomingCourseExtraDescriptionValues))
                        <ul class="draggable-content-lists draggable-lists-{{ $upcomingCourseExtraDescriptionType }}" data-drag-class="draggable-lists-{{ $upcomingCourseExtraDescriptionType }}" data-order-table="webinar_extra_descriptions_{{ $upcomingCourseExtraDescriptionType }}">
                            @foreach($upcomingCourseExtraDescriptionValues as $learningMaterialInfo)
                                @include('web.default.panel.upcoming_courses.create_includes.accordions.extra_description',
                                    [
                                        'upcomingCourse' => $upcomingCourse,
                                        'extraDescription' => $learningMaterialInfo,
                                        'extraDescriptionType' => $upcomingCourseExtraDescriptionType,
                                        'extraDescriptionParentAccordion' => $upcomingCourseExtraDescriptionType.'_accordion',
                                    ]
                                )
                            @endforeach
                        </ul>
                    @else
                        @include(getTemplate() . '.includes.no-result',[
                            'file_name' => 'faq.png',
                            'title' => trans("update.{$upcomingCourseExtraDescriptionType}_no_result"),
                            'hint' => trans("update.{$upcomingCourseExtraDescriptionType}_no_result_hint"),
                        ])
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div id="new_{{ $upcomingCourseExtraDescriptionType }}_html" class="d-none">
        @include('web.default.panel.upcoming_courses.create_includes.accordions.extra_description',
            [
                'upcomingCourse' => $upcomingCourse,
                'extraDescriptionType' => $upcomingCourseExtraDescriptionType,
                'extraDescriptionParentAccordion' => $upcomingCourseExtraDescriptionType.'_accordion',
            ]
        )
    </div>
@endforeach


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('update.related_courses') }} ({{ trans('public.optional') }})</h2>
    </div>

    <button id="webinarAddRelatedCourses" data-bundle-id="{{ $upcomingCourse->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_related_courses') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="relatedCoursesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($upcomingCourse->relatedCourses) and count($upcomingCourse->relatedCourses))
                    <ul class="draggable-lists" data-order-table="relatedCourses">
                        @foreach($upcomingCourse->relatedCourses as $relatedCourseInfo)
                            @include('web.default.panel.upcoming_courses.create_includes.accordions.related_courses',['upcomingCourse' => $upcomingCourse,'relatedCourse' => $relatedCourseInfo])
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

<div id="newRelatedCourseForm" class="d-none">
    @include('web.default.panel.upcoming_courses.create_includes.accordions.related_courses',['upcomingCourse' => $upcomingCourse])
</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>

    <script src="/assets/default/js/panel/webinar.min.js"></script>
@endpush

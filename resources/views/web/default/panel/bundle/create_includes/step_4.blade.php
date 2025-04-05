@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush


<section class="mt-50">
    <div class="">
        <h2 class="section-title after-line">{{ trans('product.courses') }}</h2>
    </div>

    <button id="addBundleWebinar" data-bundle-id="{{ $bundle->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_course') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="bundleWebinarsAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($bundle->bundleWebinars) and count($bundle->bundleWebinars))
                    <ul class="draggable-lists" data-order-table="bundle_webinars">
                        @foreach($bundle->bundleWebinars as $bundleWebinarRow)
                            @include('web.default.panel.bundle.create_includes.accordions.bundle-webinars',['bundle' => $bundle, 'bundleWebinar' => $bundleWebinarRow])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'comment.png',
                        'title' => trans('update.bundle_webinar_no_result'),
                        'hint' => trans('update.bundle_webinar_no_result_hint'),
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

    <button id="webinarAddRelatedCourses" data-bundle-id="{{ $bundle->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('update.add_related_courses') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="relatedCoursesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($bundle->relatedCourses) and count($bundle->relatedCourses))
                    <ul class="draggable-lists" data-order-table="relatedCourses">
                        @foreach($bundle->relatedCourses as $relatedCourseInfo)
                            @include('web.default.panel.bundle.create_includes.accordions.related_courses',['bundle' => $bundle,'relatedCourse' => $relatedCourseInfo])
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


<div id="newBundleWebinarForm" class="d-none">
    @include('web.default.panel.bundle.create_includes.accordions.bundle-webinars',['bundle' => $bundle])
</div>

<div id="newRelatedCourseForm" class="d-none">
    @include('web.default.panel.bundle.create_includes.accordions.related_courses',['bundle' => $bundle])
</div>

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush

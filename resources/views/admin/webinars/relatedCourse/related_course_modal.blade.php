<div class="" id="relatedCourseModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('update.add_new_related_courses') }}</h3>

    <div class="js-related-course-form" data-action="{{ getAdminPanelUrl("/relatedCourses/").(!empty($relatedCourse) ? $relatedCourse->id.'/update' : 'store') }}" >
        <input type="hidden" name="item_id" value="{{ $itemId }}">
        <input type="hidden" name="item_type" value="{{ $itemType }}">

        <div class="form-group mt-15">
            <label class="input-label d-block">{{ trans('update.select_related_courses') }}</label>
            <select name="course_id" class="js-ajax-course_id form-control related-course-select2" data-placeholder="{{ trans('update.search_courses') }}">
                @if(!empty($relatedCourse) and !empty($relatedCourse->course))
                    <option selected value="{{ $relatedCourse->course->id }}">{{ $relatedCourse->course->title .' - '. $relatedCourse->course->teacher->full_name }}</option>
                @endif
            </select>
            <div class="invalid-feedback"></div>
        </div>

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" id="saveRelateCourse" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </div>
</div>

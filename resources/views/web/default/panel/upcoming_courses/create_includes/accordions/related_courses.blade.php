<li data-id="{{ !empty($relatedCourse) ? $relatedCourse->id :'' }}" class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="relatedCourse_{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}">
        <div class="font-weight-bold text-dark-blue" href="#collapseRelatedCourse{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" aria-controls="collapseRelatedCourse{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" data-parent="#relatedCoursesAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span>{{ (!empty($relatedCourse) and !empty($relatedCourse->course)) ? $relatedCourse->course->title .' - '. $relatedCourse->course->teacher->full_name : trans('update.add_new_related_courses') }}</span>
        </div>

        <div class="d-flex align-items-center">
            {{--<i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>--}}

            @if(!empty($relatedCourse))
                <div class="btn-group dropdown table-actions mr-15">
                    <button type="button" class="btn-transparent dropdown-toggle d-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="more-vertical" height="20"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="/panel/relatedCourses/{{ $relatedCourse->id }}/delete" class="delete-action btn btn-sm btn-transparent">{{ trans('public.delete') }}</a>
                    </div>
                </div>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseRelatedCourse{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" aria-controls="collapseRelatedCourse{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" data-parent="#relatedCoursesAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseRelatedCourse{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" aria-labelledby="relatedCourse_{{ !empty($relatedCourse) ? $relatedCourse->id :'record' }}" class=" collapse @if(empty($relatedCourse)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="related-course-form" data-action="/panel/relatedCourses/{{ !empty($relatedCourse) ? $relatedCourse->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($relatedCourse) ? $relatedCourse->id : 'new' }}][item_id]" value="{{ $upcomingCourse->id }}">
                <input type="hidden" name="ajax[{{ !empty($relatedCourse) ? $relatedCourse->id : 'new' }}][item_type]" value="upcomingCourse">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group mt-15">
                            <label class="input-label d-block">{{ trans('update.select_related_courses') }}</label>
                            <select name="ajax[{{ !empty($relatedCourse) ? $relatedCourse->id : 'new' }}][course_id]" class="js-ajax-course_id form-control @if(!empty($relatedCourse)) panel-search-webinar-select2 @else relatedCourses-select2 @endif" data-placeholder="{{ trans('update.search_courses') }}">
                                @if(!empty($relatedCourse) and !empty($relatedCourse->course))
                                    <option selected value="{{ $relatedCourse->course->id }}">{{ $relatedCourse->course->title .' - '. $relatedCourse->course->teacher->full_name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-related-course btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($relatedCourse))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>

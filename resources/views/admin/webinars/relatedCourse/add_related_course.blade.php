<section class="mt-30">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="section-title after-line">{{ trans('update.related_courses') }}</h2>
        <button id="addRelatedCourse" type="button" class="btn btn-primary btn-sm mt-3" data-path="{{ getAdminPanelUrl("/relatedCourses/get-form") }}?item={{ $relatedCourseItemId }}&item_type={{ $relatedCourseItemType }}">{{ trans('update.add_related_courses') }}</button>
    </div>

    <div class="row mt-10">
        <div class="col-12">
            @if(!empty($relatedCourses) and !$relatedCourses->isEmpty())
                <div class="table-responsive">
                    <table class="table table-striped text-center font-14">

                        <tr>
                            <th>{{ trans('public.title') }}</th>
                            <th class="text-left">{{ trans('public.instructor') }}</th>
                            <th>{{ trans('public.price') }}</th>
                            <th>{{ trans('public.publish_date') }}</th>
                            <th></th>
                        </tr>

                        @foreach($relatedCourses as $relatedCourse)
                            @if(!empty($relatedCourse->course->title))
                                <tr>
                                    <th>{{ $relatedCourse->course->title }}</th>
                                    <td class="text-left">{{ $relatedCourse->course->teacher->full_name }}</td>
                                    <td>{{  handlePrice($relatedCourse->course->price) }}</td>
                                    <td>{{ dateTimeFormat($relatedCourse->course->created_at,'j F Y | H:i') }}</td>

                                    <td>
                                        <button type="button" class="js-edit-related-course btn-transparent text-primary mt-1" data-path="{{ getAdminPanelUrl("/relatedCourses/{$relatedCourse->id}/edit") }}?item={{ $relatedCourseItemId }}&item_type={{ $relatedCourseItemType }}" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/relatedCourses/'. $relatedCourse->id .'/delete', 'btnClass' => ' mt-1'])
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </table>
                </div>
            @else
                @include('admin.includes.no-result',[
                    'file_name' => 'comment.png',
                    'title' => trans('update.related_courses_no_result'),
                    'hint' => trans('update.related_courses_no_result_hint'),
                ])
            @endif
        </div>
    </div>
</section>

@push('scripts_bottom')
    <!-- Modal -->
    <script src="/assets/default/js/admin/related_courses.min.js"></script>
@endpush

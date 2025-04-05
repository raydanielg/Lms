<section class="mt-30">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="section-title after-line">{{ trans('public.faq') }}</h2>
        <button id="upcomingCourseAddFAQ" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_faq') }}</button>
    </div>

    <div class="row mt-10">
        <div class="col-12">
            @if(!empty($upcomingCourse->faqs) and !$upcomingCourse->faqs->isEmpty())
                <div class="table-responsive">
                    <table class="table table-striped text-center font-14">

                        <tr>
                            <th>{{ trans('public.title') }}</th>
                            <th>{{ trans('public.answer') }}</th>
                            <th></th>
                        </tr>

                        @foreach($upcomingCourse->faqs as $faq)
                            <tr>
                                <th>{{ $faq->title }}</th>
                                <td>
                                    <button type="button" class="js-get-faq-description btn btn-sm btn-gray200">{{ trans('public.view') }}</button>
                                    <input type="hidden" value="{{ $faq->answer }}"/>
                                </td>

                                <td class="text-right">
                                    <button type="button" data-faq-id="{{ $faq->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-faq btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl('/faqs/'. $faq->id .'/delete'), 'btnClass' => ' mt-1'])
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            @else
                @include('admin.includes.no-result',[
                    'file_name' => 'faq.png',
                    'title' => trans('public.faq_no_result'),
                    'hint' => trans('public.faq_no_result_hint'),
                ])
            @endif
        </div>
    </div>
</section>

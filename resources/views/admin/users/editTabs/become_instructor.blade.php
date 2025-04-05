

<div class="tab-pane mt-3 fade active show" id="become_instructor" role="tabpanel" aria-labelledby="become_instructor-tab">
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td class="text-left">{{ trans('admin/main.role') }}</td>
                    <td class="text-left">{{ trans('site.extra_information') }}:</td>
                    <td class="text-center">{{ trans('public.certificate_and_documents') }}</td>
                </tr>

                <tr>
                    <td class="text-left">{{ $becomeInstructor->role }}</td>
                    <td width="50%" class="text-left">{{ $becomeInstructor->description ?? '-' }}</td>
                    <td class="text-center">
                        @if(!empty($becomeInstructor->certificate))
                            <a href="{{ (strpos($becomeInstructor->certificate,'http') != false) ? $becomeInstructor->certificate : url($becomeInstructor->certificate) }}" target="_blank" class="btn btn-sm btn-success">{{ trans('admin/main.show') }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            @if(!empty($becomeInstructorFormFieldValues) and count($becomeInstructorFormFieldValues))
                <h3 class="section-title after-line mt-4">{{ trans('update.extra_form') }}</h3>

                @foreach($becomeInstructorFormFieldValues as $becomeInstructorFormFieldTitle => $becomeInstructorFormFieldValue)
                    <div class="mt-3">
                        <label>{{ $becomeInstructorFormFieldTitle }}:</label>
                        <p class="text-muted white-space-pre-wrap">{{ $becomeInstructorFormFieldValue }}</p>
                    </div>
                @endforeach
            @endif


            @include('admin.includes.delete_button',[
                             'url' => getAdminPanelUrl().'/users/become_instructors/'. $becomeInstructor->id .'/reject',
                             'btnClass' => 'mt-3 btn btn-danger',
                             'btnText' => trans('admin/main.reject_request'),
                             'hideDefaultClass' => true
                             ])

            @include('admin.includes.delete_button',[
                             'url' => getAdminPanelUrl("/users/{$user->id}/acceptRequestToInstructor"),
                             'btnClass' => 'btn btn-success ml-1 mt-3',
                             'btnText' => trans('admin/main.accept_request'),
                             'hideDefaultClass' => true
                             ])
        </div>
    </div>
</div>

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush


<div class="tab-pane mt-3 fade" id="extra_information" role="tabpanel" aria-labelledby="extra_information-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/updateFormFields' }}" method="Post">
                {{ csrf_field() }}

                @if(!empty($formFieldsHtml))
                    {!! $formFieldsHtml !!}
                @endif

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/js/parts/forms.min.js"></script>
@endpush

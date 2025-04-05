@extends('web.default.forms.layout')

@section('formContent')

    @if(!empty($form->end_date))
        <div class="d-flex align-items-center mb-40 rounded-lg border border-gray200 p-15">
            <div class="size-40 d-flex-center rounded-circle bg-gray200">
                <i data-feather="calendar" class="text-gray" width="20" height="20"></i>
            </div>
            <div class="ml-5">
                <h4 class="font-14 font-weight-bold text-gray">{{ trans('update.notice') }}</h4>
                <p class="font-12 text-gray">{{ trans('update.this_form_will_be_expired_on_date',['date' => dateTimeFormat($form->end_date, 'j M Y')]) }}</p>
            </div>
        </div>
    @endif

    <div class="d-flex-center flex-column">
        <div class="">
            <img src="{{ $form->image }}" alt="{{ $form->heading_title }}" class="img-fluid">
        </div>

        <h3 class="font-24 mt-30">{{ $form->heading_title }}</h3>
    </div>

    <div class="forms-body-welcome-message white-space-pre-wrap mt-15 font-14 text-gray">{!! $form->description !!}</div>

    {{-- Inputs --}}
    <form action="/forms/{{ $form->url }}/store" method="post" class="mt-30">
        {{ csrf_field() }}

        @include('web.default.forms.handle_field',['fields' => $form->fields])

        <div class="d-flex align-items-center justify-content-end mt-30">
            <button type="button" class="js-clear-form btn btn-danger mr-10">{{ trans('update.clear_form') }}</button>

            <button type="submit" class="btn btn-primary">{{ trans('update.submit_form') }}</button>
        </div>
    </form>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/form_submissions_details.min.js"></script>
@endpush

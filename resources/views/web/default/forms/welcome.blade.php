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
            <img src="{{ $form->welcome_message_image }}" alt="{{ $form->welcome_message_title }}" class="img-fluid">
        </div>

        <h3 class="font-24 mt-30">{{ $form->welcome_message_title }}</h3>
    </div>

    <div class="forms-body-welcome-message white-space-pre-wrap mt-15 font-14 text-gray">{{ $form->welcome_message_description }}</div>

    <div class="d-flex-center mt-20">
        <a href="/forms/{{ $form->url }}?fields=1" class="btn btn-primary">{{ trans('update.fill_out_the_form') }}</a>
    </div>
@endsection

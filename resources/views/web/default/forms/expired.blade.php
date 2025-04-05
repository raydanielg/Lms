@extends('web.default.forms.layout')

@section('formContent')

    <div class="d-flex-center flex-column">
        <div class="forms-body-welcome-image">
            <img src="/assets/default/img/forms/please_login.svg" alt="{{ trans("update.access_denied") }}" class="h-100">
        </div>

        <h3 class="font-24 mt-30">{{ trans("update.access_denied") }}</h3>

        <div class="forms-body-welcome-message white-space-pre-wrap mt-10 font-14 text-gray">{{ trans("update.this_form_expired_on_date",['date' => dateTimeFormat($form->end_date, 'j M Y')]) }}</div>

        <a href="/" class="btn btn-primary mt-20">{{ trans('update.back_to_home') }}</a>
    </div>

@endsection

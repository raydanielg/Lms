@extends('web.default.forms.layout')

@section('formContent')

    <div class="d-flex-center flex-column">
        <div class="forms-body-welcome-image">
            <img src="/assets/default/img/forms/already_submitted.svg" alt="{{ trans("update.already_submitted") }}" class="h-100">
        </div>

        <h3 class="font-24 mt-30">{{ trans("update.already_submitted") }}</h3>
        <div class="forms-body-welcome-message white-space-pre-wrap mt-10 font-14 text-gray">{{ trans("update.you_have_submitted_this_form_already_and_you_can_not_fill_it_in_again...") }}</div>

        <a href="/" class="btn btn-primary mt-20">{{ trans('update.back_to_home') }}</a>
    </div>

@endsection

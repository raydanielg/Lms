@extends('web.default.forms.layout')

@section('formContent')

    <div class="d-flex-center flex-column">
        <div class="">
            <img src="{{ $form->tank_you_message_image }}" alt="{{ $form->tank_you_message_title }}" class="img-fluid">
        </div>

        <h3 class="font-24 mt-30">{{ $form->tank_you_message_title }}</h3>
    </div>

    <div class="forms-body-welcome-message white-space-pre-wrap mt-15 font-14 text-gray">{{ $form->tank_you_message_description }}</div>

    <div class="d-flex-center mt-20">
        <a href="/" class="btn btn-primary">{{ trans('update.back_to_home') }}</a>
    </div>
@endsection

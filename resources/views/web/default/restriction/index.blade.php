@extends('web.default.layouts.app', ['appFooter' => false, 'appHeader' => false, 'justMobileApp' => true])

@php
    $restrictionSettings = getRestrictionSettings();
@endphp

@section('content')
    <section class="maintenance-section mt-25 mb-50 position-relative">
        <div class="container">
            <div class="d-flex-center flex-column">
                @if(!empty($restrictionSettings['image']))
                    <div class="maintenance-image">
                        <img src="{{ $restrictionSettings['image'] }}" alt="{{ $restrictionSettings['title'] }}" class="img-cover">
                    </div>
                @endif

                @if(!empty($restrictionSettings['title']))
                    <h1 class="font-36 font-weight-bold mt-10">{{ $restrictionSettings['title'] }}</h1>
                @endif

                @if(!empty($restrictionSettings['description']))
                    <p class="font-14 font-weight-500 text-gray mt-15">{!! nl2br($restrictionSettings['description']) !!}</p>
                @endif
            </div>
        </div>
    </section>

@endsection

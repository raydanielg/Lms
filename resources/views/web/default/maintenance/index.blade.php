@extends('web.default.layouts.app', ['appFooter' => false, 'appHeader' => false, 'justMobileApp' => true])

@php
    $maintenanceSettings = getMaintenanceSettings();
    $endDate = !empty($maintenanceSettings['end_date']) ? $maintenanceSettings['end_date'] : null;

    $remainingTimes = null;

    if (!empty($endDate) and is_numeric($endDate)) {
        $remainingTimes = time2string($endDate -  time());
    }
@endphp

@section('content')
    <section class="maintenance-section mt-25 mb-50 position-relative">
        <div class="container">
            <div class="d-flex-center flex-column">
                @if(!empty($maintenanceSettings['image']))
                    <div class="maintenance-image">
                        <img src="{{ $maintenanceSettings['image'] }}" alt="{{ $maintenanceSettings['title'] }}" class="img-cover">
                    </div>
                @endif

                @if(!empty($maintenanceSettings['title']))
                    <h1 class="font-36 font-weight-bold mt-10">{{ $maintenanceSettings['title'] }}</h1>
                @endif

                @if(!empty($maintenanceSettings['description']))
                    <p class="font-14 font-weight-500 text-gray mt-15">{!! nl2br($maintenanceSettings['description']) !!}</p>
                @endif

                @if(!empty($remainingTimes))
                    <div id="maintenanceCountDown" class="d-flex time-counter-down mt-15"
                         data-day="{{ $remainingTimes['day'] }}"
                         data-hour="{{ $remainingTimes['hour'] }}"
                         data-minute="{{ $remainingTimes['minute'] }}"
                         data-second="{{ $remainingTimes['second'] }}">

                        <div class="d-flex align-items-center flex-column mr-10">
                            <span class="bg-gray300 rounded p-10 font-16 font-weight-bold text-dark time-item days"></span>
                            <span class="font-12 mt-1 text-gray">{{ trans('public.day') }}</span>
                        </div>
                        <div class="d-flex align-items-center flex-column mr-10">
                            <span class="bg-gray300 rounded p-10 font-16 font-weight-bold text-dark time-item hours"></span>
                            <span class="font-12 mt-1 text-gray">{{ trans('public.hr') }}</span>
                        </div>
                        <div class="d-flex align-items-center flex-column mr-10">
                            <span class="bg-gray300 rounded p-10 font-16 font-weight-bold text-dark time-item minutes"></span>
                            <span class="font-12 mt-1 text-gray">{{ trans('public.min') }}</span>
                        </div>
                        <div class="d-flex align-items-center flex-column">
                            <span class="bg-gray300 rounded p-10 font-16 font-weight-bold text-dark time-item seconds"></span>
                            <span class="font-12 mt-1 text-gray">{{ trans('public.sec') }}</span>
                        </div>
                    </div>
                @endif

                @if(!empty($maintenanceSettings['maintenance_button']) and !empty($maintenanceSettings['maintenance_button']['title']) and !empty($maintenanceSettings['maintenance_button']['link']))
                    <a href="{{ $maintenanceSettings['maintenance_button']['link'] }}" class="btn btn-primary mt-20">{{ $maintenanceSettings['maintenance_button']['title'] }}</a>
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/time-counter-down.min.js"></script>
    <script src="/assets/default/js/parts/maintenance.min.js"></script>
@endpush

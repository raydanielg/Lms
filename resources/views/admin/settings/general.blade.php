@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.main_general') }} {{ trans('admin/main.settings') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/settings">{{ trans('admin/main.settings') }}</a></div>
                <div class="breadcrumb-item ">{{ trans('admin/main.main_general') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link @if(empty($social)) active @endif" id="basic-tab" data-toggle="tab" href="#basic" role="tab" aria-controls="basic" aria-selected="true">{{ trans('admin/main.basic') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if(!empty($social)) active @endif" id="socials-tab" data-toggle="tab" href="#socials" role="tab" aria-controls="socials" aria-selected="true">{{ trans('admin/main.socials') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="features-tab" data-toggle="tab" href="#features" role="tab" aria-controls="features" aria-selected="true">{{ trans('update.features') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="reminders-tab" data-toggle="tab" href="#reminders" role="tab" aria-controls="reminders" aria-selected="true">{{ trans('update.reminders') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab" aria-controls="security" aria-selected="true">{{ trans('update.security') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="general_options-tab" data-toggle="tab" href="#general_options" role="tab" aria-controls="general_options" aria-selected="true">{{ trans('update.options') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="sms_channels-tab" data-toggle="tab" href="#sms_channels" role="tab" aria-controls="sms_channels" aria-selected="true">{{ trans('update.sms_channels') }}</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                @include('admin.settings.general.basic',['itemValue' => (!empty($settings) and !empty($settings['general'])) ? $settings['general']->value : ''])
                                @include('admin.settings.general.socials',['itemValue' => (!empty($settings) and !empty($settings['socials'])) ? $settings['socials']->value : ''])
                                @include('admin.settings.general.features',['itemValue' => (!empty($settings) and !empty($settings['features'])) ? $settings['features']->value : ''])
                                @include('admin.settings.general.reminders',['itemValue' => (!empty($settings) and !empty($settings['reminders'])) ? $settings['reminders']->value : ''])
                                @include('admin.settings.general.security',['itemValue' => (!empty($settings) and !empty($settings['security'])) ? $settings['security']->value : ''])
                                @include('admin.settings.general.options',['itemValue' => (!empty($settings) and !empty($settings['general_options'])) ? $settings['general_options']->value : ''])
                                @include('admin.settings.general.sms_channels',['itemValue' => (!empty($settings) and !empty($settings['sms_channels'])) ? $settings['sms_channels']->value : ''])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

    <script src="/assets/default/js/admin/settings/general.min.js"></script>
@endpush

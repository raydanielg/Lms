@extends('admin.layouts.app')

@push('styles_top')

    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.financial_settings') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/settings">{{ trans('admin/main.settings') }}</a></div>
                <div class="breadcrumb-item ">{{ trans('admin/main.financial') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link  @if(empty(request()->get('tab'))) active @endif" id="basic-tab" data-toggle="tab" href="#basic" role="tab" aria-controls="basic" aria-selected="true">{{ trans('admin/main.basic') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if(request()->get('tab') == "offline_banks") active @endif" id="offline_banks-tab" href="{{ getAdminPanelUrl("/settings/financial?tab=offline_banks") }}">{{ trans('admin/main.offline_banks_credits') }}</a>
                                </li>

                                @can('admin_payment_channel')
                                    <li class="nav-item">
                                        <a class="nav-link @if(request()->get('tab') == "payment_channels") active @endif" id="payment_channels-tab" data-toggle="tab" href="#payment_channels" role="tab" aria-controls="payment_channels" aria-selected="true">{{ trans('admin/main.payment_channels') }}</a>
                                    </li>
                                @endcan

                                <li class="nav-item">
                                    <a class="nav-link " id="referral-tab" data-toggle="tab" href="#referral" role="tab" aria-controls="referral" aria-selected="true">{{ trans('admin/main.referral') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if(request()->get('tab') == "currency") active @endif" id="currency-tab" href="{{ getAdminPanelUrl("/settings/financial?tab=currency") }}">{{ trans('admin/main.currency') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if(request()->get('tab') == "user_banks") active @endif" id="user_banks-tab" href="{{ getAdminPanelUrl("/settings/financial?tab=user_banks") }}">{{ trans('update.user_banks') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if(request()->get('tab') == "commissions") active @endif" id="commissions-tab" href="{{ getAdminPanelUrl("/settings/financial?tab=commissions") }}">{{ trans('update.commissions') }}</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                @include('admin.settings.financial.basic',['itemValue' => (!empty($settings) and !empty($settings['financial'])) ? $settings['financial']->value : ''])

                                @if(request()->get('tab') == "offline_banks")
                                    @include('admin.settings.financial.offline_banks.index',['itemValue' => (!empty($settings) and !empty($settings['offline_banks'])) ? $settings['offline_banks']->value : ''])
                                @endif

                                @can('admin_payment_channel')
                                    @include('admin.settings.financial.payment_channel.lists')
                                @endcan

                                @include('admin.settings.financial.referral',['itemValue' => (!empty($settings) and !empty($settings['referral'])) ? $settings['referral']->value : ''])

                                @if(request()->get('tab') == "currency")
                                    @include('admin.settings.financial.currency',['itemValue' => (!empty($settings) and !empty($settings[\App\Models\Setting::$currencySettingsName])) ? $settings[\App\Models\Setting::$currencySettingsName]->value : ''])
                                @endif

                                @if(request()->get('tab') == "user_banks")
                                    @include('admin.settings.financial.user_banks.index',['itemValue' => (!empty($settings) and !empty($settings['user_banks'])) ? $settings['user_banks']->value : ''])
                                @endif

                                @if(request()->get('tab') == "commissions")
                                    @include('admin.settings.financial.commission',['itemValue' => (!empty($settings) and !empty($settings[\App\Models\Setting::$commissionSettingsName])) ? $settings[\App\Models\Setting::$commissionSettingsName]->value : ''])
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush

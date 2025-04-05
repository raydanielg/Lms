@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <section class="section">
    <div class="section-header">
            <h1>{{ trans('update.update_app') }} (v1.9.7)</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/settings">{{ trans('admin/main.settings') }}</a></div>
                <div class="breadcrumb-item">{{ trans('update.update_app') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="basic-tab" data-toggle="tab" href="#basic"
                                       role="tab" aria-controls="basic"
                                       aria-selected="true">{{ trans('update.update_core') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="database-tab" data-toggle="tab" href="#database"
                                       role="tab" aria-controls="database"
                                       aria-selected="true">{{ trans('update.update_database') }}</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent2">

                                @include('admin.settings.update_app.basic_tab')

                                @include('admin.settings.update_app.database_tab')

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h4>{{trans('admin/main.hints')}}</h4></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{ trans('update.update_app_hint_title_1') }}</div>
                        <div class=" text-small font-600-bold mb-2">{{ trans('update.update_app_hint_description_1') }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{ trans('update.update_app_hint_title_2') }}</div>
                        <div class=" text-small font-600-bold mb-2">{{ trans('update.update_app_hint_description_2') }}</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/js/admin/settings/update_app.min.js"></script>
@endpush

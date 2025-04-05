@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ !empty($group) ? trans('admin/main.edit') : '' }} {{ trans('admin/main.user_group') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.new_user_group') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            @if(!empty($group))
                                <ul class="nav nav-pills" id="myTab3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">{{ trans('admin/main.main_general') }}</a>
                                    </li>

                                    @can('admin_update_group_registration_package')
                                        <li class="nav-item">
                                            <a class="nav-link" id="registrationPackage-tab" data-toggle="tab" href="#registrationPackage" role="tab" aria-controls="registrationPackage" aria-selected="true">{{ trans('update.registration_package') }}</a>
                                        </li>
                                    @endcan
                                </ul>
                            @endif

                            <div class="tab-content" id="myTabContent2">
                                @include('admin.users.groups.tabs.general')

                                @if(!empty($group))
                                    @can('admin_update_group_registration_package')
                                        @include('admin.users.groups.tabs.registration_package')
                                    @endcan
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

@endpush
